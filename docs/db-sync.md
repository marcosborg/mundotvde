# Sincronizacao de bases (sandbox <-> production)

## Visao geral
- Comando `php artisan db:sync-external` cria um dump da base externa (sandbox ou production) e substitui toda a base interna de destino.
- Requer binarios `mysqldump` e `mysql` no PATH (clientes MySQL).
- O comando remove todas as tabelas e views do destino antes de importar.

## Variaveis de ambiente
- `DB_SANDBOX_*`: host/port/database/username/password da base local/sandbox.
- `DB_PRODUCTION_*`: credenciais da base na cloud/producao.
- `DB_SYNC_SOURCE`: perfil usado por omissao ao executar o comando sem argumento (`sandbox` ou `production`).
- `DB_SYNC_TARGET_CONNECTION`: ligacao de destino usada para gravar (ex.: `mysql_sandbox` ou outro connection).
- `DB_CONNECTION`: ligacao que a aplicacao usa em runtime; defina `mysql_sandbox` para ler da base local ou `mysql_production` para ler da cloud.
- `DB_SYNC_MYSQLDUMP_BIN` e `DB_SYNC_MYSQL_BIN`: caminhos/nomes dos binarios `mysqldump` e `mysql` caso nao estejam no PATH (ex.: `C:\\xampp\\mysql\\bin\\mysqldump.exe`).

## Exemplo de configuracao (.env)
```env
DB_CONNECTION=mysql_sandbox        # runtime (app le da base local)

DB_SYNC_SOURCE=sandbox
DB_SYNC_TARGET_CONNECTION=mysql_sandbox

DB_SANDBOX_HOST=127.0.0.1
DB_SANDBOX_PORT=3306
DB_SANDBOX_DATABASE=mundotvde
DB_SANDBOX_USERNAME=root
DB_SANDBOX_PASSWORD=

DB_PRODUCTION_HOST=94.46.22.206
DB_PRODUCTION_PORT=3306
DB_PRODUCTION_DATABASE=opiniaoe_db
DB_PRODUCTION_USERNAME=opiniaoe_user
DB_PRODUCTION_PASSWORD=W^zVdVD?{(,a

# Opcional: caminhos dos binarios MySQL
DB_SYNC_MYSQLDUMP_BIN=mysqldump
DB_SYNC_MYSQL_BIN=mysql
```
Para a app ler da cloud, troque `DB_CONNECTION` para `mysql_production`.

## Como usar
- Sandbox -> local: `php artisan db:sync-external sandbox`
- Producao -> local: `php artisan db:sync-external production --force`
- Sem `--force` o comando pede confirmacao antes de apagar/importar.
- Se o config estiver em cache, limpe com `php artisan config:clear` apos editar o `.env`.
- O comando garante que as bases de origem e destino existem (tenta `CREATE DATABASE IF NOT EXISTS ...`) antes de fazer dump/import; precisa de permissao para criar DB nesses servidores.

## Notas de seguranca
- Verifique `DB_SYNC_TARGET_CONNECTION` antes de executar para nao sobrescrever a base errada.
- O comando desliga `FOREIGN_KEY_CHECKS` durante o drop/import para evitar falhas de integridade.
