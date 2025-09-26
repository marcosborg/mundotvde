<?php 

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{CrmForm, CrmFormField, CrmFormSubmission, CrmCard, CrmStage};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PublicCrmFormsController extends Controller
{
    public function submit(Request $request, string $slug)
    {
        $form = CrmForm::with('fields','category.stages')->where('slug',$slug)->where('status','published')->firstOrFail();

        // rules dinâmicas
        $rules = [];
        foreach ($form->fields as $f) {
            $r = [];
            if ($f->required) $r[] = 'required';
            switch ($f->type) {
                case 'number':
                    $r[] = 'numeric';
                    if ($f->min_value !== null) $r[] = 'min:'.$f->min_value;
                    if ($f->max_value !== null) $r[] = 'max:'.$f->max_value;
                    break;
                case 'checkbox':
                    $r[] = 'boolean';
                    break;
                case 'select':
                    $opts = collect($f->options_json ?? [])->pluck('value')->all();
                    $r[] = Rule::in($opts);
                    break;
                default:
                    $r[] = 'string';
            }
            if ($f->is_unique) $r[] = 'unique:crm_form_submissions,data_json->'.$f->name;
            $rules[$f->name] = $r;
        }

        $data = $request->validate($rules);

        $submission = CrmFormSubmission::create([
            'form_id'     => $form->id,
            'category_id' => $form->category_id,
            'submitted_at'=> now(),
            'ip_address'  => $request->ip(),
            'user_agent'  => (string) $request->userAgent(),
            'data_json'   => $data,
        ]);

        $cardId = null;
        if ($form->create_card_on_submit) {
            $firstStage = $form->category->stages->sortBy('position')->first();
            $title = $data['title'] ?? ($form->name.' — submission #'.$submission->id);
            $card = CrmCard::create([
                'category_id' => $form->category_id,
                'stage_id'    => $firstStage?->id,
                'title'       => $title,
                'form_id'     => $form->id,
                'submission_id' => $submission->id,
                'source'      => 'form',
                'position'    => (int) ( ( (int) \App\Models\CrmCard::where('stage_id',$firstStage?->id)->max('position') ?? 0) + 1000 ),
                'fields_snapshot_json' => $data,
            ]);
            $submission->update(['created_card_id' => $card->id]);
            $cardId = $card->id; // Observer vai disparar emails on enter
        }

        return response()->json(['ok' => true, 'submission_id' => $submission->id, 'card_id' => $cardId]);
    }
}