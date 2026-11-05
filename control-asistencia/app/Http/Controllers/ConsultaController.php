<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Curso;
use Illuminate\Http\Request;

// Consulta por fecha: el docente ve todas las asistencias de un día
class ConsultaController extends Controller
{
    public function index(Request $request)
    {
        // Validación sencilla: la fecha y el curso son opcionales
        $request->validate([
            'fecha' => 'nullable|date',
            'curso_id' => 'nullable|exists:cursos,id',
        ]);

        // Por defecto se consulta el día de hoy
        $fecha = $request->input('fecha') ?: now()->toDateString();
        $cursoId = $request->input('curso_id');

        // Asistencias de esa fecha (y del curso, si se eligió uno)
        $asistencias = Asistencia::with(['curso', 'estudiante'])
            ->where('fecha', $fecha)
            ->when($cursoId, fn ($consulta) => $consulta->where('curso_id', $cursoId))
            ->get()
            ->sortBy(fn ($a) => $a->curso->nombre . ' ' . $a->estudiante->apellidos);

        // Resumen: cuántos presentes, ausentes, tarde y justificados
        $conteos = [
            'presente' => $asistencias->where('estado', 'presente')->count(),
            'ausente' => $asistencias->where('estado', 'ausente')->count(),
            'tarde' => $asistencias->where('estado', 'tarde')->count(),
            'justificado' => $asistencias->where('estado', 'justificado')->count(),
        ];

        $cursos = Curso::orderBy('nombre')->get();

        return view('asistencias.consulta', compact('asistencias', 'conteos', 'fecha', 'cursoId', 'cursos'));
    }
}
