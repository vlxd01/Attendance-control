<?php

namespace Tests\Feature;

use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Estudiante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultaTest extends TestCase
{
    use RefreshDatabase;

    public function test_docente_ve_la_consulta_filtrada_por_fecha(): void
    {
        $curso = Curso::create(['codigo' => 'DAW-01', 'nombre' => 'Desarrollo Web']);
        $ana = Estudiante::create(['carnet' => 'AB123456', 'nombres' => 'Ana', 'apellidos' => 'Bonilla', 'correo' => 'ana@ufg.edu.sv']);
        $luis = Estudiante::create(['carnet' => 'LM654321', 'nombres' => 'Luis', 'apellidos' => 'Martínez', 'correo' => 'luis@ufg.edu.sv']);
        Asistencia::create(['curso_id' => $curso->id, 'estudiante_id' => $ana->id, 'fecha' => '2026-10-01', 'estado' => 'tarde', 'observacion' => 'Llegó 10 min tarde']);
        Asistencia::create(['curso_id' => $curso->id, 'estudiante_id' => $luis->id, 'fecha' => '2026-10-02', 'estado' => 'presente']);

        $this->iniciarSesion()->get('/asistencias/consulta?fecha=2026-10-01')
            ->assertOk()
            ->assertSee('Consulta por fecha')
            ->assertSee('AB123456')
            ->assertSee('Llegó 10 min tarde')
            ->assertDontSee('LM654321');

        // Un día sin registros muestra el mensaje vacío
        $this->get('/asistencias/consulta?fecha=2026-09-01')->assertSee('No hay asistencias registradas');
    }

    public function test_invitado_es_redirigido_al_login(): void
    {
        $this->get('/asistencias/consulta')->assertRedirect('/login');
    }
}
