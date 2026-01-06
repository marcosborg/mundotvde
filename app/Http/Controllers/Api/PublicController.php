<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use App\Models\HomeInfo;
use App\Models\Article;
use App\Models\Car;
use App\Models\StandCar;
use Illuminate\Http\Request;
use App\Models\CarRentalContactRequest;
use Illuminate\Support\Facades\Notification;

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
        return Car::where('is_active', 1)->orderBy('position', 'asc')->get();
    }

    public function car($car_id)
    {
        return Car::where('is_active', 1)->find($car_id);
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

    public function carRentalContact(Request $request)
    {
        $request->validate([
            'city' => 'required|max:255',
            'email' => 'required|max:255|email',
            'name' => 'required|max:255',
            'phone' => 'required|max:255',
            'rgpd' => 'required'
        ], [], [
            'city' => 'Cidade',
            'email' => 'Email',
            'name' => 'Nome',
            'phone' => 'Telefone',
            'rgpd' => 'Autorizo o tratamento dos dados fornecidos'
        ]);

        $CarRentalContactRequest = new CarRentalContactRequest;
        $CarRentalContactRequest->car_id = $request->car_id;
        $CarRentalContactRequest->name = $request->name;
        $CarRentalContactRequest->phone = $request->phone;
        $CarRentalContactRequest->email = $request->email;
        $CarRentalContactRequest->city = $request->city;
        if ($request->tvde) {
            $CarRentalContactRequest->tvde = 1;
        }
        $CarRentalContactRequest->tvde_card = $request->tvde_card;
        $CarRentalContactRequest->message = $request->message;
        $CarRentalContactRequest->rgpd = 1;
        $CarRentalContactRequest->save();

        Notification::route('mail', 'info@mundotvde.pt')
            ->notify(new \App\Notifications\carRentalContact($CarRentalContactRequest));
    }
}
