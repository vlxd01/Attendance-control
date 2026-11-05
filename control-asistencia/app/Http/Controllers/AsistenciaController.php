<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Curso;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    // Paso 1: elegir curso y fecha. Paso 2: mostrar la lista de inscritos.
    public function index(Request $request)
    {
        $cursos = Curso::orderBy('nombre')->get();
        $curso = Curso::find($request->input('curso_id'));

        // Fecha elegida; si no viene o no es válida, se usa la de hoy
        $fecha = $request->input('fecha');
        $fecha = strtotime($fecha ?? '') ? date('Y-m-d', strtotime($fecha)) : now()->toDateString();

        $estudiantes = collect();
        $registros = collect();
        $fechasTomadas = collect();

        if ($curso) {
            $estudiantes = $curso->estudiantes()->orderBy('apellidos')->get();

            // Asistencia ya guardada en esa fecha (si existe, se muestra para editarla)
            $registros = Asistencia::where('curso_id', $curso->id)
                ->where('fecha', $fecha)
                ->get()
                ->keyBy('estudiante_id');

            // Fechas en que ya se tomó asistencia en este curso
            $fechasTomadas = Asistencia::where('curso_id', $curso->id)
                ->select('fecha')->distinct()->orderByDesc('fecha')->pluck('fecha');
        }

        return view('asistencias.index', compact('cursos', 'curso', 'fecha', 'estudiantes', 'registros', 'fechasTomadas'));
    }

    // Guarda (o actualiza) la asistencia de todo el grupo
    public function guardar(Request $request)
    {
        $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'fecha' => 'required|date|before_or_equal:today',
            'estados' => 'required|array',
            'estados.*' => 'in:presente,ausente,tarde,justificado',
            // El motivo (observación) es obligatorio solo si el estado es justificado
            'observaciones.*' => 'nullable|required_if:estados.*,justificado|string|max:255',
        ], [
            'estados.required' => 'El curso no tiene estudiantes inscritos.',
            'fecha.before_or_equal' => 'No se puede tomar asistencia de una fecha futura.',
            'observaciones.*.required_if' => 'Escribe el motivo de la justificación.',
        ]);

        $curso = Curso::findOrFail($request->curso_id);
        $inscritos = $curso->estudiantes()->pluck('estudiantes.id')->toArray();

        foreach ($request->estados as $estudianteId => $estado) {
            // Seguridad: solo se guardan estudiantes inscritos en el curso
            if (! in_array($estudianteId, $inscritos)) {
                continue;
            }

            // updateOrCreate: si ya existe el registro (curso + estudiante + fecha) lo actualiza,
            // si no existe lo crea. Así se puede editar una fecha ya tomada.
            Asistencia::updateOrCreate(
                ['curso_id' => $curso->id, 'estudiante_id' => $estudianteId, 'fecha' => $request->fecha],
                ['estado' => $estado, 'observacion' => $request->input("observaciones.$estudianteId")]
            );
        }

        return redirect()
            ->route('asistencias.index', ['curso_id' => $curso->id, 'fecha' => $request->fecha])
            ->with('ok', 'Asistencia guardada correctamente.');
    }
}
