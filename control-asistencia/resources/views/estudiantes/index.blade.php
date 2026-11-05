@extends('layouts.app')

@section('titulo', 'Estudiantes')

@section('contenido')
    <div class="encabezado">
        <div>
            <p class="antetitulo">Catálogo</p>
            <h1>Estudiantes</h1>
        </div>
        <a href="{{ route('estudiantes.create') }}" class="boton boton-principal">Nuevo estudiante</a>
    </div>

    <section class="tarjeta">
        {{-- Búsqueda: envía ?buscar=... por GET --}}
        <form method="GET" class="filtros">
            <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar por carnet, nombre o apellido">
            <button class="boton">Buscar</button>
            @if ($buscar)
                <a href="{{ route('estudiantes.index') }}" class="boton boton-texto">Limpiar</a>
            @endif
        </form>

        @if ($estudiantes->isEmpty())
            <p class="vacio">No hay estudiantes que coincidan.</p>
        @else
            <table class="tabla">
                <thead>
                    <tr><th>Carnet</th><th>Apellidos, nombres</th><th>Correo</th><th class="num">Cursos</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach ($estudiantes as $estudiante)
                        <tr>
                            <td><span class="codigo">{{ $estudiante->carnet }}</span></td>
                            <td><a href="{{ route('estudiantes.show', $estudiante) }}">{{ $estudiante->nombre_completo }}</a></td>
                            <td class="tenue">{{ $estudiante->correo }}</td>
                            <td class="num">{{ $estudiante->cursos_count }}</td>
                            <td><div class="acciones">
                                <a href="{{ route('reportes.estudiante', $estudiante) }}">Historial</a>
                                <a href="{{ route('estudiantes.edit', $estudiante) }}">Editar</a>
                                <form action="{{ route('estudiantes.destroy', $estudiante) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar a {{ $estudiante->nombres }}? También se borrarán sus asistencias.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="enlace-peligro">Eliminar</button>
                                </form>
                            </div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $estudiantes->links() }}
        @endif
    </section>
@endsection
