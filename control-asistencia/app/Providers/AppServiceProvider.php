<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Compatibilidad con MySQL/MariaDB antiguos (por ejemplo, el de XAMPP):
        // limita la longitud por defecto de los textos para evitar el error
        // "Specified key was too long" al crear índices únicos.
        Schema::defaultStringLength(191);

        // Paginación con HTML sencillo (sin Tailwind ni Bootstrap)
        Paginator::defaultView('pagination::simple-default');
        Paginator::defaultSimpleView('pagination::simple-default');
    }
}
