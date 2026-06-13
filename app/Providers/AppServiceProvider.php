<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        View::composer('layouts.admin', function ($view) {
            $blangkoStock = optional(\App\Models\BlangkoAvailability::orderByDesc('created_at')->first())->jumlah_total ?? 0;
            $blangkoThreshold = 10;
            $blangkoWarning = $blangkoStock < $blangkoThreshold;
            $latestRequests = \App\Models\DocumentRequest::with('user')->latest()->take(3)->get();

            $view->with(compact('blangkoWarning', 'blangkoStock', 'latestRequests'));
        });
    }
}