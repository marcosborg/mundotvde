<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class CarsController extends Controller
{
    public function index()
    {

        $cars = Car::orderBy('position', 'asc')->get();

        return view('website.cars')->with('cars', $cars);
    }
}