<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabla pivote (inscripciones): relación muchos a muchos entre cursos y estudiantes.
// Un estudiante puede estar en varios cursos y un curso tiene varios estudiantes.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curso_estudiante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->timestamps();

            // Un estudiante no puede inscribirse dos veces en el mismo curso
            $table->unique(['curso_id', 'estudiante_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curso_estudiante');
    }
};
