<?php

namespace Tests\Feature;

use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Estudiante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReporteTest extends TestCase
{
    use RefreshDatabase;

    private $curso;
    private $ana;

    protected function setUp(): void
    {
        parent::setUp();
        $this->curso = Curso::create(['codigo' => 'DAW-01', 'nombre' => 'Desarrollo Web']);
        $this->ana = Estudiante::create(['carnet' => 'AB123456', 'nombres' => 'Ana', 'apellidos' => 'Bonilla', 'correo' => 'ana@ufg.edu.sv']);
        $this->curso->estudiantes()->attach($this->ana->id);

        // 4 clases: 2 presentes, 1 tarde, 1 ausente -> 75% de asistencia
        $estados = ['2026-10-01' => 'presente', '2026-10-05' => 'presente', '2026-10-07' => 'tarde', '2026-10-12' => 'ausente'];
        foreach ($estados as $fecha => $estado) {
            Asistencia::create(['curso_id' => $this->curso->id, 'estudiante_id' => $this->ana->id, 'fecha' => $fecha, 'estado' => $estado]);
        }
    }

    public function test_reporte_por_curso_calcula_el_porcentaje(): void
    {
        $this->iniciarSesion()->get('/reportes?curso_id=' . $this->curso->id)
            ->assertOk()
            ->assertSee('Bonilla, Ana')
            ->assertSee('75%');
    }

    public function test_reporte_con_rango_de_fechas(): void
    {
        // Solo del 01 al 07: 2 presentes + 1 tarde de 3 clases = 100%
        $this->iniciarSesion()->get('/reportes?curso_id=' . $this->curso->id . '&desde=2026-10-01&hasta=2026-10-07')
            ->assertOk()
            ->assertSee('100%');
    }

    public function test_exportar_csv(): void
    {
        $respuesta = $this->iniciarSesion()->get('/reportes/csv?curso_id=' . $this->curso->id);

        $respuesta->assertOk();
        $this->assertStringContainsString('AB123456', $respuesta->streamedContent());
    }

    public function test_historial_del_estudiante(): void
    {
        $this->iniciarSesion()->get('/reportes/estudiante/' . $this->ana->id)
            ->assertOk()
            ->assertSee('Desarrollo Web')
            ->assertSee('75%');
    }

    public function test_rango_de_fechas_invertido_muestra_error_sin_bucle(): void
    {
        $this->iniciarSesion()->get('/reportes?curso_id=' . $this->curso->id . '&desde=2026-10-20&hasta=2026-10-01')
            ->assertRedirect('/reportes')
            ->assertSessionHasErrors('hasta');

        $this->get('/reportes')->assertOk();
    }
}
