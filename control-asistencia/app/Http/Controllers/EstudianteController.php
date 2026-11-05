<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EstudianteController extends Controller
{
    // Lista de estudiantes con búsqueda por carnet, nombre o apellido
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $estudiantes = Estudiante::withCount('cursos')
            ->when($buscar, function ($consulta) use ($buscar) {
                $consulta->where('carnet', 'like', "%$buscar%")
                    ->orWhere('nombres', 'like', "%$buscar%")
                    ->orWhere('apellidos', 'like', "%$buscar%");
            })
            ->orderBy('apellidos')
            ->paginate(10)
            ->withQueryString();

        return view('estudiantes.index', compact('estudiantes', 'buscar'));
    }

    // Formulario para crear
    public function create()
    {
        return view('estudiantes.create', ['estudiante' => new Estudiante()]);
    }

    // Guarda un estudiante nuevo
    public function store(Request $request)
    {
        $datos = $this->validar($request);
        Estudiante::create($datos);

        return redirect()->route('estudiantes.index')->with('ok', 'Estudiante registrado correctamente.');
    }

    // Detalle del estudiante (datos y cursos inscritos)
    public function show(Estudiante $estudiante)
    {
        $estudiante->load('cursos');
        return view('estudiantes.show', compact('estudiante'));
    }

    // Formulario para editar
    public function edit(Estudiante $estudiante)
    {
        return view('estudiantes.edit', compact('estudiante'));
    }

    // Actualiza un estudiante
    public function update(Request $request, Estudiante $estudiante)
    {
        $datos = $this->validar($request, $estudiante);

        // Si el campo contraseña viene vacío, se conserva la anterior
        if (empty($datos['password'])) {
            unset($datos['password']);
        }
        $estudiante->update($datos);

        return redirect()->route('estudiantes.index')->with('ok', 'Estudiante actualizado correctamente.');
    }

    // Elimina un estudiante (sus inscripciones y asistencias se borran en cascada)
    public function destroy(Estudiante $estudiante)
    {
        $estudiante->delete();
        return redirect()->route('estudiantes.index')->with('ok', 'Estudiante eliminado.');
    }

    // Reglas de validación compartidas por store() y update()
    private function validar(Request $request, ?Estudiante $estudiante = null)
    {
        // El carnet se guarda siempre en mayúsculas
        $request->merge(['carnet' => strtoupper(trim($request->input('carnet', '')))]);

        return $request->validate([
            // Formato UFG: 2 letras + 6 números (ej. RR123456). Al editar se ignora el propio registro.
            'carnet' => ['required', 'regex:/^[A-Z]{2}[0-9]{6}$/', Rule::unique('estudiantes')->ignore($estudiante)],
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'correo' => ['required', 'email', 'max:150', Rule::unique('estudiantes')->ignore($estudiante)],
            // Contraseña del portal: obligatoria al crear, opcional al editar
            'password' => [$estudiante ? 'nullable' : 'required', 'string', 'min:6'],
        ], [
            'carnet.regex' => 'El carnet debe tener 2 letras y 6 números (ej. RR123456).',
        ]);
    }
}
