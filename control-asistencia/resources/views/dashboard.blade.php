@extends('layouts.app')

@section('titulo', 'Inicio')

@section('contenido')
    <div class="encabezado">
        <div>
            <p class="antetitulo">{{ now()->translatedFormat('l j \d\e F, Y') }}</p>
            <h1>Bienvenido, {{ auth()->user()->name }}</h1>
        </div>
        <a href="{{ route('asistencias.index') }}" class="boton boton-principal">Tomar asistencia de hoy</a>
    </div>

    {{-- Tarjetas con conteos generales --}}
    <div class="cifras">
        <div class="cifra">
            <span>Estudiantes</span>
            <strong>{{ $totalEstudiantes }}</strong>
        </div>
        <div class="cifra">
            <span>Cursos</span>
            <strong>{{ $totalCursos }}</strong>
        </div>
        <div class="cifra">
            <span>Registros de hoy</span>
            <strong>{{ $asistenciasHoy }}</strong>
        </div>
        <div class="cifra">
            <span>Entradas de hoy</span>
            <strong>{{ $entradasHoy }}</strong>
        </div>
        <div class="cifra cifra-acento">
            <span>Asistencia general</span>
            <strong>{{ $porcentajeGeneral }}%</strong>
        </div>
    </div>

    <section class="tarjeta">
        <h2>Últimas clases registradas</h2>
        @if ($ultimasClases->isEmpty())
            <p class="vacio">Todavía no se ha tomado asistencia.</p>
        @else
            <table class="tabla">
                <thead>
                    <tr><th>Fecha</th><th>Curso</th><th class="num">Registros</th><th class="num">Ausentes</th><th class="num">Justificados</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach ($ultimasClases as $clase)
                        <tr>
                            <td>{{ $clase->fecha_formateada }}</td>
                            <td>{{ $clase->curso->nombre }}</td>
                            <td class="num">{{ $clase->total }}</td>
                            <td class="num">{{ $clase->ausentes }}</td>
                            <td class="num">{{ $clase->justificados }}</td>
                            <td><div class="acciones">
                                <a href="{{ route('asistencias.index', ['curso_id' => $clase->curso_id, 'fecha' => $clase->fecha]) }}">Ver / editar</a>
                            </div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>
@endsection
