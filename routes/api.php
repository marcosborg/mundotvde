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

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
});

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

