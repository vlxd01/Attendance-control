<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // Inicia sesión con un docente de prueba (se usa en casi todos los tests)
    protected function iniciarSesion()
    {
        $this->actingAs(User::factory()->create());
        return $this;
    }
}
