@extends('layouts.app')

@section('titulo', 'Editar estudiante')

@section('contenido')
    <div class="encabezado">
        <div>
            <p class="antetitulo"><a href="{{ route('estudiantes.index') }}">Estudiantes</a></p>
            <h1>Editar estudiante</h1>
        </div>
    </div>

    <section class="tarjeta tarjeta-angosta">
        <form action="{{ route('estudiantes.update', $estudiante) }}" method="POST" class="formulario">
            @csrf
            @method('PUT') {{-- Los formularios HTML solo envían GET/POST; Laravel simula PUT --}}
            @include('estudiantes._form')
            <div class="botones">
                <a href="{{ route('estudiantes.index') }}" class="boton boton-texto">Cancelar</a>
                <button type="submit" class="boton boton-principal">Actualizar</button>
            </div>
        </form>
    </section>
@endsection
