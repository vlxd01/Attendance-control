<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Si alguien SIN sesión entra a una página protegida, va al login
        // (a la pestaña de estudiante si intentaba entrar al portal del estudiante)
        $middleware->redirectGuestsTo(fn (Request $request) => $request->is('estudiante', 'estudiante/*')
            ? route('login', ['tipo' => 'estudiante'])
            : route('login'));

        // Si alguien CON sesión abre el login, se envía a su propio panel
        $middleware->redirectUsersTo(fn () => Auth::guard('estudiante')->check()
            ? route('estudiante.panel')
            : route('dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
