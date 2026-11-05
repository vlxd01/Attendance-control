<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Marcaciones de entrada y salida que hace cada estudiante desde su portal
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marcaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->date('fecha');
            $table->time('hora_entrada');
            $table->time('hora_salida')->nullable(); // vacía hasta que marque la salida
            $table->timestamps();

            // Una sola marcación por estudiante y por día
            $table->unique(['estudiante_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marcaciones');
    }
};
