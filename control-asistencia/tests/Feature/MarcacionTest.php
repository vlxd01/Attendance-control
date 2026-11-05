<?php

namespace Tests\Feature;

use App\Models\Estudiante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Vista del docente: /marcaciones
class MarcacionTest extends TestCase
{
    use RefreshDatabase;

    private function datos()
    {
        $ana = Estudiante::create(['carnet' => 'AB123456', 'nombres' => 'Ana', 'apellidos' => 'Bonilla', 'correo' => 'ana@ufg.edu.sv']);
        $luis = Estudiante::create(['carnet' => 'LA301224', 'nombres' => 'Luis', 'apellidos' => 'López', 'correo' => 'luis@ufg.edu.sv']);

        $ana->marcaciones()->create(['fecha' => now()->toDateString(), 'hora_entrada' => '07:10:00', 'hora_salida' => '12:00:00']);
        $luis->marcaciones()->create(['fecha' => now()->toDateString(), 'hora_entrada' => '07:30:00']);
        $luis->marcaciones()->create(['fecha' => '2026-10-20', 'hora_entrada' => '07:05:00', 'hora_salida' => '11:35:00']);

        return [$ana, $luis];
    }

    public function test_un_invitado_no_ve_las_marcaciones(): void
    {
        $this->get('/marcaciones')->assertRedirect('/login');
    }

    public function test_por_defecto_muestra_las_de_hoy(): void
    {
        $this->datos();

        $this->iniciarSesion()->get('/marcaciones')
            ->assertOk()
            ->assertSee('AB123456')->assertSee('LA301224')
            ->assertSee('07:10')->assertSee('12:00')->assertSee('4 h 50 min')
            ->assertSee('En clase')          // Luis aún no marca salida
            ->assertDontSee('07:05');        // la del 20/10 no aparece
    }

    public function test_filtrar_por_fecha_y_estudiante(): void
    {
        [$ana, $luis] = $this->datos();
        $this->iniciarSesion();

        $this->get('/marcaciones?fecha=2026-10-20')
            ->assertSee('07:05')->assertSee('4 h 30 min')->assertDontSee('07:10');

        // Fecha vacía + estudiante = todo el historial de ese estudiante
        $this->get("/marcaciones?fecha=&estudiante_id={$luis->id}")
            ->assertSee('07:05')->assertSee('07:30')->assertDontSee('07:10');
    }

    public function test_fecha_invalida_no_rompe_la_pagina(): void
    {
        $this->iniciarSesion()->get('/marcaciones?fecha=abc')->assertOk();
    }

    public function test_el_dashboard_muestra_entradas_de_hoy(): void
    {
        $this->datos();
        $this->iniciarSesion()->get('/')->assertOk()->assertSee('Entradas de hoy');
    }
}
