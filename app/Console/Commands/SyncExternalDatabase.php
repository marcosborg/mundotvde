<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

class SyncExternalDatabase extends Command
{
    protected $signature = 'db:sync-external
        {source? : sandbox ou production}
        {--force : Ignora a confirmação antes de substituir a base interna}';

    protected $description = 'Copia a base externa (sandbox/production) para a base interna configurada.';

    public function handle(): int
    {
        $source = strtolower($this->argument('source') ?? config('db-sync.default_source', 'sandbox'));
        $sources = config('db-sync.sources', []);

        if (!array_key_exists($source, $sources)) {
            $this->error("Fonte '{$source}' desconhecida. Use sandbox ou production.");
            return Command::FAILURE;
        }

        $sourceConfig = $this->normalizeConnection($sources[$source]);
        if (!$this->hasRequiredValues($sourceConfig)) {
            $this->error("Configuração incompleta para a fonte '{$source}'. Confira o .env.");
            return Command::FAILURE;
        }

        $targetConnection = config('db-sync.target_connection', config('database.default'));
        $targetRawConfig = config("database.connections.{$targetConnection}");

        if (!$targetRawConfig) {
            $this->error("Ligação de destino '{$targetConnection}' não encontrada.");
            return Command::FAILURE;
        }

        $targetConfig = $this->normalizeConnection($targetRawConfig);
        if (!$this->hasRequiredValues($targetConfig)) {
            $this->error("Configuração incompleta para a base de destino '{$targetConnection}'.");
            return Command::FAILURE;
        }

        $dumpBinary = config('db-sync.dump_binary', 'mysqldump');
        $clientBinary = config('db-sync.client_binary', 'mysql');
        $this->assertBinaryAvailable($dumpBinary);
        $this->assertBinaryAvailable($clientBinary);

        $sourceLabel = "{$sourceConfig['host']}:{$sourceConfig['port']}/{$sourceConfig['database']}";
        $targetLabel = "{$targetConfig['host']}:{$targetConfig['port']}/{$targetConfig['database']} ({$targetConnection})";

        $this->line("A garantir que a base de origem existe ({$sourceLabel})...");
        $this->ensureDatabaseExists($sourceConfig, $clientBinary, "origem {$sourceLabel}");

        $this->line("A garantir que a base de destino existe ({$targetLabel})...");
        $this->ensureDatabaseExists($targetConfig, $clientBinary, "destino {$targetLabel}");

        if (!$this->option('force')) {
            $this->warn("Isto vai substituir TODOS os dados de {$targetLabel} pelo conteúdo de {$sourceLabel}.");
            if (!$this->confirm('Deseja continuar?')) {
                $this->info('Sincronização cancelada.');
                return Command::SUCCESS;
            }
        }

        try {
            $this->line("Criando dump a partir de {$sourceLabel}...");
            $dump = $this->dumpDatabase($sourceConfig, $dumpBinary);

            $this->line("A limpar a base de destino {$targetLabel}...");
            $this->dropAllTables($targetConnection);

            $this->line("A importar o dump para {$targetLabel}...");
            $this->importDump($targetConfig, $dump, $clientBinary);
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            return Command::FAILURE;
        }

        $this->info('Base de dados sincronizada com sucesso.');

        return Command::SUCCESS;
    }

    private function normalizeConnection(array $config): array
    {
        return [
            'host' => $config['host'] ?? '127.0.0.1',
            'port' => (string) ($config['port'] ?? 3306),
            'database' => $config['database'] ?? null,
            'username' => $config['username'] ?? null,
            'password' => $config['password'] ?? '',
        ];
    }

    private function hasRequiredValues(array $config): bool
    {
        return !empty($config['host'])
            && !empty($config['port'])
            && !empty($config['database'])
            && !empty($config['username']);
    }

    private function dumpDatabase(array $config, string $dumpBinary): string
    {
        $process = new Process([
            $dumpBinary,
            '--host=' . $config['host'],
            '--port=' . $config['port'],
            '--user=' . $config['username'],
            '--single-transaction',
            '--quick',
            '--add-drop-table',
            '--routines',
            '--events',
            '--triggers',
            $config['database'],
        ]);

        $process->setTimeout(null);
        $process->run(null, $this->mysqlEnv($config['password']));

        if (!$process->isSuccessful()) {
            throw new RuntimeException('Falha ao criar o dump externo: ' . $process->getErrorOutput());
        }

        return "SET FOREIGN_KEY_CHECKS=0;\n" . $process->getOutput() . "\nSET FOREIGN_KEY_CHECKS=1;\n";
    }

    private function importDump(array $config, string $dump, string $clientBinary): void
    {
        $process = new Process([
            $clientBinary,
            '--host=' . $config['host'],
            '--port=' . $config['port'],
            '--user=' . $config['username'],
            $config['database'],
        ]);

        $process->setTimeout(null);
        $process->setInput($dump);
        $process->run(null, $this->mysqlEnv($config['password']));

        if (!$process->isSuccessful()) {
            throw new RuntimeException('Falha ao importar o dump para a base interna: ' . $process->getErrorOutput());
        }
    }

    private function dropAllTables(string $connection): void
    {
        $db = DB::connection($connection);

        $tables = $this->fetchTables($db, 'BASE TABLE');
        $views = $this->fetchTables($db, 'VIEW');

        $db->statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($views as $view) {
            $db->statement('DROP VIEW IF EXISTS ' . $this->quoteIdentifier($view));
        }

        foreach ($tables as $table) {
            $db->statement('DROP TABLE IF EXISTS ' . $this->quoteIdentifier($table));
        }

        $db->statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function fetchTables($db, string $type): array
    {
        $result = $db->select('SHOW FULL TABLES WHERE Table_type = ?', [$type]);

        return array_values(array_filter(array_map(function ($row) {
            return array_values((array) $row)[0] ?? null;
        }, $result)));
    }

    private function assertBinaryAvailable(string $binary): void
    {
        $process = new Process([$binary, '--version']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException("O binário '{$binary}' não está disponível no PATH.");
        }
    }

    private function mysqlEnv(?string $password): array
    {
        return ($password ?? '') === '' ? [] : ['MYSQL_PWD' => (string) $password];
    }

    private function quoteIdentifier(string $value): string
    {
        return '`' . str_replace('`', '``', $value) . '`';
    }

    private function ensureDatabaseExists(array $config, string $clientBinary, string $label): void
    {
        $dbName = $config['database'];

        $process = new Process([
            $clientBinary,
            '--host=' . $config['host'],
            '--port=' . $config['port'],
            '--user=' . $config['username'],
            '-e',
            'CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '``', $dbName) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;',
        ]);

        $process->setTimeout(30);
        $process->run(null, $this->mysqlEnv($config['password']));

        if (!$process->isSuccessful()) {
            throw new RuntimeException("Não foi possível garantir a existência da base ({$label}): " . $process->getErrorOutput());
        }
    }
}
