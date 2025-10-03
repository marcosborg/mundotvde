<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyCrmCardRequest;
use App\Http\Requests\StoreCrmCardRequest;
use App\Http\Requests\UpdateCrmCardRequest;
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
use App\Models\{CrmCard, CrmFormSubmission};
use Illuminate\Support\Facades\Auth;
use App\Models\CrmCardNote;
use App\Models\CrmCardActivity;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

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

    public function quickShow(CrmCard $crm_card)
    {
        Gate::authorize('crm_card_access');

        $raw = $crm_card->getRawOriginal('due_at');

        return response()->json([
            'ok' => true,
            'card' => [
                'id'       => $crm_card->id,
                'title'    => $crm_card->title,
                'priority' => $crm_card->priority,
                'stage_id' => $crm_card->stage_id,
                'position' => $crm_card->position,
                'due_at'   => $raw ? \Carbon\Carbon::parse($raw)->format('Y-m-d') : null,
                'show_url' => route('admin.crm-cards.show', $crm_card->id),
            ],
        ]);
    }

    public function quickStore(Request $request)
    {
        Gate::authorize('crm_card_create');

        $data = $request->validate([
            'category_id'        => ['required', 'integer', 'exists:crm_categories,id'],
            'stage_id'           => ['required', 'integer', 'exists:crm_stages,id'],
            'title'              => ['required', 'string', 'max:255'],
            'priority'           => ['nullable', 'in:low,medium,high'],
            'due_at'             => ['nullable', 'string'],
            // novos opcionais quando a origem é formulário:
            'form_submission_id' => ['nullable', 'integer', 'exists:crm_form_submissions,id'],
        ]);

        $card = new CrmCard();
        $card->fill([
            'category_id' => (int) $data['category_id'],
            'stage_id'    => (int) $data['stage_id'],
            'title'       => $data['title'],
            'priority'    => $data['priority'] ?? 'medium',
            'status'      => 'open',
            'source'      => $request->filled('form_submission_id') ? 'form' : 'manual',
        ]);

        if (!empty($data['due_at'])) {
            try {
                $card->due_at = preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data['due_at'])
                    ? Carbon::createFromFormat('d/m/Y', $data['due_at'])->format('Y-m-d')
                    : Carbon::parse($data['due_at'])->format('Y-m-d');
            } catch (\Throwable) {
                $card->due_at = null;
            }
        }

        // posição no fim da coluna
        $card->position = ((int) CrmCard::where('stage_id', $card->stage_id)->max('position')) + 1000;

        // se veio de uma submissão, copia a info
        if ($request->filled('form_submission_id')) {
            $sub = CrmFormSubmission::with('form')->find($request->form_submission_id);
            if ($sub) {
                $card->form_id = $sub->form_id;
                $card->form_submission_id = $sub->id;
                $card->fields_snapshot_json = $sub->fields_json ?? $sub->payload_json ?? null;
                // TIP: podes também gerar o título automaticamente a partir do submission, se quiseres
            }
        }

        $card->save();

        return response()->json([
            'ok'   => true,
            'card' => [
                'id'        => $card->id,
                'title'     => $card->title,
                'priority'  => $card->priority,
                'stage_id'  => $card->stage_id,
                'position'  => $card->position,
                'due_at'    => optional($card->due_at)->format('Y-m-d'),
                'show_url'  => route('admin.crm-cards.show', $card->id),
                // útil para o modal mostrar snapshot
                'fields_snapshot_json' => $card->fields_snapshot_json,
            ],
        ], 201);
    }

    public function quickUpdate(Request $request, CrmCard $crm_card)
    {
        Gate::authorize('crm_card_edit');

        $data = $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'stage_id' => ['required', 'integer', 'exists:crm_stages,id'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'due_at'   => ['nullable', 'string'],
        ]);

        $crm_card->title    = $data['title'];
        $crm_card->stage_id = (int) $data['stage_id'];
        $crm_card->priority = $data['priority'] ?? 'medium';

        $due = trim((string)($data['due_at'] ?? ''));
        if ($due === '') {
            $crm_card->due_at = null;
        } else {
            try {
                $crm_card->due_at = preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $due)
                    ? Carbon::createFromFormat('d/m/Y', $due)->format('Y-m-d')
                    : Carbon::parse($due)->format('Y-m-d');
            } catch (\Throwable) {
                $crm_card->due_at = null;
            }
        }

        $crm_card->save();

        $raw = $crm_card->getRawOriginal('due_at');

        return response()->json([
            'ok'   => true,
            'card' => [
                'id'       => $crm_card->id,
                'title'    => $crm_card->title,
                'priority' => $crm_card->priority,
                'stage_id' => $crm_card->stage_id,
                'position' => $crm_card->position,
                'due_at'   => $raw ? \Carbon\Carbon::parse($raw)->format('Y-m-d') : null,
                'fields_snapshot_json' => $crm_card->fields_snapshot_json,
                'show_url' => route('admin.crm-cards.show', $crm_card->id),
            ],
        ]);
    }

    public function quickListNotes(CrmCard $crm_card)
    {
        \Gate::authorize('crm_card_access');

        $notes = $crm_card->notes()->with('user')->latest()->get()->map(function ($n) {
            return [
                'id'        => $n->id,
                'user_name' => optional($n->user)->name,
                'content'   => $n->content,
                'created_at' => $n->created_at->diffForHumans(),
            ];
        });

        return response()->json(['ok' => true, 'notes' => $notes]);
    }

    public function quickAddNote(Request $request, CrmCard $crm_card)
    {
        \Gate::authorize('crm_card_edit');

        $data = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
        ]);

        $note = CrmCardNote::create([
            'card_id' => $crm_card->id,
            'user_id' => Auth::id(),
            'content' => $data['content'],
        ]);

        // Regista atividade
        CrmCardActivity::create([
            'card_id'       => $crm_card->id,
            'type'          => 'note',
            'meta_json'     => json_encode(['note_id' => $note->id]),
            'created_by_id' => Auth::id(),
        ]);

        return response()->json([
            'ok' => true,
            'note' => [
                'id' => $note->id,
                'user_name' => optional($note->user)->name,
                'content' => $note->content,
                'created_at' => $note->created_at->diffForHumans(),
            ]
        ], 201);
    }

    public function quickListAttachments(CrmCard $crm_card)
    {
        \Gate::authorize('crm_card_access');

        $items = $crm_card->getMedia('crm_card_attachments')->map(function ($m) {
            return [
                'id'   => $m->id,
                'name' => $m->file_name,
                'size' => $m->human_readable_size,
                'url'  => $m->getUrl(),
            ];
        });

        return response()->json(['ok' => true, 'attachments' => $items]);
    }

    public function quickUploadAttachment(Request $request, CrmCard $crm_card)
    {
        \Gate::authorize('crm_card_edit');

        $request->validate([
            'file' => ['required', 'file', 'max:20480'], // 20MB
        ]);

        $media = $crm_card->addMediaFromRequest('file')->toMediaCollection('crm_card_attachments');

        // atividade
        CrmCardActivity::create([
            'card_id'       => $crm_card->id,
            'type'          => 'attachment',
            'meta_json'     => json_encode(['media_id' => $media->id, 'file' => $media->file_name]),
            'created_by_id' => Auth::id(),
        ]);

        return response()->json([
            'ok' => true,
            'attachment' => [
                'id' => $media->id,
                'name' => $media->file_name,
                'size' => $media->human_readable_size,
                'url' => $media->getUrl(),
            ]
        ], 201);
    }

    public function quickDeleteAttachment(CrmCard $crm_card, SpatieMedia $media)
    {
        \Gate::authorize('crm_card_edit');

        // garante que pertence a este card
        abort_unless($media->model_type === CrmCard::class && (int)$media->model_id === (int)$crm_card->id, 404);

        $media->delete();

        CrmCardActivity::create([
            'card_id'       => $crm_card->id,
            'type'          => 'attachment',
            'meta_json'     => json_encode(['deleted_media_id' => $media->id]),
            'created_by_id' => Auth::id(),
        ]);

        return response()->json(['ok' => true]);
    }

    public function quickListActivities(CrmCard $crm_card)
    {
        \Gate::authorize('crm_card_access');

        $items = $crm_card->activities()->latest()->get()->map(function ($a) {
            return [
                'id'   => $a->id,
                'type' => $a->type,
                'meta' => json_decode($a->meta_json, true),
                'who'  => optional($a->created_by)->name,
                'when' => $a->created_at->diffForHumans(),
            ];
        });

        return response()->json(['ok' => true, 'activities' => $items]);
    }
}
