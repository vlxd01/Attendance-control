<?php

namespace App\Http\Controllers;

use App\Models\Marcacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Portal del estudiante: ver su día, marcar entrada/salida y ver su historial
class PortalEstudianteController extends Controller
{
    public function panel()
    {
        $estudiante = Auth::guard('estudiante')->user();

        // Marcación de hoy (puede no existir todavía)
        $hoy = $estudiante->marcaciones()->where('fecha', now()->toDateString())->first();

        // Historial de marcaciones, la más reciente primero
        $historial = $estudiante->marcaciones()->orderByDesc('fecha')->get();
        $totalMinutos = $historial->sum('minutos');

        return view('estudiante.panel', compact('estudiante', 'hoy', 'historial', 'totalMinutos'));
    }

    // Marcar entrada: solo una vez por día
    public function marcarEntrada()
    {
        $estudiante = Auth::guard('estudiante')->user();
        $fecha = now()->toDateString();

        if ($estudiante->marcaciones()->where('fecha', $fecha)->exists()) {
            return back()->with('error', 'Ya marcaste tu entrada hoy.');
        }

        $estudiante->marcaciones()->create([
            'fecha' => $fecha,
            'hora_entrada' => now()->format('H:i:s'),
        ]);

        return back()->with('ok', 'Entrada registrada a las ' . now()->format('H:i') . '.');
    }

    // Marcar salida: solo si ya marcó entrada y aún no marca salida
    public function marcarSalida()
    {
        $estudiante = Auth::guard('estudiante')->user();
        $hoy = $estudiante->marcaciones()->where('fecha', now()->toDateString())->first();

        if (! $hoy) {
            return back()->with('error', 'Primero debes marcar tu entrada.');
        }
        if ($hoy->hora_salida) {
            return back()->with('error', 'Ya marcaste tu salida hoy.');
        }

        $hoy->update(['hora_salida' => now()->format('H:i:s')]);

        return back()->with('ok', 'Salida registrada a las ' . now()->format('H:i') . '.');
    }
}
