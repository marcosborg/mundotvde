<?php

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Fuel;
use App\Models\Origin;
use App\Models\StandCar;
use App\Models\StandTvdeContact;
use App\Models\StandTvdePage;
use App\Models\StandTvdePub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VirtualAssistantController;
use App\Http\Controllers\Api\DriverInspectionController;
use App\Http\Controllers\Api\CompanyInspectionController;
use App\Http\Controllers\Api\InspectionCommonController;
use App\Http\Controllers\Api\InspectionAdminController;

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {});

Route::get('stand-cars', function () {
    return StandCar::all()->load([
        'brand',
        'car_model',
        'fuel',
        'month',
        'origin',
        'status',
    ]);
});

Route::get('stand-car/{id}', function ($id) {
    return StandCar::find($id)->load([
        'brand',
        'car_model',
        'fuel',
        'month',
        'origin',
        'status',
    ]);
});

Route::get('filter-elements', function () {
    $cars = StandCar::all();
    return [
        'brands' => Brand::all(),
        'models' => CarModel::all(),
        'fuels' => Fuel::all(),
        'origins' => Origin::all(),
        'kilometers' => [
            'min' => $cars->min('kilometers'),
            'max' => $cars->max('kilometers')
        ],
        'prices' => [
            'min' => $cars->min('price'),
            'max' => $cars->max('price')
        ]
    ];
});

Route::get('pages', function () {
    return StandTvdePage::all();
});

Route::get('page/{id}', function ($id) {
    return StandTvdePage::find($id);
});

Route::get('pubs', function () {
    return StandTvdePub::all();
});

Route::post('contact', function (Request $request) {

    $contact = new StandTvdeContact;
    $contact->car = $request->car;
    $contact->name = $request->name;
    $contact->email = $request->email;
    $contact->phone = $request->phone;
    $contact->subject = $request->subject;
    $contact->message = $request->message;
    $contact->save();

    return $contact;
});

//APP

Route::post('login', 'Api\\AuthController@login');
Route::prefix('app')->middleware(['auth:sanctum'])->group(function () {
    Route::get('user', 'Api\\AuthController@user');
    Route::get('admin', 'Api\\AppController@admin');
    Route::get('my-receipts', 'Api\\AppController@myReceipts');
    Route::get('reports', 'Api\\AppController@reports');
    Route::get('documents', 'Api\\AppController@documents');
    Route::post('send-receipt', 'Api\\AppController@sendReceipt');
    Route::get('my-documents', 'Api\\AppController@myDocuments');
    Route::post('send-document', 'Api\\AppController@sendDocument');
    Route::prefix('time-log')->group(function () {
        Route::get('last-time-log', 'Api\\AppController@lastTimeLog');
        Route::get('new-time-log/{status}', 'Api\\AppController@newTimeLog');
        Route::get('get-time-logs', 'Api\\AppController@getTimeLogs');
    });
    Route::get('contracts', 'Api\\AppController@contracts');
});

Route::get('app/reports/pdf/{activity_launch_id}', 'Api\\AppController@pdf');

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    // News
    Route::post('newss/media', 'NewsApiController@storeMedia')->name('newss.storeMedia');
    Route::apiResource('newss', 'NewsApiController');
});

Route::prefix('public')->group(function () {
    Route::get('home', 'Api\\PublicController@home');
    Route::get('article/{article_id}', 'Api\\PublicController@article');
    Route::get('cars', 'Api\\PublicController@cars');
    Route::get('car/{car_id}', 'Api\\PublicController@car');
    Route::get('stand-cars', 'Api\\PublicController@standCars');
    Route::post('car-rental-contact', 'Api\\PublicController@carRentalContact');
});

Route::get('/bot/{id}/instructions', 'Api\\BotController@getInstructions');
Route::get('/message', 'Api\\BotController@getMessage');
Route::post('/message', 'Api\\BotController@saveMessage');

Route::post('assistente-virtual', [VirtualAssistantController::class, 'handleMessage'])->name('assistente.virtual');
Route::get('website-messages/{email}', function ($email) {
    $message = \App\Models\WebsiteMessage::where('email', $email)->first();
    return $message ? json_decode($message->messages, true) : [];
});

Route::middleware('auth:sanctum')->post('assistente-motorista', [VirtualAssistantController::class, 'handleMotoristaMessage'])->name('assistente.motorista');

Route::middleware('auth:sanctum')->get('motorista-messages', function (Request $request) {
    $user = $request->user();
    $message = \App\Models\AppMessage::where('user_id', $user->id)->first();
    return $message ? json_decode($message->messages, true) : [];
});

Route::middleware('auth:sanctum')->prefix('driver/inspections')->group(function () {
    Route::get('next', [DriverInspectionController::class, 'next']);
    Route::post('{assignment}/start', [DriverInspectionController::class, 'start']);
    Route::post('{assignment}/photo', [DriverInspectionController::class, 'photo'])
        ->middleware('throttle:' . (int) env('INSPECTION_UPLOAD_RATE_LIMIT', 120) . ',1');
    Route::delete('{assignment}/photo/{photo}', [DriverInspectionController::class, 'deletePhoto']);
    Route::post('{assignment}/submit', [DriverInspectionController::class, 'submit']);
});

Route::middleware('auth:sanctum')->prefix('company/inspections')->group(function () {
    Route::get('dashboard', [InspectionAdminController::class, 'dashboard']);
    Route::post('settings', [InspectionAdminController::class, 'updateSettings']);
    Route::get('vehicles', [InspectionAdminController::class, 'vehicles']);
    Route::get('templates/select', [InspectionAdminController::class, 'templatesForSelect']);

    Route::get('templates', [CompanyInspectionController::class, 'templatesIndex']);
    Route::post('templates', [CompanyInspectionController::class, 'templatesStore']);
    Route::post('templates/{template}', [CompanyInspectionController::class, 'templatesUpdate']);
    Route::delete('templates/{template}', [CompanyInspectionController::class, 'templatesDestroy']);

    Route::get('schedules', [CompanyInspectionController::class, 'schedulesIndex']);
    Route::post('schedules', [CompanyInspectionController::class, 'schedulesStore']);
    Route::post('schedules/{schedule}', [CompanyInspectionController::class, 'schedulesUpdate']);
    Route::delete('schedules/{schedule}', [CompanyInspectionController::class, 'schedulesDestroy']);
    Route::get('vehicle/{vehicle}/schedules', [InspectionAdminController::class, 'schedulesForVehicle']);

    Route::get('assignments', [CompanyInspectionController::class, 'assignmentsIndex']);
    Route::post('{assignment}/review', [CompanyInspectionController::class, 'review']);
});

Route::middleware('auth:sanctum')->post('device-tokens/register', [InspectionCommonController::class, 'registerDeviceToken']);
Route::middleware('auth:sanctum')->get('notifications', [InspectionCommonController::class, 'notifications']);
Route::middleware('auth:sanctum')->get('inspections/photos/{photo}/signed-url', [InspectionCommonController::class, 'photoSignedUrl']);
Route::middleware(['auth:sanctum', 'signed'])->get('inspections/photos/{photo}/download', [InspectionCommonController::class, 'photoDownload'])
    ->name('api.inspections.photos.download');

use App\Http\Controllers\Api\PublicCrmFormsController;

Route::post('crm/forms/{slug}/submit', [PublicCrmFormsController::class, 'submit'])
    ->name('api.crm.forms.submit');
