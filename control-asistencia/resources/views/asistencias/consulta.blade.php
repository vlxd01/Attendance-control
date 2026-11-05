@extends('layouts.app')

@section('titulo', 'Consulta por fecha')

@section('contenido')
    <div class="encabezado">
        <div>
            <p class="antetitulo">Asistencia</p>
            <h1>Consulta por fecha</h1>
        </div>
    </div>

    {{-- Filtros: fecha (por defecto hoy) y curso (opcional) --}}
    <section class="tarjeta">
        <form method="GET" action="{{ route('asistencias.consulta') }}" class="filtros">
            <label class="campo">
                <span>Fecha</span>
                <input type="date" name="fecha" value="{{ $fecha }}">
            </label>
            <label class="campo">
                <span>Curso <em>(opcional)</em></span>
                <select name="curso_id">
                    <option value="">Todos los cursos</option>
                    @foreach ($cursos as $c)
                        <option value="{{ $c->id }}" {{ $cursoId == $c->id ? 'selected' : '' }}>
                            {{ $c->codigo }} — {{ $c->nombre }}
                        </option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="boton boton-principal">Consultar</button>
        </form>
    </section>

    {{-- Resumen de conteos --}}
    <div class="cifras">
        <div class="cifra"><span>Presentes</span><strong>{{ $conteos['presente'] }}</strong></div>
        <div class="cifra"><span>Ausentes</span><strong>{{ $conteos['ausente'] }}</strong></div>
        <div class="cifra"><span>Tarde</span><strong>{{ $conteos['tarde'] }}</strong></div>
        <div class="cifra"><span>Justificados</span><strong>{{ $conteos['justificado'] }}</strong></div>
    </div>

    <section class="tarjeta">
        <div class="tarjeta-titulo">
            <h2>{{ date('d/m/Y', strtotime($fecha)) }}</h2>
            <span class="tenue">{{ $asistencias->count() }} registros</span>
        </div>

        @if ($asistencias->isEmpty())
            <p class="vacio">No hay asistencias registradas para esta fecha. Prueba con otro día o con otro curso.</p>
        @else
            <table class="tabla">
                <thead>
                    <tr><th>Curso</th><th>Carnet</th><th>Estudiante</th><th>Estado</th><th>Observación / Motivo</th></tr>
                </thead>
                <tbody>
                    @foreach ($asistencias as $a)
                        <tr>
                            <td>{{ $a->curso->nombre }}</td>
                            <td><span class="codigo">{{ $a->estudiante->carnet }}</span></td>
                            <td>{{ $a->estudiante->nombre_completo }}</td>
                            <td><span class="insignia insignia-{{ $a->estado }}">{{ ucfirst($a->estado) }}</span></td>
                            <td>{{ $a->observacion ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>
@endsection
