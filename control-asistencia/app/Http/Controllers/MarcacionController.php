<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Marcacion;
use Illuminate\Http\Request;

// Vista del docente: marcaciones de entrada y salida de los estudiantes
class MarcacionController extends Controller
{
    public function index(Request $request)
    {
        // Fecha del filtro: por defecto hoy. Si se deja vacía, se muestran todas las fechas.
        $fecha = $request->has('fecha') ? $request->input('fecha') : now()->toDateString();
        if ($fecha && ! strtotime($fecha)) {
            $fecha = now()->toDateString(); // fecha inválida en la URL -> hoy
        }
        $estudianteId = $request->input('estudiante_id');

        $marcaciones = Marcacion::with('estudiante')
            ->when($fecha, fn ($consulta) => $consulta->where('fecha', date('Y-m-d', strtotime($fecha))))
            ->when($estudianteId, fn ($consulta) => $consulta->where('estudiante_id', $estudianteId))
            ->orderByDesc('fecha')
            ->orderBy('hora_entrada')
            ->get();

        $totalMinutos = $marcaciones->sum('minutos');
        $estudiantes = Estudiante::orderBy('apellidos')->get();

        return view('marcaciones.index', compact('marcaciones', 'fecha', 'estudianteId', 'estudiantes', 'totalMinutos'));
    }
}
