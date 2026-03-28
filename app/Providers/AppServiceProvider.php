<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Jeffgreco13\FilamentBreezy\Livewire\TwoFactorAuthentication;

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
    public function boot(): void
    {
        Product::observe(ProductObserver::class);
        Livewire::component('two_factor_authentication', TwoFactorAuthentication::class);


    }
}
