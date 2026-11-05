<?php

namespace Tests\Feature;

use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Estudiante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JustificacionTest extends TestCase
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
    }

    public function test_justificado_sin_motivo_no_se_guarda(): void
    {
        $this->iniciarSesion()->post('/asistencias', [
            'curso_id' => $this->curso->id,
            'fecha' => '2026-10-01',
            'estados' => [$this->ana->id => 'justificado'],
            'observaciones' => [$this->ana->id => ''],
        ])->assertSessionHasErrors(['observaciones.' . $this->ana->id => 'Escribe el motivo de la justificación.']);

        $this->assertDatabaseCount('asistencias', 0);
    }

    public function test_justificado_con_motivo_se_guarda(): void
    {
        $this->iniciarSesion()->post('/asistencias', [
            'curso_id' => $this->curso->id,
            'fecha' => '2026-10-01',
            'estados' => [$this->ana->id => 'justificado'],
            'observaciones' => [$this->ana->id => 'Cita médica'],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('asistencias', [
            'estudiante_id' => $this->ana->id, 'estado' => 'justificado', 'observacion' => 'Cita médica',
        ]);
    }

    public function test_reporte_cuenta_justificados_aparte(): void
    {
        // 1 presente, 1 ausente, 2 justificados -> 25% de asistencia
        $estados = ['2026-10-01' => 'presente', '2026-10-05' => 'ausente', '2026-10-07' => 'justificado', '2026-10-12' => 'justificado'];
        foreach ($estados as $fecha => $estado) {
            Asistencia::create(['curso_id' => $this->curso->id, 'estudiante_id' => $this->ana->id, 'fecha' => $fecha, 'estado' => $estado, 'observacion' => 'Motivo']);
        }

        $this->iniciarSesion()->get('/reportes?curso_id=' . $this->curso->id)
            ->assertOk()
            ->assertSee('Justificados')
            ->assertSeeInOrder(['<td class="num">1</td>', '<td class="num">0</td>', '<td class="num">1</td>', '<td class="num">2</td>', '<td class="num">4</td>'], false)
            ->assertSee('25%');
    }
}
