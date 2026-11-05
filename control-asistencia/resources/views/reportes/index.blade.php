@extends('layouts.app')

@section('titulo', 'Reportes')

@section('contenido')
    <div class="encabezado">
        <div>
            <p class="antetitulo">Reportes</p>
            <h1>Asistencia por curso</h1>
        </div>
        @if ($curso)
            {{-- Mismo filtro, pero descarga el archivo CSV --}}
            <a href="{{ route('reportes.csv', ['curso_id' => $curso->id, 'desde' => $desde, 'hasta' => $hasta]) }}" class="boton">Exportar CSV</a>
        @endif
    </div>

    <section class="tarjeta">
        <form method="GET" action="{{ route('reportes.index') }}" class="filtros">
            <label class="campo">
                <span>Curso</span>
                <select name="curso_id" required>
                    <option value="">Seleccione un curso…</option>
                    @foreach ($cursos as $c)
                        <option value="{{ $c->id }}" {{ $curso && $curso->id == $c->id ? 'selected' : '' }}>
                            {{ $c->codigo }} — {{ $c->nombre }}
                        </option>
                    @endforeach
                </select>
            </label>
            <label class="campo">
                <span>Desde <em>(opcional)</em></span>
                <input type="date" name="desde" value="{{ $desde }}">
            </label>
            <label class="campo">
                <span>Hasta <em>(opcional)</em></span>
                <input type="date" name="hasta" value="{{ $hasta }}">
            </label>
            <button class="boton boton-principal">Generar</button>
        </form>
        {{-- Errores de los filtros (por ejemplo, rango de fechas invertido) --}}
        @foreach ($errors->all() as $error)
            <small class="error bloque">{{ $error }}</small>
        @endforeach
    </section>

    @if ($curso)
        <section class="tarjeta">
            <div class="tarjeta-titulo">
                <h2>{{ $curso->codigo }} · {{ $curso->nombre }}</h2>
                <span class="tenue">
                    @if ($desde || $hasta)
                        {{ $desde ? \Carbon\Carbon::parse($desde)->format('d/m/Y') : 'inicio' }}
                        — {{ $hasta ? \Carbon\Carbon::parse($hasta)->format('d/m/Y') : 'hoy' }}
                    @else
                        Todas las fechas
                    @endif
                </span>
            </div>

            @if (empty($filas))
                <p class="vacio">El curso no tiene estudiantes inscritos.</p>
            @else
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th class="num">Presentes</th><th class="num">Tardes</th><th class="num">Ausentes</th><th class="num">Justificados</th>
                            <th class="num">Total</th><th>% Asistencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($filas as $fila)
                            <tr>
                                <td>
                                    <a href="{{ route('reportes.estudiante', $fila['estudiante']) }}">{{ $fila['estudiante']->nombre_completo }}</a>
                                    <small class="tenue bloque">{{ $fila['estudiante']->carnet }}</small>
                                </td>
                                <td class="num">{{ $fila['presentes'] }}</td>
                                <td class="num">{{ $fila['tardes'] }}</td>
                                <td class="num">{{ $fila['ausentes'] }}</td>
                                <td class="num">{{ $fila['justificados'] }}</td>
                                <td class="num">{{ $fila['total'] }}</td>
                                <td>
                                    {{-- Barra visual del porcentaje (rojo si es menor a 75%) --}}
                                    <div class="porcentaje {{ $fila['porcentaje'] < 75 ? 'porcentaje-bajo' : '' }}">
                                        <div class="porcentaje-barra"><span style="width: {{ $fila['porcentaje'] }}%"></span></div>
                                        <strong>{{ $fila['porcentaje'] }}%</strong>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p class="nota">% asistencia = (presentes + tardes) ÷ total de clases registradas (los justificados no suman asistencia, pero se muestran aparte de los ausentes). En rojo: menos de 75%.</p>
            @endif
        </section>
    @endif
@endsection
