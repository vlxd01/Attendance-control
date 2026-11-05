@extends('layouts.app')

@section('titulo', $estudiante->nombre_completo)

@section('contenido')
    <div class="encabezado">
        <div>
            <p class="antetitulo"><a href="{{ route('estudiantes.index') }}">Estudiantes</a> · {{ $estudiante->carnet }}</p>
            <h1>{{ $estudiante->nombres }} {{ $estudiante->apellidos }}</h1>
        </div>
        <div class="botones">
            <a href="{{ route('reportes.estudiante', $estudiante) }}" class="boton">Ver historial</a>
            <a href="{{ route('estudiantes.edit', $estudiante) }}" class="boton boton-principal">Editar</a>
        </div>
    </div>

    <div class="rejilla-2">
        <section class="tarjeta">
            <h2>Datos</h2>
            <dl class="datos">
                <dt>Carnet</dt><dd><span class="codigo">{{ $estudiante->carnet }}</span></dd>
                <dt>Nombres</dt><dd>{{ $estudiante->nombres }}</dd>
                <dt>Apellidos</dt><dd>{{ $estudiante->apellidos }}</dd>
                <dt>Correo</dt><dd>{{ $estudiante->correo }}</dd>
            </dl>
        </section>

        <section class="tarjeta">
            <h2>Cursos inscritos</h2>
            @forelse ($estudiante->cursos as $curso)
                <a href="{{ route('cursos.show', $curso) }}" class="fila-enlace">
                    <span class="codigo">{{ $curso->codigo }}</span> {{ $curso->nombre }}
                </a>
            @empty
                <p class="vacio">No está inscrito en ningún curso.</p>
            @endforelse
        </section>
    </div>
@endsection
