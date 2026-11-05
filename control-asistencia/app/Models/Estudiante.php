<?php

namespace App\Models;

// Authenticatable permite que el estudiante inicie sesión (igual que el modelo User)
use Illuminate\Foundation\Auth\User as Authenticatable;

class Estudiante extends Authenticatable
{
    // Campos que se pueden llenar desde un formulario (asignación masiva)
    protected $fillable = ['carnet', 'nombres', 'apellidos', 'correo', 'password'];

    // La contraseña nunca se muestra al convertir el modelo a arreglo/JSON
    protected $hidden = ['password'];

    // 'hashed': Laravel encripta la contraseña automáticamente al guardarla
    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    // Un estudiante pertenece a muchos cursos (tabla pivote curso_estudiante)
    public function cursos()
    {
        return $this->belongsToMany(Curso::class)->withTimestamps();
    }

    // Un estudiante tiene muchas asistencias
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    // Un estudiante tiene muchas marcaciones de entrada y salida
    public function marcaciones()
    {
        return $this->hasMany(Marcacion::class);
    }

    // Atributo calculado: $estudiante->nombre_completo
    public function getNombreCompletoAttribute()
    {
        return $this->apellidos . ', ' . $this->nombres;
    }
}
