<?php

namespace Tests\Feature;

use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Estudiante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsistenciaTest extends TestCase
{
    use RefreshDatabase;

    private $curso;
    private $ana;
    private $luis;

    protected function setUp(): void
    {
        parent::setUp();
        $this->curso = Curso::create(['codigo' => 'DAW-01', 'nombre' => 'Desarrollo Web']);
        $this->ana = Estudiante::create(['carnet' => 'AB123456', 'nombres' => 'Ana', 'apellidos' => 'Bonilla', 'correo' => 'ana@ufg.edu.sv']);
        $this->luis = Estudiante::create(['carnet' => 'LC123456', 'nombres' => 'Luis', 'apellidos' => 'Cruz', 'correo' => 'luis@ufg.edu.sv']);
        $this->curso->estudiantes()->attach([$this->ana->id, $this->luis->id]);
    }

    public function test_muestra_la_lista_de_inscritos(): void
    {
        $this->iniciarSesion()
            ->get('/asistencias?curso_id=' . $this->curso->id . '&fecha=2026-10-01')
            ->assertOk()
            ->assertSee('Bonilla, Ana')
            ->assertSee('Cruz, Luis');
    }

    public function test_guardar_asistencia(): void
    {
        $this->iniciarSesion()->post('/asistencias', [
            'curso_id' => $this->curso->id,
            'fecha' => '2026-10-01',
            'estados' => [$this->ana->id => 'presente', $this->luis->id => 'tarde'],
            'observaciones' => [$this->luis->id => 'Llegó 10 min tarde'],
        ])->assertRedirect()->assertSessionHas('ok');

        $this->assertDatabaseCount('asistencias', 2);
        $this->assertDatabaseHas('asistencias', ['estudiante_id' => $this->luis->id, 'estado' => 'tarde', 'observacion' => 'Llegó 10 min tarde']);
    }

    public function test_editar_una_fecha_no_duplica_registros(): void
    {
        $this->iniciarSesion();
        $datos = ['curso_id' => $this->curso->id, 'fecha' => '2026-10-01'];

        $this->post('/asistencias', $datos + ['estados' => [$this->ana->id => 'presente', $this->luis->id => 'presente']]);
        $this->post('/asistencias', $datos + ['estados' => [$this->ana->id => 'ausente', $this->luis->id => 'presente']]);

        // Siguen siendo 2 registros, pero Ana ahora aparece ausente
        $this->assertDatabaseCount('asistencias', 2);
        $this->assertEquals('ausente', Asistencia::where('estudiante_id', $this->ana->id)->first()->estado);
    }

    public function test_estado_invalido_y_fecha_futura_son_rechazados(): void
    {
        $this->iniciarSesion()->post('/asistencias', [
            'curso_id' => $this->curso->id,
            'fecha' => now()->addDays(3)->toDateString(),
            'estados' => [$this->ana->id => 'dormido'],
        ])->assertSessionHasErrors(['fecha', 'estados.' . $this->ana->id]);

        $this->assertDatabaseCount('asistencias', 0);
    }

    public function test_no_guarda_estudiantes_no_inscritos(): void
    {
        $otro = Estudiante::create(['carnet' => 'ZZ999999', 'nombres' => 'Otro', 'apellidos' => 'Alumno', 'correo' => 'otro@ufg.edu.sv']);

        $this->iniciarSesion()->post('/asistencias', [
            'curso_id' => $this->curso->id,
            'fecha' => '2026-10-01',
            'estados' => [$otro->id => 'presente'],
        ]);

        $this->assertDatabaseCount('asistencias', 0);
    }

    public function test_fecha_invalida_en_la_url_usa_la_de_hoy(): void
    {
        $this->iniciarSesion()->get('/asistencias?curso_id=' . $this->curso->id . '&fecha=abc')
            ->assertOk()
            ->assertSee(now()->format('d/m/Y'));
    }
}
