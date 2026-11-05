@extends('layouts.app')

@section('titulo', $curso->nombre)

@section('contenido')
    <div class="encabezado">
        <div>
            <p class="antetitulo"><a href="{{ route('cursos.index') }}">Cursos</a> · {{ $curso->codigo }}</p>
            <h1>{{ $curso->nombre }}</h1>
            <p class="tenue">{{ $curso->docente ?: 'Sin docente asignado' }}</p>
        </div>
        <div class="botones">
            <a href="{{ route('reportes.index', ['curso_id' => $curso->id]) }}" class="boton">Ver reporte</a>
            <a href="{{ route('asistencias.index', ['curso_id' => $curso->id]) }}" class="boton boton-principal">Tomar asistencia</a>
        </div>
    </div>

    @if ($curso->descripcion)
        <p class="descripcion">{{ $curso->descripcion }}</p>
    @endif

    {{-- Inscripciones: casillas marcadas = estudiantes inscritos --}}
    <section class="tarjeta">
        <h2>Inscripción de estudiantes <small class="tenue">({{ count($inscritos) }} de {{ $todos->count() }})</small></h2>

        @if ($todos->isEmpty())
            <p class="vacio">Primero registre estudiantes.</p>
        @else
            <form action="{{ route('cursos.inscribir', $curso) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="lista-casillas">
                    @foreach ($todos as $estudiante)
                        <label class="casilla-tarjeta">
                            <input type="checkbox" name="estudiantes[]" value="{{ $estudiante->id }}"
                                   {{ in_array($estudiante->id, $inscritos) ? 'checked' : '' }}>
                            <span>
                                <strong>{{ $estudiante->nombre_completo }}</strong>
                                <small>{{ $estudiante->carnet }}</small>
                            </span>
                        </label>
                    @endforeach
                </div>
                <div class="botones">
                    <button type="submit" class="boton boton-principal">Guardar inscripciones</button>
                </div>
            </form>
        @endif
    </section>
@endsection
