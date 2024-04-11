<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use Illuminate\Http\Request;

class CouriersController extends Controller
{
    public function index($id = 1)
    {
        $courier = Courier::find($id);

        return view('website.couriers')->with('courier', $courier);
    }
}