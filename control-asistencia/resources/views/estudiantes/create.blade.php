@extends('layouts.app')

@section('titulo', 'Nuevo estudiante')

@section('contenido')
    <div class="encabezado">
        <div>
            <p class="antetitulo"><a href="{{ route('estudiantes.index') }}">Estudiantes</a></p>
            <h1>Nuevo estudiante</h1>
        </div>
    </div>

    <section class="tarjeta tarjeta-angosta">
        <form action="{{ route('estudiantes.store') }}" method="POST" class="formulario">
            @csrf
            @include('estudiantes._form')
            <div class="botones">
                <a href="{{ route('estudiantes.index') }}" class="boton boton-texto">Cancelar</a>
                <button type="submit" class="boton boton-principal">Guardar</button>
            </div>
        </form>
    </section>
@endsection
