<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Marcacion;

class DashboardController extends Controller
{
    // Página de inicio con conteos generales
    public function index()
    {
        $totalEstudiantes = Estudiante::count();
        $totalCursos = Curso::count();
        $totalRegistros = Asistencia::count();
        $asistenciasHoy = Asistencia::where('fecha', now()->toDateString())->count();
        $entradasHoy = Marcacion::where('fecha', now()->toDateString())->count(); // estudiantes que marcaron entrada hoy

        // Porcentaje general: (presentes + tardes) / total de registros
        $asistieron = Asistencia::whereIn('estado', ['presente', 'tarde'])->count();
        $porcentajeGeneral = $totalRegistros > 0 ? round($asistieron * 100 / $totalRegistros, 1) : 0;

        // Últimas 6 clases en las que se tomó asistencia (agrupadas por curso y fecha)
        $ultimasClases = Asistencia::with('curso')
            ->select('curso_id', 'fecha')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN estado = 'ausente' THEN 1 ELSE 0 END) as ausentes")
            ->selectRaw("SUM(CASE WHEN estado = 'justificado' THEN 1 ELSE 0 END) as justificados")
            ->groupBy('curso_id', 'fecha')
            ->orderByDesc('fecha')
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'totalEstudiantes', 'totalCursos', 'asistenciasHoy', 'entradasHoy', 'porcentajeGeneral', 'ultimasClases'
        ));
    }
}
