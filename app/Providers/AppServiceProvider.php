<?php

namespace App\Providers;

use App\Models\Category;
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

    public function boot()
    {
        // Share categories dengan semua view yang membutuhkan
        View::composer(['items.create', 'items.edit', 'items.index'], function ($view) {
            $view->with('categories', Category::all());
        });

        // Share user data dengan profile views
        View::composer(['profile.edit'], function ($view) {
            $user = auth()->user();
            $view->with('user', $user);
            $view->with('itemRequestsCount', $user->itemRequests()->count());
            $view->with('vehicleUsageCount', $user->vehicleUsage()->count());
        });

        // Share categories dengan view lainnya (jika masih diperlukan)
        View::composer(['items.create', 'items.edit', 'items.index'], function ($view) {
            $view->with('categories', \App\Models\Category::all());
        });
    }
}
