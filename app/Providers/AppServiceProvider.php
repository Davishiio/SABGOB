<?php

namespace App\Providers;

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
    public function boot(): void
    {
        \Illuminate\Database\Eloquent\Relations\Relation::enforceMorphMap([
            'Proyecto' => 'App\Models\Proyecto',
            'Tarea' => 'App\Models\Tarea',
            'Subtarea' => 'App\Models\Subtarea',
            'User' => 'App\Models\User', // Necesario para Sanctum (tokens)
        ]);
    }
}
