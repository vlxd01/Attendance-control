<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabla de asistencias: un registro por estudiante, por curso y por fecha.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->date('fecha');
            $table->enum('estado', ['presente', 'ausente', 'tarde']);
            $table->string('observacion', 255)->nullable();
            $table->timestamps();

            // Evita duplicados: solo un registro por curso + estudiante + fecha
            $table->unique(['curso_id', 'estudiante_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
