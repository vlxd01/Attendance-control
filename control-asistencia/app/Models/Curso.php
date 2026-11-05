<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $fillable = ['codigo', 'nombre', 'docente', 'descripcion'];

    // Un curso tiene muchos estudiantes inscritos (tabla pivote curso_estudiante)
    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class)->withTimestamps();
    }

    // Un curso tiene muchas asistencias
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }
}
