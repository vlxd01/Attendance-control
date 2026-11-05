<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReporteController extends Controller
{
    // Reporte por curso (con rango de fechas opcional)
    public function index(Request $request)
    {
        $validador = Validator::make($request->all(), [
            'curso_id' => 'nullable|exists:cursos,id',
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date|after_or_equal:desde',
        ]);

        // Si los filtros no son válidos, se regresa al formulario limpio con los errores
        if ($validador->fails()) {
            return redirect()->route('reportes.index')->withErrors($validador);
        }

        $cursos = Curso::orderBy('nombre')->get();
        $curso = Curso::find($request->curso_id);
        $filas = $curso ? $this->calcularResumen($curso, $request->desde, $request->hasta) : [];

        return view('reportes.index', [
            'cursos' => $cursos,
            'curso' => $curso,
            'filas' => $filas,
            'desde' => $request->desde,
            'hasta' => $request->hasta,
        ]);
    }

    // Descarga el mismo reporte en formato CSV (se abre con Excel)
    public function exportarCsv(Request $request)
    {
        $curso = Curso::findOrFail($request->curso_id);
        $filas = $this->calcularResumen($curso, $request->desde, $request->hasta);

        $nombreArchivo = 'reporte_' . $curso->codigo . '.csv';

        return response()->streamDownload(function () use ($filas) {
            $archivo = fopen('php://output', 'w');
            fwrite($archivo, "\xEF\xBB\xBF"); // BOM para que Excel muestre bien las tildes
            // Se usa ";" como separador porque Excel en español lo espera así
            fputcsv($archivo, ['Carnet', 'Estudiante', 'Presentes', 'Tardes', 'Ausentes', 'Justificados', 'Total', '% Asistencia'], ';');
            foreach ($filas as $f) {
                fputcsv($archivo, [
                    $f['estudiante']->carnet, $f['estudiante']->nombre_completo,
                    $f['presentes'], $f['tardes'], $f['ausentes'], $f['justificados'], $f['total'], $f['porcentaje'],
                ], ';');
            }
            fclose($archivo);
        }, $nombreArchivo, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // Historial de asistencias de un estudiante
    public function estudiante(Estudiante $estudiante)
    {
        $asistencias = $estudiante->asistencias()->with('curso')->orderByDesc('fecha')->get();

        $total = $asistencias->count();
        $asistio = $asistencias->whereIn('estado', ['presente', 'tarde'])->count();
        $porcentaje = $total > 0 ? round($asistio * 100 / $total, 1) : 0;

        return view('reportes.estudiante', compact('estudiante', 'asistencias', 'total', 'porcentaje'));
    }

    // Cuenta presentes, tardes y ausentes de cada estudiante inscrito en el curso.
    // % asistencia = (presentes + tardes) / total de clases registradas * 100
    // Los justificados cuentan en el total (no son asistencia), pero se muestran aparte de los ausentes.
    private function calcularResumen(Curso $curso, $desde, $hasta)
    {
        // Todas las asistencias del curso dentro del rango (si se indicó)
        $asistencias = Asistencia::where('curso_id', $curso->id)
            ->when($desde, fn ($q) => $q->where('fecha', '>=', $desde))
            ->when($hasta, fn ($q) => $q->where('fecha', '<=', $hasta))
            ->get();

        $filas = [];
        foreach ($curso->estudiantes()->orderBy('apellidos')->get() as $estudiante) {
            $registros = $asistencias->where('estudiante_id', $estudiante->id);

            $presentes = $registros->where('estado', 'presente')->count();
            $tardes = $registros->where('estado', 'tarde')->count();
            $ausentes = $registros->where('estado', 'ausente')->count();
            $justificados = $registros->where('estado', 'justificado')->count(); // se cuentan aparte de los ausentes
            $total = $registros->count();

            $filas[] = [
                'estudiante' => $estudiante,
                'presentes' => $presentes,
                'tardes' => $tardes,
                'ausentes' => $ausentes,
                'justificados' => $justificados,
                'total' => $total,
                'porcentaje' => $total > 0 ? round(($presentes + $tardes) * 100 / $total, 1) : 0,
            ];
        }

        return $filas;
    }
}
