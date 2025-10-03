<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{CrmForm, CrmFormField, CrmFormSubmission, CrmCard, CrmStage};
use Carbon\Carbon;

class PublicFormsController extends Controller
{
    public function submit(Request $request)
    {
        $form = CrmForm::with('fields')->findOrFail($request->input('form_id'));

        // construir regras dinamicamente
        $rules = ['form_id' => ['required','integer','exists:crm_forms,id']];
        foreach ($form->fields as $f) {
            $key = "f.{$f->id}";
            $r = [];
            if ($f->required) $r[] = 'required';
            switch ($f->type) {
                case 'number':
                    $r[] = 'numeric';
                    if (!is_null($f->min_value)) $r[] = 'min:'.$f->min_value;
                    if (!is_null($f->max_value)) $r[] = 'max:'.$f->max_value;
                    break;
                case 'select':
                    $opts = json_decode($f->options_json ?: '[]', true) ?: [];
                    if ($opts) $r[] = 'in:'.implode(',', array_map(fn($v)=>str_replace(',','\,',$v), $opts));
                    break;
                case 'checkbox':
                    // checkbox não obrigatório = sometimes|accepted
                    $r[] = $f->required ? 'accepted' : 'nullable';
                    break;
                default:
                    $r[] = 'string';
            }
            $rules[$key] = $r;
        }

        $data = $request->validate($rules);

        // guardar submissão
        $payload = $request->input('f', []);
        $sub = new CrmFormSubmission();
        $sub->form_id     = $form->id;
        $sub->category_id = $form->category_id;
        $sub->submitted_at= Carbon::now()->format('Y-m-d H:i:s');
        $sub->user_agent  = substr($request->userAgent() ?? '', 0, 255);
        $sub->referer     = substr($request->headers->get('referer',''),0,255);
        $sub->utm_json    = json_encode([
            'utm_source'   => $request->get('utm_source'),
            'utm_medium'   => $request->get('utm_medium'),
            'utm_campaign' => $request->get('utm_campaign'),
        ]);
        $sub->data_json   = json_encode($payload);
        $sub->save();

        // criar card, se configurado
        if ($form->create_card_on_submit) {
            $stage = CrmStage::where('category_id', $form->category_id)->orderBy('position')->first();
            if ($stage) {
                // tentar título com 1.º campo de texto com conteúdo
                $title = 'Formulário: '.$form->name;
                foreach ($form->fields as $f) {
                    if (in_array($f->type, ['text','textarea']) && !empty($payload[$f->id] ?? null)) {
                        $title = (string) $payload[$f->id];
                        break;
                    }
                }

                $card = new CrmCard();
                $card->category_id = $stage->category_id;
                $card->stage_id    = $stage->id;
                $card->title       = mb_substr($title,0,190);
                $card->priority    = 'medium';
                $card->status      = 'open';
                $card->source      = 'form';
                $card->form_id     = $form->id;
                $card->form_submission_id = $sub->id; // (está no teu modelo CrmCard)
                $card->fields_snapshot_json = $sub->data_json;

                $card->position = ((int) CrmCard::where('stage_id',$stage->id)->max('position')) + 1000;
                $card->save();

                $sub->created_card_id = $card->id;
                $sub->save();
            }
        }

        // sucesso
        $msg = $form->confirmation_message ?: 'Obrigado! Recebemos a sua mensagem.';
        if ($form->redirect_url) {
            return redirect()->to($form->redirect_url)->with('form_ok_'.$form->id, $msg);
        }
        return back()->with('form_ok_'.$form->id, $msg)->withInput([]);
    }
}
