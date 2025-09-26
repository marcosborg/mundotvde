<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use App\Models\CrmCard;
use App\Observers\CrmCardObserver;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        CrmCard::observe(CrmCardObserver::class);
        // EventServiceProvider: map CardStageChanged => QueueStageEmails
    }
}
