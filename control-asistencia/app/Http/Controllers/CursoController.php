<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CursoController extends Controller
{
    // Lista de cursos con el número de inscritos
    public function index()
    {
        $cursos = Curso::withCount('estudiantes')->orderBy('codigo')->get();
        return view('cursos.index', compact('cursos'));
    }

    public function create()
    {
        return view('cursos.create', ['curso' => new Curso()]);
    }

    public function store(Request $request)
    {
        $curso = Curso::create($this->validar($request));

        return redirect()->route('cursos.show', $curso)->with('ok', 'Curso creado. Ahora puede inscribir estudiantes.');
    }

    // Detalle del curso: datos + formulario para inscribir estudiantes
    public function show(Curso $curso)
    {
        $todos = Estudiante::orderBy('apellidos')->get();
        // IDs de los estudiantes ya inscritos (para marcar las casillas)
        $inscritos = $curso->estudiantes()->pluck('estudiantes.id')->toArray();

        return view('cursos.show', compact('curso', 'todos', 'inscritos'));
    }

    public function edit(Curso $curso)
    {
        return view('cursos.edit', compact('curso'));
    }

    public function update(Request $request, Curso $curso)
    {
        $curso->update($this->validar($request, $curso));

        return redirect()->route('cursos.index')->with('ok', 'Curso actualizado correctamente.');
    }

    public function destroy(Curso $curso)
    {
        $curso->delete();
        return redirect()->route('cursos.index')->with('ok', 'Curso eliminado.');
    }

    // Guarda las inscripciones del curso.
    // sync() deja inscritos SOLO a los estudiantes marcados (agrega y quita automáticamente).
    public function inscribir(Request $request, Curso $curso)
    {
        $request->validate([
            'estudiantes' => 'array',
            'estudiantes.*' => 'exists:estudiantes,id',
        ]);

        $curso->estudiantes()->sync($request->input('estudiantes', []));

        return redirect()->route('cursos.show', $curso)->with('ok', 'Inscripciones actualizadas.');
    }

    private function validar(Request $request, ?Curso $curso = null)
    {
        $request->merge(['codigo' => strtoupper(trim($request->input('codigo', '')))]);

        return $request->validate([
            'codigo' => ['required', 'string', 'max:20', Rule::unique('cursos')->ignore($curso)],
            'nombre' => 'required|string|max:150',
            'docente' => 'nullable|string|max:150',
            'descripcion' => 'nullable|string|max:1000',
        ]);
    }
}
