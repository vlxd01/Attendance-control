<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Agrega el estado "justificado" a la columna estado de asistencias.
// El motivo de la justificación se guarda en la columna "observacion" que ya existe.
// ->change() funciona igual en MariaDB/MySQL y en SQLite (la BD de los tests).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->enum('estado', ['presente', 'ausente', 'tarde', 'justificado'])->change();
        });
    }

    public function down(): void
    {
        // Antes de quitar el estado, los justificados pasan a ausente
        DB::table('asistencias')->where('estado', 'justificado')->update(['estado' => 'ausente']);

        Schema::table('asistencias', function (Blueprint $table) {
            $table->enum('estado', ['presente', 'ausente', 'tarde'])->change();
        });
    }
};
