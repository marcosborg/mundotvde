<?php
use App\Models\StandCar;

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
});

Route::get('stand-cars', function () {
    return StandCar::all();
});