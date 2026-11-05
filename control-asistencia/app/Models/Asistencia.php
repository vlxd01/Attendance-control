<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $fillable = ['curso_id', 'estudiante_id', 'fecha', 'estado', 'observacion'];

    // Los cuatro estados posibles de una asistencia
    // (justificado = faltó con permiso; el motivo se guarda en "observacion")
    const ESTADOS = ['presente', 'ausente', 'tarde', 'justificado'];

    // Cada asistencia pertenece a un curso
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    // Cada asistencia pertenece a un estudiante
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    // Devuelve la fecha en formato dd/mm/aaaa para mostrarla en las vistas
    public function getFechaFormateadaAttribute()
    {
        return Carbon::parse($this->fecha)->format('d/m/Y');
    }
}
