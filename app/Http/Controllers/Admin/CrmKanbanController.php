<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{CrmCard, CrmCategory, CrmStage};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CrmKanbanController extends Controller
{
    public function index($categoryId = null)
    {
        \Illuminate\Support\Facades\Gate::authorize('crm_card_access');

        if (!$categoryId) {
            $first = \App\Models\CrmCategory::orderBy('position')->orderBy('id')->first();
            abort_unless($first, 404, 'Ainda não existem categorias CRM.');
            return redirect()->route('admin.crm-kanban.index', ['categoryId' => $first->id]);
        }

        $category = ctype_digit((string)$categoryId)
            ? \App\Models\CrmCategory::find((int)$categoryId)
            : \App\Models\CrmCategory::where('slug', $categoryId)->first();

        if (!$category) {
            // fallback para a primeira existente em vez de 404 duro
            $first = \App\Models\CrmCategory::orderBy('position')->orderBy('id')->first();
            abort_unless($first, 404, 'Ainda não existem categorias CRM.');
            return redirect()->route('admin.crm-kanban.index', ['categoryId' => $first->id]);
        }

        $stages = \App\Models\CrmStage::where('category_id', $category->id)->orderBy('position')->get();
        $cardsByStage = \App\Models\CrmCard::where('category_id', $category->id)
            ->where('status', 'open')->orderBy('position')->get()->groupBy('stage_id');

        return view('admin.crmKanban.index', compact('category', 'stages', 'cardsByStage'));
    }

    public function move(Request $request, \App\Models\CrmCard $crm_card)
    {
        $card = $crm_card; // para manter o nome usado na lógica abaixo

        $data = $request->validate([
            'stage_id' => ['required', 'integer', 'exists:crm_stages,id'],
            'prev_id'  => ['nullable', 'integer', 'exists:crm_cards,id'],
            'next_id'  => ['nullable', 'integer', 'exists:crm_cards,id'],
        ]);

        $card->stage_id = (int) $data['stage_id'];

        $prevPos = $data['prev_id'] ? (\App\Models\CrmCard::find($data['prev_id'])->position ?? null) : null;
        $nextPos = $data['next_id'] ? (\App\Models\CrmCard::find($data['next_id'])->position ?? null) : null;

        if ($prevPos !== null && $nextPos !== null) {
            $card->position = (int) floor(($prevPos + $nextPos) / 2);
        } elseif ($prevPos !== null) {
            $card->position = $prevPos + 1000;
        } elseif ($nextPos !== null) {
            $card->position = $nextPos - 1000;
        } else {
            $max = \App\Models\CrmCard::where('stage_id', $card->stage_id)->max('position') ?? 0;
            $card->position = $max + 1000;
        }

        $card->save();

        return response()->json(['ok' => true]);
    }
}
