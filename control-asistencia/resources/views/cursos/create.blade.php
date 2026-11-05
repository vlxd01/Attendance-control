@extends('layouts.app')

@section('titulo', 'Nuevo curso')

@section('contenido')
    <div class="encabezado">
        <div>
            <p class="antetitulo"><a href="{{ route('cursos.index') }}">Cursos</a></p>
            <h1>Nuevo curso</h1>
        </div>
    </div>

    <section class="tarjeta tarjeta-angosta">
        <form action="{{ route('cursos.store') }}" method="POST" class="formulario">
            @csrf
            @include('cursos._form')
            <div class="botones">
                <a href="{{ route('cursos.index') }}" class="boton boton-texto">Cancelar</a>
                <button type="submit" class="boton boton-principal">Guardar</button>
            </div>
        </form>
    </section>
@endsection
