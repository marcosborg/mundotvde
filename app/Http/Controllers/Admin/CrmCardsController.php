<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyCrmCardRequest;
use App\Http\Requests\StoreCrmCardRequest;
use App\Http\Requests\UpdateCrmCardRequest;
use App\Models\CrmCard;
use App\Models\CrmCategory;
use App\Models\CrmForm;
use App\Models\CrmStage;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CrmCardsController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('crm_card_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = CrmCard::with(['category', 'stage', 'form', 'assigned_to', 'created_by'])->select(sprintf('%s.*', (new CrmCard)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'crm_card_show';
                $editGate      = 'crm_card_edit';
                $deleteGate    = 'crm_card_delete';
                $crudRoutePart = 'crm-cards';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->addColumn('category_name', function ($row) {
                return $row->category ? $row->category->name : '';
            });

            $table->addColumn('stage_name', function ($row) {
                return $row->stage ? $row->stage->name : '';
            });

            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->addColumn('form_name', function ($row) {
                return $row->form ? $row->form->name : '';
            });

            $table->editColumn('source', function ($row) {
                return $row->source ? CrmCard::SOURCE_RADIO[$row->source] : '';
            });
            $table->editColumn('priority', function ($row) {
                return $row->priority ? CrmCard::PRIORITY_RADIO[$row->priority] : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? CrmCard::STATUS_RADIO[$row->status] : '';
            });
            $table->editColumn('lost_reason', function ($row) {
                return $row->lost_reason ? $row->lost_reason : '';
            });

            $table->addColumn('assigned_to_name', function ($row) {
                return $row->assigned_to ? $row->assigned_to->name : '';
            });

            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->editColumn('position', function ($row) {
                return $row->position ? $row->position : '';
            });
            $table->editColumn('fields_snapshot_json', function ($row) {
                return $row->fields_snapshot_json ? $row->fields_snapshot_json : '';
            });
            $table->editColumn('crm_card_attachments', function ($row) {
                if (! $row->crm_card_attachments) {
                    return '';
                }
                $links = [];
                foreach ($row->crm_card_attachments as $media) {
                    $links[] = '<a href="' . $media->getUrl() . '" target="_blank">' . trans('global.downloadFile') . '</a>';
                }

                return implode(', ', $links);
            });

            $table->rawColumns(['actions', 'placeholder', 'category', 'stage', 'form', 'assigned_to', 'created_by', 'crm_card_attachments']);

            return $table->make(true);
        }

        return view('admin.crmCards.index');
    }

    public function create()
    {
        abort_if(Gate::denies('crm_card_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = CrmCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $stages = CrmStage::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $forms = CrmForm::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $assigned_tos = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.crmCards.create', compact('assigned_tos', 'categories', 'created_bies', 'forms', 'stages'));
    }

    public function store(StoreCrmCardRequest $request)
    {
        $crmCard = CrmCard::create($request->all());

        foreach ($request->input('crm_card_attachments', []) as $file) {
            $crmCard->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('crm_card_attachments');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $crmCard->id]);
        }

        return redirect()->route('admin.crm-cards.index');
    }

    public function edit(CrmCard $crmCard)
    {
        abort_if(Gate::denies('crm_card_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = CrmCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $stages = CrmStage::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $forms = CrmForm::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $assigned_tos = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $crmCard->load('category', 'stage', 'form', 'assigned_to', 'created_by');

        return view('admin.crmCards.edit', compact('assigned_tos', 'categories', 'created_bies', 'crmCard', 'forms', 'stages'));
    }

    public function update(UpdateCrmCardRequest $request, CrmCard $crmCard)
    {
        $crmCard->update($request->all());

        if (count($crmCard->crm_card_attachments) > 0) {
            foreach ($crmCard->crm_card_attachments as $media) {
                if (! in_array($media->file_name, $request->input('crm_card_attachments', []))) {
                    $media->delete();
                }
            }
        }
        $media = $crmCard->crm_card_attachments->pluck('file_name')->toArray();
        foreach ($request->input('crm_card_attachments', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $crmCard->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('crm_card_attachments');
            }
        }

        return redirect()->route('admin.crm-cards.index');
    }

    public function show(CrmCard $crmCard)
    {
        abort_if(Gate::denies('crm_card_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmCard->load('category', 'stage', 'form', 'assigned_to', 'created_by');

        return view('admin.crmCards.show', compact('crmCard'));
    }

    public function destroy(CrmCard $crmCard)
    {
        abort_if(Gate::denies('crm_card_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmCard->delete();

        return back();
    }

    public function massDestroy(MassDestroyCrmCardRequest $request)
    {
        $crmCards = CrmCard::find(request('ids'));

        foreach ($crmCards as $crmCard) {
            $crmCard->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('crm_card_create') && Gate::denies('crm_card_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new CrmCard();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function quickShow(\App\Models\CrmCard $crm_card)
    {
        \Gate::authorize('crm_card_access');

        return response()->json([
            'ok' => true,
            'card' => [
                'id'             => $crm_card->id,
                'title'          => $crm_card->title,
                'priority'       => $crm_card->priority,
                'stage_id'       => $crm_card->stage_id,
                'position'       => $crm_card->position,
                'value_amount'   => $crm_card->value_amount,
                'value_currency' => $crm_card->value_currency,
                'due_at'         => $crm_card->due_at ? $crm_card->due_at->format('Y-m-d') : null,
                'show_url'       => route('admin.crm-cards.show', $crm_card->id),
            ],
        ]);
    }

    public function quickStore(\Illuminate\Http\Request $request)
    {
        \Gate::authorize('crm_card_create');

        $data = $request->validate([
            'category_id'    => ['required', 'integer', 'exists:crm_categories,id'],
            'stage_id'       => ['required', 'integer', 'exists:crm_stages,id'],
            'title'          => ['required', 'string', 'max:255'],
            'priority'       => ['nullable', 'in:low,medium,high'],
            'due_at'         => ['nullable', 'string'],
            'value_amount'   => ['nullable'],
            'value_currency' => ['nullable', 'string', 'max:3'],
        ]);

        $card = new \App\Models\CrmCard();
        $card->category_id = (int) $data['category_id'];
        $card->stage_id    = (int) $data['stage_id'];
        $card->title       = $data['title'];
        $card->priority    = $data['priority'] ?? 'medium';
        $card->status      = 'open';

        // position (no fim da coluna)
        $max = \App\Models\CrmCard::where('stage_id', $card->stage_id)->max('position') ?? 0;
        if (\Schema::hasColumn('crm_cards', 'position')) $card->position = $max + 1000;

        // due_at
        $due = $data['due_at'] ?? null;
        if ($due) {
            try {
                $card->due_at = preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $due)
                    ? \Carbon\Carbon::createFromFormat('d/m/Y', $due)->toDateString()
                    : \Carbon\Carbon::parse($due)->toDateString();
            } catch (\Throwable $e) {
                $card->due_at = null;
            }
        }

        // valor
        if (\Schema::hasColumn('crm_cards', 'value_amount')) {
            $val = $request->input('value_amount');
            $card->value_amount = ($val === null || $val === '') ? null : (float) str_replace(',', '.', $val);
        }
        if (\Schema::hasColumn('crm_cards', 'value_currency') && array_key_exists('value_currency', $data)) {
            $card->value_currency = strtoupper($data['value_currency'] ?: 'EUR');
        }

        $card->source = 'manual';
        $card->save();

        return response()->json([
            'ok' => true,
            'card' => [
                'id'             => $card->id,
                'title'          => $card->title,
                'priority'       => $card->priority,
                'stage_id'       => $card->stage_id,
                'position'       => $card->position,
                'value_amount'   => $card->value_amount,
                'value_currency' => $card->value_currency,
                'due_at'         => $card->due_at ? $card->due_at->format('Y-m-d') : null,
                'show_url'       => route('admin.crm-cards.show', $card->id),
            ],
        ]);
    }

    public function quickUpdate(\Illuminate\Http\Request $request, \App\Models\CrmCard $crm_card)
    {
        \Illuminate\Support\Facades\Gate::authorize('crm_card_edit');

        $data = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'stage_id'       => ['required', 'integer', 'exists:crm_stages,id'],
            'priority'       => ['nullable', 'in:low,medium,high'],
            'due_at'         => ['nullable', 'string'], // aceitamos vários formatos
            'value_amount'   => ['nullable'],          // pode vir "" (vamos tratar)
            'value_currency' => ['nullable', 'string', 'max:3'],
        ]);

        // Campos base
        $crm_card->title    = $data['title'];
        $crm_card->stage_id = (int) $data['stage_id'];
        $crm_card->priority = $data['priority'] ?? 'medium';

        // Data (YYYY-MM-DD ou DD/MM/YYYY). Sem 500 se vier inválida:
        $dueInput = isset($data['due_at']) ? trim((string)$data['due_at']) : '';
        if ($dueInput === '') {
            $crm_card->due_at = null;
        } else {
            try {
                $crm_card->due_at = preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dueInput)
                    ? Carbon::createFromFormat('d/m/Y', $dueInput)->format('Y-m-d')
                    : Carbon::parse($dueInput)->format('Y-m-d');
            } catch (\Throwable $e) {
                // Se não conseguir interpretar, não rebenta:
                $crm_card->due_at = null;
            }
        }

        // Valor (só se as colunas existirem)
        if (Schema::hasColumn('crm_cards', 'value_amount')) {
            $val = $request->input('value_amount');
            $crm_card->value_amount = ($val === null || $val === '') ? null : (float) str_replace(',', '.', $val);
        }
        if (Schema::hasColumn('crm_cards', 'value_currency')) {
            $curr = $request->input('value_currency');
            $crm_card->value_currency = $curr ? strtoupper($curr) : 'EUR';
        }

        // Guardar sem deixar 500 escapar:
        try {
            $crm_card->save();
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Falha ao gravar o card.',
            ], 422);
        }

        // Preparar due_at para JSON mesmo que seja string na BD
        $dueOut = null;
        if (!empty($crm_card->due_at)) {
            try {
                $dueOut = Carbon::parse($crm_card->due_at)->format('Y-m-d');
            } catch (\Throwable $e) {
                $dueOut = (string) $crm_card->due_at;
            }
        }

        return response()->json([
            'ok'   => true,
            'card' => [
                'id'             => $crm_card->id,
                'title'          => $crm_card->title,
                'priority'       => $crm_card->priority,
                'stage_id'       => $crm_card->stage_id,
                'position'       => $crm_card->position,
                'value_amount'   => $crm_card->value_amount,
                'value_currency' => $crm_card->value_currency,
                'due_at'         => $dueOut,
                'show_url'       => route('admin.crm-cards.show', $crm_card->id),
            ],
        ]);
    }
}
