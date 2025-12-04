<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Kategori;
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
        View::composer(['barang.create', 'barang.edit', 'barang.index'], function ($view) {
            $view->with('kategori', Kategori::all());
        });

        // Share user data dengan profile views
        View::composer(['profile.edit'], function ($view) {
            $user = auth()->user();
            $view->with('user', $user);
            $view->with('permintaanBarangCount', $user->permintaanBarang()->count());
            $view->with('penggunaanKendaraanCount', $user->penggunaanKendaraan()->count());
        });

        // Share categories dengan view lainnya (jika masih diperlukan)
        View::composer(['barang.create', 'barang.edit', 'barang.index'], function ($view) {
            $view->with('kategori', \App\Models\Kategori::all());
        });
    }
}
