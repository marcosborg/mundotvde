<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use App\Models\HomeInfo;
use App\Models\Article;
use App\Models\Car;
use App\Models\StandCar;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $hero_banner = HeroBanner::first();
        $home_info = HomeInfo::first();
        $articles = Article::where('active', 1)->orderBy('id')->limit(3)->get();

        return response()->json([
            'hero_banner' => $hero_banner,
            'home_info' => $home_info,
            'articles' => $articles,
        ]);
    }

    public function article($article_id)
    {
        return Article::find($article_id);
    }

    public function cars()
    {
        return Car::all();
    }

    public function standCars()
    {
        return StandCar::all()->load([
            'brand',
            'car_model',
            'fuel',
            'month',
            'origin',
            'status',
        ]);
    }
}
