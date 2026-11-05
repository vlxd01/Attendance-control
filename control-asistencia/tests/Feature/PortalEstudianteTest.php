<?php

namespace Tests\Feature;

use App\Models\Estudiante;
use App\Models\Marcacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PortalEstudianteTest extends TestCase
{
    use RefreshDatabase;

    private function crearEstudiante()
    {
        return Estudiante::create([
            'carnet' => 'RH100124', 'nombres' => 'Jonathan', 'apellidos' => 'Rodríguez',
            'correo' => 'jonathan@ufg.edu.sv', 'password' => 'estudiante123',
        ]);
    }

    // ---------- Inicio de sesión ----------

    public function test_el_login_muestra_las_dos_pestanas(): void
    {
        $this->get('/login')->assertOk()->assertSee('Docente')->assertSee('Estudiante');
        $this->get('/login?tipo=estudiante')->assertOk()->assertSee('Carnet')->assertSee('Entrar al portal');
    }

    public function test_el_estudiante_inicia_sesion_con_carnet_y_contrasena(): void
    {
        $estudiante = $this->crearEstudiante();

        // El carnet en minúsculas también funciona (se convierte a mayúsculas)
        $this->post('/estudiante/login', ['carnet' => 'rh100124', 'password' => 'estudiante123'])
            ->assertRedirect(route('estudiante.panel'));

        $this->assertAuthenticatedAs($estudiante, 'estudiante');
        $this->assertGuest('web'); // NO queda como docente
    }

    public function test_contrasena_incorrecta_del_estudiante(): void
    {
        $this->crearEstudiante();

        $this->post('/estudiante/login', ['carnet' => 'RH100124', 'password' => 'otra'])
            ->assertSessionHasErrors(['carnet' => 'El carnet o la contraseña son incorrectos.']);

        $this->assertGuest('estudiante');
    }

    public function test_estudiante_sin_contrasena_no_puede_entrar(): void
    {
        Estudiante::create(['carnet' => 'AB123456', 'nombres' => 'Ana', 'apellidos' => 'Bonilla', 'correo' => 'ana@ufg.edu.sv']);

        $this->post('/estudiante/login', ['carnet' => 'AB123456', 'password' => ''])->assertSessionHasErrors('password');
        $this->assertGuest('estudiante');
    }

    public function test_el_estudiante_cierra_sesion(): void
    {
        $this->actingAs($this->crearEstudiante(), 'estudiante')
            ->post('/estudiante/salir')
            ->assertRedirect(route('login', ['tipo' => 'estudiante']));

        $this->assertGuest('estudiante');
    }

    // ---------- Separación de áreas ----------

    public function test_un_invitado_no_entra_al_portal(): void
    {
        $this->get('/estudiante')->assertRedirect(route('login', ['tipo' => 'estudiante']));
    }

    public function test_el_estudiante_no_puede_entrar_al_panel_del_docente(): void
    {
        $this->actingAs($this->crearEstudiante(), 'estudiante');

        foreach (['/', '/estudiantes', '/cursos', '/asistencias', '/reportes', '/marcaciones'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
        // Y si abre el login, se le devuelve a su propio panel
        $this->get('/login')->assertRedirect(route('estudiante.panel'));
    }

    public function test_el_docente_no_puede_entrar_al_portal_del_estudiante(): void
    {
        $this->crearEstudiante();
        $this->iniciarSesion();

        $this->get('/estudiante')->assertRedirect(route('login', ['tipo' => 'estudiante']));
        $this->post('/estudiante/entrada')->assertRedirect(route('login', ['tipo' => 'estudiante']));
        $this->assertDatabaseCount('marcaciones', 0);
    }

    // ---------- Marcar entrada y salida ----------

    public function test_el_panel_muestra_marcar_entrada(): void
    {
        $this->actingAs($this->crearEstudiante(), 'estudiante')
            ->get('/estudiante')
            ->assertOk()
            ->assertSee('Hola, Jonathan')
            ->assertSee('Marcar entrada')
            ->assertDontSee('Marcar salida');
    }

    public function test_marcar_entrada_y_salida(): void
    {
        $estudiante = $this->crearEstudiante();
        $this->actingAs($estudiante, 'estudiante');

        Carbon::setTestNow('2026-10-27 07:15:00');
        $this->from('/estudiante')->post('/estudiante/entrada')->assertRedirect('/estudiante')->assertSessionHas('ok');
        $this->get('/estudiante')->assertSee('Marcar salida')->assertDontSee('Marcar entrada');

        Carbon::setTestNow('2026-10-27 11:45:00');
        $this->from('/estudiante')->post('/estudiante/salida')->assertSessionHas('ok');

        $marcacion = Marcacion::first();
        $this->assertEquals('2026-10-27', date('Y-m-d', strtotime($marcacion->fecha)));
        $this->assertEquals('07:15', $marcacion->entrada);
        $this->assertEquals('11:45', $marcacion->salida);
        $this->assertEquals('4 h 30 min', $marcacion->horas);

        // Ya no se muestra ningún botón, y el historial tiene las horas
        $this->get('/estudiante')->assertDontSee('Marcar entrada')->assertDontSee('Marcar salida')->assertSee('4 h 30 min');
        Carbon::setTestNow();
    }

    public function test_no_se_puede_marcar_entrada_dos_veces(): void
    {
        $this->actingAs($this->crearEstudiante(), 'estudiante');

        $this->post('/estudiante/entrada');
        $this->post('/estudiante/entrada')->assertSessionHas('error', 'Ya marcaste tu entrada hoy.');

        $this->assertDatabaseCount('marcaciones', 1);
    }

    public function test_no_se_puede_marcar_salida_sin_entrada(): void
    {
        $this->actingAs($this->crearEstudiante(), 'estudiante');

        $this->post('/estudiante/salida')->assertSessionHas('error', 'Primero debes marcar tu entrada.');

        $this->assertDatabaseCount('marcaciones', 0);
    }

    public function test_no_se_puede_marcar_salida_dos_veces(): void
    {
        $this->actingAs($this->crearEstudiante(), 'estudiante');

        Carbon::setTestNow('2026-10-27 07:00:00');
        $this->post('/estudiante/entrada');
        Carbon::setTestNow('2026-10-27 12:00:00');
        $this->post('/estudiante/salida');
        Carbon::setTestNow('2026-10-27 13:00:00');
        $this->post('/estudiante/salida')->assertSessionHas('error', 'Ya marcaste tu salida hoy.');

        $this->assertEquals('12:00', Marcacion::first()->salida); // no cambió
        Carbon::setTestNow();
    }

    public function test_marcaciones_de_otro_dia_no_bloquean_hoy(): void
    {
        $estudiante = $this->crearEstudiante();
        $estudiante->marcaciones()->create(['fecha' => now()->subDay()->toDateString(), 'hora_entrada' => '07:00:00', 'hora_salida' => '12:00:00']);

        $this->actingAs($estudiante, 'estudiante')->post('/estudiante/entrada')->assertSessionHas('ok');
        $this->assertDatabaseCount('marcaciones', 2);
    }
}
