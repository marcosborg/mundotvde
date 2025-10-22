<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\CrmForm;
use App\Models\CrmFormSubmission;
use App\Models\CrmCard;
use App\Models\CrmCardActivity;
use App\Models\Car;
use App\Models\StandCar;

class PublicFormsController extends Controller
{

    public function submit(Request $request)
    {
        $slug = (string) ($request->input('form_slug') ?? $request->input('slug'));
        $form = CrmForm::with(['fields' => fn($q) => $q->orderBy('position')])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Regras dinâmicas
        $rules = [];
        foreach ($form->fields as $f) {
            $name = 'field_' . $f->id;
            $r = [];
            if ($f->required) $r[] = 'required';
            switch ($f->type) {
                case 'number':
                    $r[] = 'numeric';
                    if (!is_null($f->min_value)) $r[] = 'min:' . $f->min_value;
                    if (!is_null($f->max_value)) $r[] = 'max:' . $f->max_value;
                    break;
                case 'checkbox':
                    $r[] = 'nullable';
                    break;
                default:
                    $r[] = 'nullable|string';
            }
            $rules[$name] = implode('|', $r);
        }
        $validated = $request->validate($rules);

        // Normalizar dados em payload legível
        $payload = [];
        foreach ($form->fields as $f) {
            $key = 'field_' . $f->id;
            $val = $validated[$key] ?? null;
            if ($f->type === 'checkbox') {
                $val = $request->has($key) ? 1 : 0;
            }
            $payload[$f->label] = $val;
        }

        // Guardar submissão
        $submission = CrmFormSubmission::create([
            'form_id'      => $form->id,
            'category_id'  => $form->category_id,
            'submitted_at' => now(),
            'user_agent'   => substr((string) $request->userAgent(), 0, 255),
            'referer'      => substr((string) ($request->headers->get('referer') ?? url()->previous()), 0, 255),
            'utm_json'     => json_encode($request->only(['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'])),
            'data_json'    => json_encode($payload),
        ]);

        // Primeiro estádio da categoria
        $firstStage = \App\Models\CrmStage::where('category_id', $form->category_id)
            ->orderBy('position')->orderBy('id')
            ->first();

        // Criar card se configurado
        if ($form->create_card_on_submit) {
            try {
                // ----------------- TÍTULO ESPECIAL POR SLUG -----------------
                // Base (comportamento atual)
                $title = ($payload['Nome'] ?? $payload['name'] ?? $payload['email'] ?? ($form->name . ' — Submissão'));

                // Regras especiais (sem quebrar o fluxo atual)
                // RENT: usar Car::title + subtitle
                if ($slug === 'rent') {
                    $carId = $request->input('car_id') ?? $request->query('car');
                    if ($carId) {
                        if ($car = Car::find($carId)) {
                            $t = trim(($car->title ?? '') . ' — ' . ($car->subtitle ?? ''));
                            $title = $t ? "Rent: {$t}" : "Rent: #{$car->id}";
                        } else {
                            $title = 'Rent: Pedido de aluguer';
                        }
                    }
                }

                // STAND: usar StandCar + relações (Brand, CarModel, Fuel) + transmision
                if ($slug === 'stand') {
                    $standId = $request->input('stand_car_id') ?? $request->query('stand');
                    if ($standId) {
                        $stand = StandCar::with(['brand', 'car_model', 'fuel'])->find($standId);
                        if ($stand) {
                            $brand = optional($stand->brand)->name;
                            $model = optional($stand->car_model)->name;
                            $fuel  = optional($stand->fuel)->name;
                            $tx    = $stand->transmision; // 'Manual' | 'Auto'
                            $left  = trim(implode(' ', array_filter([$brand, $model])));
                            $right = implode(' — ', array_filter([$fuel, $tx]));
                            $title = $left ?: "StandCar #{$stand->id}";
                            if ($right) $title .= ' — ' . $right;
                        } else {
                            $title = 'Stand: Pedido sobre viatura';
                        }
                    }
                }
                // -------------------------------------------------------------

                $card = new CrmCard();
                $card->title                 = $title;
                $card->category_id           = $form->category_id;
                $card->stage_id              = optional($firstStage)->id;
                $card->priority              = 'medium';
                $card->source                = 'form';
                $card->status                = 'open';
                $card->form_id               = $form->id;
                $card->fields_snapshot_json  = json_encode($payload);
                $card->save();

                $submission->update(['created_card_id' => $card->id]);

                CrmCardActivity::create([
                    'card_id'       => $card->id,
                    'type'          => 'form_submission',
                    'meta_json'     => json_encode([
                        'form_slug'      => $form->slug,
                        'submission_id'  => $submission->id,
                    ]),
                    'created_by_id' => auth()->id(),
                ]);
            } catch (\Throwable $e) {
                \Log::error('Create card on form submit failed: ' . $e->getMessage());
            }
        }

        // Notificações por email
        if (!empty($form->notify_emails)) {
            $emails = array_filter(array_map('trim', preg_split('/[;,]+/', $form->notify_emails)));
            foreach ($emails as $to) {
                try {
                    \Mail::raw(
                        "Nova submissão ao formulário {$form->name} ({$form->slug})\n\n" . print_r($payload, true),
                        function ($m) use ($to, $form) {
                            $m->to($to)->subject('Nova submissão: ' . $form->name);
                        }
                    );
                } catch (\Throwable $e) {
                    \Log::error('Notify email failed: ' . $e->getMessage());
                }
            }
        }

        // Redirect ou mensagem
        if (!empty($form->redirect_url)) {
            return redirect()->to($form->redirect_url);
        }
        return back()->with('crm_form_ok', $form->confirmation_message ?: 'Submissão recebida com sucesso.');
    }
}
