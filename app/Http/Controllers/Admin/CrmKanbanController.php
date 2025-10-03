<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{CrmCard, CrmCategory, CrmStage};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CrmKanbanController extends Controller
{
    public function index($category = null)
    {
        \Illuminate\Support\Facades\Gate::authorize('crm_card_access');

        // Se entrares aqui sem categoria, manda para o HUB (ou troca para "first" se preferires)
        if (is_null($category)) {
            return redirect()->route('admin.crm-kanban.hub');
        }

        // Aceita ID numérico ou slug
        $categoryModel = \App\Models\CrmCategory::query()
            ->when(
                ctype_digit((string) $category),
                fn($q) => $q->where('id', (int) $category),
                fn($q) => $q->where('slug', $category)
            )
            ->first();

        if (!$categoryModel) {
            // fallback mais amigável (hub). Se preferires, redirecciona para a 1.ª categoria.
            return redirect()
                ->route('admin.crm-kanban.hub')
                ->with('error', 'Categoria não encontrada.');
        }

        // Carrega ESTADOS e CARDS já ordenados (e só “open”)
        $categoryModel->load([
            'stages' => fn($q) => $q->orderBy('position')->orderBy('id'),
            'stages.cards' => fn($q) => $q->where('status', 'open')
                ->orderBy('position')->orderBy('id'),
        ]);

        // O Blade deve iterar $category->stages; já vêm ordenados aqui.
        return view('admin.crmKanban.index', [
            'category' => $categoryModel,
        ]);
    }

    public function move(Request $request, \App\Models\CrmCard $crm_card)
    {
        \Illuminate\Support\Facades\Gate::authorize('crm_card_edit');

        // valida e já preenche defaults para evitar Undefined array key
        $data = array_merge(
            ['prev_id' => null, 'next_id' => null],
            $request->validate([
                'stage_id' => ['required', 'integer', 'exists:crm_stages,id'],
                'prev_id'  => ['nullable', 'integer', 'exists:crm_cards,id'],
                'next_id'  => ['nullable', 'integer', 'exists:crm_cards,id'],
            ])
        );

        $card = $crm_card;

        $card->stage_id = (int) $data['stage_id'];

        $prevId = $data['prev_id'];
        $nextId = $data['next_id'];

        // lê posições de forma segura (pode ser null)
        $prevPos = $prevId ? \App\Models\CrmCard::whereKey($prevId)->value('position') : null;
        $nextPos = $nextId ? \App\Models\CrmCard::whereKey($nextId)->value('position') : null;

        if ($prevPos !== null && $nextPos !== null) {
            $card->position = (int) floor(($prevPos + $nextPos) / 2);
        } elseif ($prevPos !== null) {
            $card->position = (int) $prevPos + 1000;
        } elseif ($nextPos !== null) {
            $card->position = (int) $nextPos - 1000;
        } else {
            // coluna vazia ou card largado no topo sem vizinhos
            $max = \App\Models\CrmCard::where('stage_id', $card->stage_id)->max('position') ?? 0;
            $card->position = (int) $max + 1000;
        }

        // (opcional) ajustar status quando entra em estádios finais
        $toStage = \App\Models\CrmStage::find($card->stage_id);
        if ($toStage?->is_won) {
            $card->status = 'won';
            $card->won_at = now();
            $card->closed_at = now();
        } elseif ($toStage?->is_lost) {
            $card->status = 'lost';
            $card->closed_at = now();
        } elseif (in_array($card->status, ['won', 'lost', 'archived'])) {
            // reabrir se saiu de um final para intermédio
            $card->status = 'open';
            $card->won_at = null;
            $card->closed_at = null;
        }

        $card->save();

        return response()->json([
            'ok'   => true,
            'card' => [
                'id'        => $card->id,
                'stage_id'  => $card->stage_id,
                'position'  => $card->position,
                'status'    => $card->status,
            ],
        ]);
    }

    public function quickCreate(\Illuminate\Http\Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('crm_card_create');

        $v = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:crm_categories,id'],
            'stage_id' => ['required', 'exists:crm_stages,id'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'value_amount' => ['nullable', 'numeric'],
            'value_currency' => ['nullable', 'string', 'size:3'],
            'due_at' => ['nullable', 'date'],
        ]);

        $pos = (int)((\App\Models\CrmCard::where('stage_id', $v['stage_id'])->max('position') ?? 0) + 1000);

        $card = \App\Models\CrmCard::create([
            'title' => $v['title'],
            'category_id' => (int)$v['category_id'],
            'stage_id' => (int)$v['stage_id'],
            'position' => $pos,
            'status' => 'open',
            'source' => 'manual',
            'priority' => $v['priority'] ?? 'medium',
            'value_amount' => $v['value_amount'] ?? null,
            'value_currency' => $v['value_currency'] ?? 'EUR',
            'due_at' => $v['due_at'] ?? null,
            'created_by_id' => auth()->id(),
        ]);

        return response()->json([
            'ok' => true,
            'card' => [
                'id' => $card->id,
                'title' => $card->title,
                'priority' => $card->priority,
                'value_amount' => $card->value_amount,
                'value_currency' => $card->value_currency,
                'position' => $card->position,
                'stage_id' => $card->stage_id,
                'show_url' => route('admin.crm-cards.show', $card->id),
            ],
        ]);
    }

    public function quickShow(\App\Models\CrmCard $crm_card)
    {
        \Gate::authorize('crm_card_edit');
        $card = $crm_card->only(['id', 'title', 'priority', 'value_amount', 'value_currency', 'due_at', 'stage_id', 'category_id']);
        $card['due_at'] = $crm_card->due_at ? \Carbon\Carbon::parse($crm_card->due_at)->format('Y-m-d') : null;

        $stages = \App\Models\CrmStage::where('category_id', $crm_card->category_id)->orderBy('position')->get(['id', 'name']);
        return response()->json(['ok' => true, 'card' => $card, 'stages' => $stages]);
    }

    public function quickUpdate(\Illuminate\Http\Request $request, \App\Models\CrmCard $crm_card)
    {
        \Gate::authorize('crm_card_edit');
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'value_amount' => ['nullable', 'numeric'],
            'value_currency' => ['nullable', 'string', 'size:3'],
            'due_at' => ['nullable', 'date'],
            'stage_id' => ['required', 'integer', 'exists:crm_stages,id'],
        ]);
        $stageChanged = (int)$data['stage_id'] !== (int)$crm_card->stage_id;

        $crm_card->fill([
            'title' => $data['title'],
            'priority' => $data['priority'] ?? 'medium',
            'value_amount' => $data['value_amount'] ?? null,
            'value_currency' => $data['value_currency'] ?? ($crm_card->value_currency ?? 'EUR'),
            'due_at' => $data['due_at'] ?? null,
            'stage_id' => (int)$data['stage_id'],
        ]);

        if ($stageChanged) {
            $crm_card->position = (int)((\App\Models\CrmCard::where('stage_id', $crm_card->stage_id)->max('position') ?? 0) + 1000);
            $toStage = \App\Models\CrmStage::find($crm_card->stage_id);
            if ($toStage?->is_won) {
                $crm_card->status = 'won';
                $crm_card->won_at = now();
                $crm_card->closed_at = now();
            } elseif ($toStage?->is_lost) {
                $crm_card->status = 'lost';
                $crm_card->closed_at = now();
            } elseif (in_array($crm_card->status, ['won', 'lost', 'archived'])) {
                $crm_card->status = 'open';
                $crm_card->won_at = null;
                $crm_card->closed_at = null;
            }
        }

        $crm_card->save();

        return response()->json([
            'ok' => true,
            'card' => [
                'id' => $crm_card->id,
                'title' => $crm_card->title,
                'priority' => $crm_card->priority,
                'value_amount' => $crm_card->value_amount,
                'value_currency' => $crm_card->value_currency,
                'due_at' => $crm_card->due_at ? \Carbon\Carbon::parse($crm_card->due_at)->format('Y-m-d') : null,
                'stage_id' => $crm_card->stage_id,
                'position' => $crm_card->position,
                'show_url' => route('admin.crm-cards.show', $crm_card->id),
            ]
        ]);
    }

    public function storeCategory(\Illuminate\Http\Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('crm_category_create');

        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string', 'max:1000'],
            'color'          => ['nullable', 'string', 'max:20'],
            'with_defaults'  => ['nullable', 'boolean'],
        ]);

        // Criar categoria (só define colunas que existirem na tabela)
        $cat = new \App\Models\CrmCategory();
        $cat->name = $data['name'];

        if (\Illuminate\Support\Facades\Schema::hasColumn('crm_categories', 'position')) {
            $cat->position = (int) ((\App\Models\CrmCategory::max('position') ?? 0) + 1000);
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('crm_categories', 'description') && array_key_exists('description', $data)) {
            $cat->description = $data['description'];
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('crm_categories', 'color') && array_key_exists('color', $data)) {
            $cat->color = $data['color'];
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('crm_categories', 'slug')) {
            $base = \Illuminate\Support\Str::slug($data['name']) ?: 'categoria';
            $slug = $base;
            $i = 2;
            while (\App\Models\CrmCategory::where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }
            $cat->slug = $slug;
        }
        $cat->save();

        // Estados por omissão (opcional)
        if ($request->boolean('with_defaults')) {
            $defaults = [
                ['name' => 'Novo',        'color' => '#e5e7eb', 'is_won' => false, 'is_lost' => false],
                ['name' => 'Em contacto', 'color' => '#dbeafe', 'is_won' => false, 'is_lost' => false],
                ['name' => 'Qualificado', 'color' => '#cffafe', 'is_won' => false, 'is_lost' => false],
                ['name' => 'Ganho',       'color' => '#dcfce7', 'is_won' => true,  'is_lost' => false],
                ['name' => 'Perdido',     'color' => '#fee2e2', 'is_won' => false, 'is_lost' => true],
            ];
            $pos = (int) ((\App\Models\CrmStage::where('category_id', $cat->id)->max('position') ?? 0) + 1000);
            foreach ($defaults as $d) {
                $st = new \App\Models\CrmStage();
                $st->category_id = $cat->id;
                $st->name = $d['name'];
                if (\Illuminate\Support\Facades\Schema::hasColumn('crm_stages', 'position')) $st->position = $pos;
                if (\Illuminate\Support\Facades\Schema::hasColumn('crm_stages', 'color'))    $st->color    = $d['color'];
                if (\Illuminate\Support\Facades\Schema::hasColumn('crm_stages', 'is_won'))   $st->is_won   = $d['is_won'];
                if (\Illuminate\Support\Facades\Schema::hasColumn('crm_stages', 'is_lost'))  $st->is_lost  = $d['is_lost'];
                $st->save();
                $pos += 1000;
            }
        }

        return response()->json(['ok' => true, 'category' => ['id' => $cat->id]]);
    }

    public function hub()
    {
        \Gate::authorize('crm_kanban_access');

        $categories = \App\Models\CrmCategory::with([
            'stages' => fn($q) => $q->orderBy('position'),
        ])->withCount([
            'stages',
            'cards as open_cards_count' => fn($q) => $q->where('status', 'open'),
        ])->orderBy('position')->orderBy('name')->get();

        return view('admin.crmKanban.hub', compact('categories'));
    }

    public function storeStage(\Illuminate\Http\Request $request, \App\Models\CrmCategory $category)
    {
        \Gate::authorize('crm_stage_create');

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'color'   => ['nullable', 'string', 'max:20'],
            'is_won'  => ['nullable', 'boolean'],
            'is_lost' => ['nullable', 'boolean'],
        ]);

        if (($request->boolean('is_won') && $request->boolean('is_lost'))) {
            return response()->json(['ok' => false, 'errors' => ['is_won' => ['Não pode ser ganho e perdido ao mesmo tempo.']]], 422);
        }

        $stage = \App\Models\CrmStage::create([
            'category_id' => $category->id,
            'name'        => $data['name'],
            'color'       => $data['color'] ?? null,
            'is_won'      => $request->boolean('is_won'),
            'is_lost'     => $request->boolean('is_lost'),
            'position'    => (int)((\App\Models\CrmStage::where('category_id', $category->id)->max('position') ?? 0) + 1000),
        ]);

        return response()->json(['ok' => true, 'stage' => [
            'id' => $stage->id,
            'name' => $stage->name,
            'color' => $stage->color,
            'is_won' => $stage->is_won,
            'is_lost' => $stage->is_lost
        ]]);
    }

    public function updateStage(\Illuminate\Http\Request $request, \App\Models\CrmStage $stage)
    {
        \Gate::authorize('crm_stage_edit');

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'color'   => ['nullable', 'string', 'max:20'],
            'is_won'  => ['nullable', 'boolean'],
            'is_lost' => ['nullable', 'boolean'],
        ]);

        if (($request->boolean('is_won') && $request->boolean('is_lost'))) {
            return response()->json(['ok' => false, 'errors' => ['is_won' => ['Não pode ser ganho e perdido ao mesmo tempo.']]], 422);
        }

        $stage->update([
            'name'    => $data['name'],
            'color'   => $data['color'] ?? null,
            'is_won'  => $request->boolean('is_won'),
            'is_lost' => $request->boolean('is_lost'),
        ]);

        return response()->json(['ok' => true, 'stage' => [
            'id' => $stage->id,
            'name' => $stage->name,
            'color' => $stage->color,
            'is_won' => $stage->is_won,
            'is_lost' => $stage->is_lost
        ]]);
    }

    public function destroyStage(\App\Models\CrmStage $stage)
    {
        \Gate::authorize('crm_stage_delete');

        $hasOpenCards = \App\Models\CrmCard::where('stage_id', $stage->id)->where('status', 'open')->exists();
        if ($hasOpenCards) {
            return response()->json(['ok' => false, 'message' => 'Não é possível apagar: existem cards abertos neste estado.'], 422);
        }
        $stage->delete();

        return response()->json(['ok' => true]);
    }

    public function reorderStages(\Illuminate\Http\Request $request, \App\Models\CrmCategory $category)
    {
        \Gate::authorize('crm_stage_edit');

        $data = $request->validate([
            'order' => ['required', 'array', 'min:1'], // array de IDs na nova ordem
            'order.*' => ['integer', 'exists:crm_stages,id']
        ]);

        $pos = 1000;
        foreach ($data['order'] as $id) {
            \App\Models\CrmStage::where('id', $id)
                ->where('category_id', $category->id)
                ->update(['position' => $pos]);
            $pos += 1000;
        }

        return response()->json(['ok' => true]);
    }
}
