<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabla de estudiantes: guarda los datos básicos de cada alumno.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string('carnet', 8)->unique();   // Ej: RR123456 (no se puede repetir)
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('correo', 150)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
