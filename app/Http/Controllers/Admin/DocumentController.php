<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyDocumentRequest;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\Driver;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use App\Notifications\NewDocumentSend;
use Illuminate\Support\Facades\Notification;

class DocumentController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('document_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $documents = Document::with(['driver.operation', 'media'])->get();

        return view('admin.documents.index', compact('documents'));
    }

    public function create()
    {
        abort_if(Gate::denies('document_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.documents.create', compact('drivers'));
    }

    public function store(StoreDocumentRequest $request)
    {
        $document = Document::create($request->all());

        foreach ($request->input('citizen_card', []) as $file) {
            $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('citizen_card');
        }

        foreach ($request->input('tvde_driver_certificate', []) as $file) {
            $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('tvde_driver_certificate');
        }

        foreach ($request->input('criminal_record', []) as $file) {
            $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('criminal_record');
        }

        if ($request->input('profile_picture', false)) {
            $document->addMedia(storage_path('tmp/uploads/' . basename($request->input('profile_picture'))))->toMediaCollection('profile_picture');
        }

        foreach ($request->input('driving_license', []) as $file) {
            $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('driving_license');
        }

        foreach ($request->input('iban', []) as $file) {
            $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('iban');
        }

        foreach ($request->input('address', []) as $file) {
            $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('address');
        }

        foreach ($request->input('dua_vehicle', []) as $file) {
            $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('dua_vehicle');
        }

        foreach ($request->input('car_insurance', []) as $file) {
            $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('car_insurance');
        }

        foreach ($request->input('ipo_vehicle', []) as $file) {
            $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('ipo_vehicle');
        }

        foreach ($request->input('vehicle_inspection', []) as $file) {
            $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('vehicle_inspection');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $document->id]);
        }

        return redirect()->route('admin.documents.index');
    }

    public function edit(Document $document)
    {
        abort_if(Gate::denies('document_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $document->load('driver');

        return view('admin.documents.edit', compact('document', 'drivers'));
    }

    public function update(UpdateDocumentRequest $request, Document $document)
    {
        $document->update($request->all());

        if (count($document->citizen_card) > 0) {
            foreach ($document->citizen_card as $media) {
                if (!in_array($media->file_name, $request->input('citizen_card', []))) {
                    $media->delete();
                }
            }
        }
        $media = $document->citizen_card->pluck('file_name')->toArray();
        foreach ($request->input('citizen_card', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('citizen_card');
            }
        }

        if (count($document->tvde_driver_certificate) > 0) {
            foreach ($document->tvde_driver_certificate as $media) {
                if (!in_array($media->file_name, $request->input('tvde_driver_certificate', []))) {
                    $media->delete();
                }
            }
        }
        $media = $document->tvde_driver_certificate->pluck('file_name')->toArray();
        foreach ($request->input('tvde_driver_certificate', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('tvde_driver_certificate');
            }
        }

        if (count($document->criminal_record) > 0) {
            foreach ($document->criminal_record as $media) {
                if (!in_array($media->file_name, $request->input('criminal_record', []))) {
                    $media->delete();
                }
            }
        }
        $media = $document->criminal_record->pluck('file_name')->toArray();
        foreach ($request->input('criminal_record', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('criminal_record');
            }
        }

        if ($request->input('profile_picture', false)) {
            if (!$document->profile_picture || $request->input('profile_picture') !== $document->profile_picture->file_name) {
                if ($document->profile_picture) {
                    $document->profile_picture->delete();
                }
                $document->addMedia(storage_path('tmp/uploads/' . basename($request->input('profile_picture'))))->toMediaCollection('profile_picture');
            }
        } elseif ($document->profile_picture) {
            $document->profile_picture->delete();
        }

        if (count($document->driving_license) > 0) {
            foreach ($document->driving_license as $media) {
                if (!in_array($media->file_name, $request->input('driving_license', []))) {
                    $media->delete();
                }
            }
        }
        $media = $document->driving_license->pluck('file_name')->toArray();
        foreach ($request->input('driving_license', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('driving_license');
            }
        }

        if (count($document->iban) > 0) {
            foreach ($document->iban as $media) {
                if (!in_array($media->file_name, $request->input('iban', []))) {
                    $media->delete();
                }
            }
        }
        $media = $document->iban->pluck('file_name')->toArray();
        foreach ($request->input('iban', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('iban');
            }
        }

        if (count($document->address) > 0) {
            foreach ($document->address as $media) {
                if (!in_array($media->file_name, $request->input('address', []))) {
                    $media->delete();
                }
            }
        }
        $media = $document->address->pluck('file_name')->toArray();
        foreach ($request->input('address', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('address');
            }
        }

        if (count($document->dua_vehicle) > 0) {
            foreach ($document->dua_vehicle as $media) {
                if (!in_array($media->file_name, $request->input('dua_vehicle', []))) {
                    $media->delete();
                }
            }
        }
        $media = $document->dua_vehicle->pluck('file_name')->toArray();
        foreach ($request->input('dua_vehicle', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('dua_vehicle');
            }
        }

        if (count($document->car_insurance) > 0) {
            foreach ($document->car_insurance as $media) {
                if (!in_array($media->file_name, $request->input('car_insurance', []))) {
                    $media->delete();
                }
            }
        }
        $media = $document->car_insurance->pluck('file_name')->toArray();
        foreach ($request->input('car_insurance', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('car_insurance');
            }
        }

        if (count($document->ipo_vehicle) > 0) {
            foreach ($document->ipo_vehicle as $media) {
                if (!in_array($media->file_name, $request->input('ipo_vehicle', []))) {
                    $media->delete();
                }
            }
        }
        $media = $document->ipo_vehicle->pluck('file_name')->toArray();
        foreach ($request->input('ipo_vehicle', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('ipo_vehicle');
            }
        }

        if (count($document->vehicle_inspection) > 0) {
            foreach ($document->vehicle_inspection as $media) {
                if (!in_array($media->file_name, $request->input('vehicle_inspection', []))) {
                    $media->delete();
                }
            }
        }
        $media = $document->vehicle_inspection->pluck('file_name')->toArray();
        foreach ($request->input('vehicle_inspection', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $document->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('vehicle_inspection');
            }
        }

        if ($request->notify_driver) {
            $driver = Driver::find($request->driver_id)->load('user');
            if ($driver->email) {
                $email = $driver->email;
            } else {
                $email = $driver->user->email;
            }
            $text = $request->notify_text;
            Notification::route('mail', $email)
                ->notify(new NewDocumentSend($text));
        }

        return redirect()->route('admin.documents.index');
    }

    public function show(Document $document)
    {
        abort_if(Gate::denies('document_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $document->load('driver');

        return view('admin.documents.show', compact('document'));
    }

    public function destroy(Document $document)
    {
        abort_if(Gate::denies('document_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $document->delete();

        return back();
    }

    public function massDestroy(MassDestroyDocumentRequest $request)
    {
        $documents = Document::find(request('ids'));

        foreach ($documents as $document) {
            $document->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('document_create') && Gate::denies('document_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new Document();
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
