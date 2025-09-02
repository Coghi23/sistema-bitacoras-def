<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\User;
use App\Observers\UserObserver;
use App\View\Composers\SidebarComposer;

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
        // Registrar el observer para automáticamente crear perfiles de profesor
        User::observe(UserObserver::class);
        
        // Registrar el View Composer para el sidebar
        View::composer('Template-administrador', SidebarComposer::class);
    }
}
