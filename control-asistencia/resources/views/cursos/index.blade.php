@extends('layouts.app')

@section('titulo', 'Cursos')

@section('contenido')
    <div class="encabezado">
        <div>
            <p class="antetitulo">Catálogo</p>
            <h1>Cursos</h1>
        </div>
        <a href="{{ route('cursos.create') }}" class="boton boton-principal">Nuevo curso</a>
    </div>

    @if ($cursos->isEmpty())
        <section class="tarjeta"><p class="vacio">No hay cursos registrados.</p></section>
    @else
        <div class="cursos">
            @foreach ($cursos as $curso)
                <article class="tarjeta curso">
                    <span class="codigo">{{ $curso->codigo }}</span>
                    <h2><a href="{{ route('cursos.show', $curso) }}">{{ $curso->nombre }}</a></h2>
                    <p class="tenue">{{ $curso->docente ?: 'Sin docente asignado' }}</p>
                    <p class="curso-inscritos"><strong>{{ $curso->estudiantes_count }}</strong> inscritos</p>
                    <div class="acciones">
                        <a href="{{ route('cursos.show', $curso) }}">Inscripciones</a>
                        <a href="{{ route('asistencias.index', ['curso_id' => $curso->id]) }}">Asistencia</a>
                        <a href="{{ route('cursos.edit', $curso) }}">Editar</a>
                        <form action="{{ route('cursos.destroy', $curso) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar el curso {{ $curso->codigo }}? Se borrarán sus inscripciones y asistencias.')">
                            @csrf
                            @method('DELETE')
                            <button class="enlace-peligro">Eliminar</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
