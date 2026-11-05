@extends('layouts.app')

@section('titulo', 'Historial de ' . $estudiante->nombres)

@section('contenido')
    <div class="encabezado">
        <div>
            <p class="antetitulo"><a href="{{ route('estudiantes.show', $estudiante) }}">{{ $estudiante->carnet }}</a> · Historial</p>
            <h1>{{ $estudiante->nombres }} {{ $estudiante->apellidos }}</h1>
        </div>
    </div>

    <div class="cifras">
        <div class="cifra"><span>Clases registradas</span><strong>{{ $total }}</strong></div>
        <div class="cifra"><span>Presentes</span><strong>{{ $asistencias->where('estado', 'presente')->count() }}</strong></div>
        <div class="cifra"><span>Tardes</span><strong>{{ $asistencias->where('estado', 'tarde')->count() }}</strong></div>
        <div class="cifra"><span>Ausentes</span><strong>{{ $asistencias->where('estado', 'ausente')->count() }}</strong></div>
        <div class="cifra"><span>Justificados</span><strong>{{ $asistencias->where('estado', 'justificado')->count() }}</strong></div>
        <div class="cifra cifra-acento"><span>% Asistencia</span><strong>{{ $porcentaje }}%</strong></div>
    </div>

    <section class="tarjeta">
        <h2>Detalle</h2>
        @if ($asistencias->isEmpty())
            <p class="vacio">Este estudiante aún no tiene asistencias registradas.</p>
        @else
            <table class="tabla">
                <thead>
                    <tr><th>Fecha</th><th>Curso</th><th>Estado</th><th>Observación / Motivo</th></tr>
                </thead>
                <tbody>
                    @foreach ($asistencias as $asistencia)
                        <tr>
                            <td>{{ $asistencia->fecha_formateada }}</td>
                            <td>{{ $asistencia->curso->nombre }}</td>
                            <td><span class="insignia insignia-{{ $asistencia->estado }}">{{ ucfirst($asistencia->estado) }}</span></td>
                            <td class="tenue">{{ $asistencia->observacion }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>
@endsection
