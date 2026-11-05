@extends('layouts.app')

@section('titulo', 'Editar curso')

@section('contenido')
    <div class="encabezado">
        <div>
            <p class="antetitulo"><a href="{{ route('cursos.index') }}">Cursos</a></p>
            <h1>Editar curso</h1>
        </div>
    </div>

    <section class="tarjeta tarjeta-angosta">
        <form action="{{ route('cursos.update', $curso) }}" method="POST" class="formulario">
            @csrf
            @method('PUT')
            @include('cursos._form')
            <div class="botones">
                <a href="{{ route('cursos.index') }}" class="boton boton-texto">Cancelar</a>
                <button type="submit" class="boton boton-principal">Actualizar</button>
            </div>
        </form>
    </section>
@endsection
