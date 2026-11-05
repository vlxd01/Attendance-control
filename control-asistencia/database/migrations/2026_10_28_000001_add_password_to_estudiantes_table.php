<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Agrega la contraseña del portal del estudiante (se guarda encriptada con bcrypt)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            // nullable: los estudiantes que ya existían quedan sin contraseña hasta que el docente se la asigne
            $table->string('password')->nullable()->after('correo');
        });
    }

    public function down(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->dropColumn('password');
        });
    }
};
