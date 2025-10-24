<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDocumentGeneratedRequest;
use App\Http\Requests\StoreDocumentGeneratedRequest;
use App\Http\Requests\UpdateDocumentGeneratedRequest;
use App\Models\DocumentGenerated;
use App\Models\DocumentManagement;
use App\Models\Driver;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Services\DocumentRenderService;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentGeneratedController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('document_generated_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = DocumentGenerated::with(['document_management', 'driver'])->select(sprintf('%s.*', (new DocumentGenerated)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'document_generated_show';
                $editGate      = 'document_generated_edit';
                $deleteGate    = 'document_generated_delete';
                $crudRoutePart = 'document-generateds';

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
            $table->addColumn('document_management_title', function ($row) {
                return $row->document_management ? $row->document_management->title : '';
            });

            $table->addColumn('driver_name', function ($row) {
                return $row->driver ? $row->driver->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'document_management', 'driver']);

            return $table->make(true);
        }

        return view('admin.documentGenerateds.index');
    }

    public function create()
    {
        abort_if(Gate::denies('document_generated_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $document_managements = DocumentManagement::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.documentGenerateds.create', compact('document_managements', 'drivers'));
    }

    public function store(StoreDocumentGeneratedRequest $request)
    {
        $documentGenerated = DocumentGenerated::create($request->all());

        return redirect()->route('admin.document-generateds.index');
    }

    public function edit(DocumentGenerated $documentGenerated)
    {
        abort_if(Gate::denies('document_generated_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $document_managements = DocumentManagement::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $documentGenerated->load('document_management', 'driver');

        return view('admin.documentGenerateds.edit', compact('documentGenerated', 'document_managements', 'drivers'));
    }

    public function update(UpdateDocumentGeneratedRequest $request, DocumentGenerated $documentGenerated)
    {
        $documentGenerated->update($request->all());

        return redirect()->route('admin.document-generateds.index');
    }

    public function show(DocumentGenerated $documentGenerated)
    {
        abort_if(Gate::denies('document_generated_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $documentGenerated->load('document_management', 'driver');

        return view('admin.documentGenerateds.show', compact('documentGenerated'));
    }

    public function destroy(DocumentGenerated $documentGenerated)
    {
        abort_if(Gate::denies('document_generated_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $documentGenerated->delete();

        return back();
    }

    public function massDestroy(MassDestroyDocumentGeneratedRequest $request)
    {
        $documentGenerateds = DocumentGenerated::find(request('ids'));

        foreach ($documentGenerateds as $documentGenerated) {
            $documentGenerated->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function pdf(DocumentGenerated $documentGenerated)
    {
        abort_if(Gate::denies('document_generated_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $documentGenerated->load('document_management.doc_company', 'document_management.signatures.media', 'driver');

        $render = DocumentRenderService::renderBody($documentGenerated);
        $repl   = $render['replacements']; // mapa de substituições

        $signatureImages = [];
        foreach ($render['signatures'] as $sig) {
            $media   = $sig->getFirstMedia('signature');
            $path    = $media ? $media->getPath() : null;
            $dataUri = DocumentRenderService::imageToDataUri($path);

            $extraRaw  = (string)($sig->other_fields ?? '');
            $extraHtml = $extraRaw !== '' ? DocumentRenderService::renderText($repl, $extraRaw, true) : '';

            $signatureImages[] = [
                'title'      => $sig->title ?? '',
                'uri'        => $dataUri,          // null se não houver imagem
                'extra_html' => $extraHtml,        // HTML já renderizado (com tags substituídas)
            ];
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.documentGenerateds.pdf', [
            'title'           => $render['title'],
            'body_html'       => $render['body_html'],
            'signatureImages' => $signatureImages,
            'generated'       => $documentGenerated,
        ])->setPaper('a4');

        $pdf->setOption(['isRemoteEnabled' => true]);

        return $pdf->download('documento-' . $documentGenerated->id . '.pdf');
    }
}
