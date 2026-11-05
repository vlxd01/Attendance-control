<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marcacion extends Model
{
    // Laravel buscaría "marcacions"; por eso se indica el nombre de la tabla
    protected $table = 'marcaciones';

    protected $fillable = ['estudiante_id', 'fecha', 'hora_entrada', 'hora_salida'];

    // Cada marcación pertenece a un estudiante
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    // Minutos entre la entrada y la salida (null si aún no marca salida)
    public function getMinutosAttribute()
    {
        if (! $this->hora_salida) {
            return null;
        }
        return intdiv(strtotime($this->hora_salida) - strtotime($this->hora_entrada), 60);
    }

    // Horas trabajadas en texto, por ejemplo "4 h 35 min"
    public function getHorasAttribute()
    {
        return $this->minutos === null ? '—' : self::formatoHoras($this->minutos);
    }

    // Convierte minutos a "X h YY min" (se usa también para los totales)
    public static function formatoHoras($minutos)
    {
        return intdiv($minutos, 60) . ' h ' . str_pad($minutos % 60, 2, '0', STR_PAD_LEFT) . ' min';
    }

    // Fecha en formato dd/mm/aaaa
    public function getFechaFormateadaAttribute()
    {
        return date('d/m/Y', strtotime($this->fecha));
    }

    // Horas sin segundos: "07:45"
    public function getEntradaAttribute()
    {
        return substr($this->hora_entrada, 0, 5);
    }

    public function getSalidaAttribute()
    {
        return $this->hora_salida ? substr($this->hora_salida, 0, 5) : null;
    }
}
