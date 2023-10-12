<?php
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Fuel;
use App\Models\Origin;
use App\Models\StandCar;

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