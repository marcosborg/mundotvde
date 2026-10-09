<?php

namespace App\Services;

use App\Models\{Driver, DriverMessageTemplate, DriverContractVersion};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class DriverOnboarding
{
    public function values(Driver $driver) : array
    {
        return ['nome' => $driver->name, 'email' => $driver->user->email ?? $driver->email, 'telefone' => $driver->phone, 'nif' => $driver->driver_vat, 'morada' => $driver->address, 'codigo_postal' => $driver->zip, 'localidade' => $driver->city, 'documento_identificacao' => $driver->citizen_card, 'carta_conducao' => $driver->driver_license, 'certificado_tvde' => $driver->drivers_certificate, 'matricula' => $driver->license_plate, 'marca' => $driver->brand, 'modelo' => $driver->model, 'data' => now()->format('d/m/Y'), 'login_url' => route('login')];
    }
    public function render(string $text, array $values) : string
    {
        $replacements = [];
        foreach ($values as $key => $value) {
            $replacements['{' . $key . '}'] = (string) $value;
        }
        preg_match_all('/\\{([a-z_]+)\\}/', $text, $matches);
        foreach ($matches[1] as $key) {
            if (!array_key_exists($key, $values) || trim((string) $values[$key]) === '') {
                throw ValidationException::withMessages(['template' => 'Preencha o campo necessário para o contrato: ' . $key]);
            }
        }
        return strtr($text, $replacements);
    }
    public function generate(Driver $driver, string $type, int $actor) : DriverContractVersion
    {
        return DB::transaction(function () use($driver, $type, $actor) {
            $driver = Driver::whereKey($driver->id)->lockForUpdate()->firstOrFail();
            if (!$driver->user) {
                throw ValidationException::withMessages(['contract' => 'Associe primeiro uma conta ao condutor.']);
            }
            $template = DriverMessageTemplate::where('key', $type)->where('active', true)->first();
            if (!$template || !trim($template->body ?? '')) {
                throw ValidationException::withMessages(['contract' => 'Este modelo ainda não está pronto. Introduza e ative o texto aprovado na área de templates.']);
            }
            $snapshot = $this->values($driver);
            $body = $this->render($template->body, $snapshot);
            $version = 1 + (int) DriverContractVersion::where('driver_id', $driver->id)->max('version');
            DriverContractVersion::where('driver_id', $driver->id)->current()->update(['superseded_at' => now()]);
            return DriverContractVersion::create(['driver_id' => $driver->id, 'user_id' => $driver->user_id, 'version' => $version, 'type' => $type, 'title' => $template->title, 'body' => $body, 'snapshot' => $snapshot, 'content_hash' => hash('sha256', $template->title . "\n" . $body), 'created_by' => $actor]);
        });
    }
    public function accept(DriverContractVersion $contract, $user, string $hash, ?string $ip, string $agent) : DriverContractVersion
    {
        return DB::transaction(function () use($contract, $user, $hash, $ip, $agent) {
            $driver = Driver::whereKey($contract->driver_id)->lockForUpdate()->firstOrFail();
            $contract = DriverContractVersion::whereKey($contract->id)->lockForUpdate()->firstOrFail();
            abort_unless((int) $contract->user_id === (int) $user->id && (int) $driver->user_id === (int) $user->id, 403);
            abort_if($contract->superseded_at || !hash_equals($contract->content_hash, $hash), 409, 'O contrato foi atualizado. Leia a nova versão.');
            if (!$contract->accepted_at) {
                $contract->update(['accepted_at' => now(), 'accepted_by' => $user->id, 'accepted_ip' => $ip, 'accepted_user_agent' => substr($agent, 0, 2000), 'acceptance_text' => 'Li e concordo com este contrato.']);
            }
            return $contract;
        });
    }
}
