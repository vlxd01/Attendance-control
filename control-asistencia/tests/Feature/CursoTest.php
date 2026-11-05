<?php

namespace Tests\Feature;

use App\Models\Curso;
use App\Models\Estudiante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CursoTest extends TestCase
{
    use RefreshDatabase;

    public function test_crear_editar_y_eliminar_curso(): void
    {
        $this->iniciarSesion();

        $this->post('/cursos', ['codigo' => 'daw-01', 'nombre' => 'Desarrollo Web', 'docente' => 'Ing. Pérez'])
            ->assertRedirect();
        $curso = Curso::first();
        $this->assertEquals('DAW-01', $curso->codigo);

        $this->get('/cursos')->assertOk()->assertSee('Desarrollo Web');

        $this->put("/cursos/{$curso->id}", ['codigo' => 'DAW-01', 'nombre' => 'Desarrollo Web II'])
            ->assertRedirect('/cursos');
        $this->assertDatabaseHas('cursos', ['nombre' => 'Desarrollo Web II']);

        $this->delete("/cursos/{$curso->id}")->assertRedirect('/cursos');
        $this->assertDatabaseCount('cursos', 0);
    }

    public function test_el_nombre_es_obligatorio(): void
    {
        $this->iniciarSesion()->post('/cursos', ['codigo' => 'X-1'])
            ->assertSessionHasErrors(['nombre' => 'El campo nombre es obligatorio.']);
    }

    public function test_inscribir_estudiantes_en_un_curso(): void
    {
        $curso = Curso::create(['codigo' => 'DAW-01', 'nombre' => 'Desarrollo Web']);
        $ana = Estudiante::create(['carnet' => 'AB123456', 'nombres' => 'Ana', 'apellidos' => 'Bonilla', 'correo' => 'ana@ufg.edu.sv']);
        $luis = Estudiante::create(['carnet' => 'LC123456', 'nombres' => 'Luis', 'apellidos' => 'Cruz', 'correo' => 'luis@ufg.edu.sv']);

        $this->iniciarSesion()->put("/cursos/{$curso->id}/inscripciones", ['estudiantes' => [$ana->id, $luis->id]])
            ->assertRedirect("/cursos/{$curso->id}");
        $this->assertEquals(2, $curso->estudiantes()->count());

        // Al desmarcar a Luis, sync() lo quita del curso
        $this->put("/cursos/{$curso->id}/inscripciones", ['estudiantes' => [$ana->id]]);
        $this->assertEquals([$ana->id], $curso->estudiantes()->pluck('estudiantes.id')->toArray());

        $this->get("/cursos/{$curso->id}")->assertOk()->assertSee('Bonilla, Ana');
    }
}
