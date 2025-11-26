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
    }
}
