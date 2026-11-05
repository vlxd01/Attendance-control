<?php

namespace Tests\Feature;

use App\Models\Estudiante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EstudianteTest extends TestCase
{
    use RefreshDatabase;

    private function datos($cambios = [])
    {
        return array_merge([
            'carnet' => 'rr123456', // en minúsculas: el controlador lo convierte a mayúsculas
            'nombres' => 'Jonathan',
            'apellidos' => 'Rodríguez',
            'correo' => 'jonathan@ufg.edu.sv',
            'password' => 'secreta123',
        ], $cambios);
    }

    public function test_lista_de_estudiantes(): void
    {
        Estudiante::create(['carnet' => 'AB123456', 'nombres' => 'Ana', 'apellidos' => 'Bonilla', 'correo' => 'ana@ufg.edu.sv']);

        $this->iniciarSesion()->get('/estudiantes')->assertOk()->assertSee('AB123456');
    }

    public function test_crear_estudiante(): void
    {
        $this->iniciarSesion()->post('/estudiantes', $this->datos())
            ->assertRedirect('/estudiantes')
            ->assertSessionHas('ok');

        $this->assertDatabaseHas('estudiantes', ['carnet' => 'RR123456', 'nombres' => 'Jonathan']);

        // La contraseña se guarda encriptada, nunca en texto plano
        $estudiante = Estudiante::where('carnet', 'RR123456')->first();
        $this->assertNotEquals('secreta123', $estudiante->password);
        $this->assertTrue(Hash::check('secreta123', $estudiante->password));
    }

    public function test_la_contrasena_es_obligatoria_al_crear(): void
    {
        $this->iniciarSesion()->post('/estudiantes', $this->datos(['password' => '']))
            ->assertSessionHasErrors('password');
    }

    public function test_al_editar_la_contrasena_es_opcional(): void
    {
        $estudiante = Estudiante::create($this->datos(['carnet' => 'RR123456']));
        $this->iniciarSesion();

        // Sin contraseña: se conserva la anterior
        $this->put("/estudiantes/{$estudiante->id}", $this->datos(['password' => '']))->assertRedirect('/estudiantes');
        $this->assertTrue(Hash::check('secreta123', $estudiante->fresh()->password));

        // Con contraseña nueva: se cambia
        $this->put("/estudiantes/{$estudiante->id}", $this->datos(['password' => 'nueva123']))->assertRedirect('/estudiantes');
        $this->assertTrue(Hash::check('nueva123', $estudiante->fresh()->password));
    }

    public function test_validacion_en_espanol_y_carnet_unico(): void
    {
        $this->iniciarSesion();
        $this->post('/estudiantes', $this->datos());

        // Mismo carnet otra vez -> error de "único"
        $this->post('/estudiantes', $this->datos(['correo' => 'otro@ufg.edu.sv']))
            ->assertSessionHasErrors(['carnet' => 'El carnet ya está registrado.']);

        // Campos vacíos y carnet con formato incorrecto
        $this->post('/estudiantes', ['carnet' => '123'])
            ->assertSessionHasErrors([
                'carnet' => 'El carnet debe tener 2 letras y 6 números (ej. RR123456).',
                'nombres' => 'El campo nombres es obligatorio.',
            ]);

        $this->assertDatabaseCount('estudiantes', 1);
    }

    public function test_editar_estudiante(): void
    {
        $estudiante = Estudiante::create($this->datos(['carnet' => 'RR123456']));

        $this->iniciarSesion()->put("/estudiantes/{$estudiante->id}", $this->datos(['nombres' => 'Jonathan Alexander']))
            ->assertRedirect('/estudiantes');

        $this->assertDatabaseHas('estudiantes', ['id' => $estudiante->id, 'nombres' => 'Jonathan Alexander']);
    }

    public function test_eliminar_estudiante(): void
    {
        $estudiante = Estudiante::create($this->datos(['carnet' => 'RR123456']));

        $this->iniciarSesion()->delete("/estudiantes/{$estudiante->id}")->assertRedirect('/estudiantes');

        $this->assertDatabaseMissing('estudiantes', ['id' => $estudiante->id]);
    }
}
