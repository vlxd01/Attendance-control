<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutenticacionTest extends TestCase
{
    use RefreshDatabase; // Base de datos limpia (sqlite en memoria) en cada test

    public function test_un_invitado_es_redirigido_al_login(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/estudiantes')->assertRedirect('/login');
    }

    public function test_la_pagina_de_login_carga(): void
    {
        $this->get('/login')->assertOk()->assertSee('Iniciar sesión');
    }

    public function test_el_docente_puede_iniciar_sesion(): void
    {
        User::factory()->create(['email' => 'admin@asistencia.com', 'password' => bcrypt('password')]);

        $this->post('/login', ['email' => 'admin@asistencia.com', 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_contrasena_incorrecta_no_inicia_sesion(): void
    {
        User::factory()->create(['email' => 'admin@asistencia.com', 'password' => bcrypt('password')]);

        $this->post('/login', ['email' => 'admin@asistencia.com', 'password' => 'otra'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_el_dashboard_carga_con_sesion(): void
    {
        $this->iniciarSesion()->get('/')->assertOk()->assertSee('Asistencia general');
    }
}
