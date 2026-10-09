@php
$versions=\App\Models\DriverContractVersion::where('driver_id',$driver->id)->orderByDesc('version')->get();
$current=$versions->first();
$welcome=\Illuminate\Support\Facades\DB::table('driver_welcome_deliveries')->where('driver_id',$driver->id)->latest('id')->first();
@endphp
<div class="panel panel-default"><div class="panel-heading">Contrato e boas-vindas</div><div class="panel-body">
@foreach($errors->all() as $error)<p class="text-danger">{{ $error }}</p>@endforeach
<p>Guarde primeiro as alterações do condutor. O contrato usa os dados guardados; só é renovado quando carregar em “Gerar contrato”.</p>
@can('admin_contract_edit')<p><a href="{{ route('admin.driver-templates.index') }}">Editar templates de contratos e e-mail de boas-vindas</a></p>@endcan
<p><strong>Estado:</strong> {{ !$current ? 'Sem contrato gerado nesta área' : ($current->accepted_at ? 'Assinado em '.$current->accepted_at->format('d/m/Y H:i') : 'Não assinado') }}</p>
@can('admin_contract_create')<form method="POST" action="{{ route('admin.drivers.generate-contract',$driver) }}">@csrf<fieldset><legend>Tipo de contrato</legend>@foreach(\App\Models\DriverMessageTemplate::TYPES as $key=>$label)<label style="display:block"><input type="radio" name="type" value="{{ $key }}" required {{ old('type',$current->type ?? '') === $key ? 'checked' : '' }}> {{ $label }}</label>@endforeach</fieldset><p>Uma nova versão terá de ser aceite pelo condutor antes de voltar a usar a área pessoal.</p><button class="btn btn-primary">Gerar contrato</button></form>@endcan
@if($versions->count())<h4>Histórico</h4><ul>@foreach($versions as $version)<li>Versão {{ $version->version }} — {{ $version->title }} — {{ $version->accepted_at ? 'Assinado' : 'Não assinado' }} {{ $version->superseded_at ? '(substituído)' : '' }} @can('admin_contract_show')<a href="{{ route('admin.driver-contracts.document',$version) }}" target="_blank" rel="noopener">Ver PDF</a>@endcan</li>@endforeach</ul>@endif
<hr><h4>E-mail de boas-vindas</h4><p>Destinatário: {{ $driver->user->email ?? 'Associe uma conta ao condutor' }}</p>@if($welcome)<p>Última operação: {{ $welcome->created_at }} {{ in_array($welcome->mailer,['log','array']) ? '(simulação local)' : '(enviado)' }}</p>@endif
<form method="POST" action="{{ route('admin.drivers.welcome',$driver) }}">@csrf<label><input type="checkbox" name="confirm" value="1" required> Enviar o e-mail de boas-vindas para esta conta</label><br><button class="btn btn-default">Enviar boas-vindas</button></form>
</div></div>
