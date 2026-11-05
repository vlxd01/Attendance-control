@extends('layouts.app')

@section('titulo', 'Marcaciones')

@section('contenido')
    <div class="encabezado">
        <div>
            <p class="antetitulo">Entrada y salida</p>
            <h1>Marcaciones</h1>
        </div>
    </div>

    {{-- Filtros por fecha (por defecto hoy) y por estudiante (opcional) --}}
    <section class="tarjeta">
        <form method="GET" action="{{ route('marcaciones.index') }}" class="filtros">
            <label class="campo">
                <span>Fecha <em>(vacía = todas)</em></span>
                <input type="date" name="fecha" value="{{ $fecha }}">
            </label>
            <label class="campo">
                <span>Estudiante <em>(opcional)</em></span>
                <select name="estudiante_id">
                    <option value="">Todos los estudiantes</option>
                    @foreach ($estudiantes as $e)
                        <option value="{{ $e->id }}" {{ $estudianteId == $e->id ? 'selected' : '' }}>
                            {{ $e->carnet }} — {{ $e->nombre_completo }}
                        </option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="boton boton-principal">Filtrar</button>
        </form>
    </section>

    <section class="tarjeta">
        <div class="tarjeta-titulo">
            <h2>{{ $fecha ? date('d/m/Y', strtotime($fecha)) : 'Todas las fechas' }}</h2>
            <span class="tenue">{{ $marcaciones->count() }} marcaciones · {{ \App\Models\Marcacion::formatoHoras($totalMinutos) }}</span>
        </div>

        @if ($marcaciones->isEmpty())
            <p class="vacio">No hay marcaciones con estos filtros.</p>
        @else
            <table class="tabla">
                <thead>
                    <tr><th>Fecha</th><th>Carnet</th><th>Estudiante</th><th>Entrada</th><th>Salida</th><th class="num">Horas</th></tr>
                </thead>
                <tbody>
                    @foreach ($marcaciones as $m)
                        <tr>
                            <td>{{ $m->fecha_formateada }}</td>
                            <td><span class="codigo">{{ $m->estudiante->carnet }}</span></td>
                            <td>{{ $m->estudiante->nombre_completo }}</td>
                            <td>{{ $m->entrada }}</td>
                            <td>
                                @if ($m->salida) {{ $m->salida }}
                                @else <span class="insignia insignia-tarde">En clase</span>
                                @endif
                            </td>
                            <td class="num">{{ $m->horas }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>
@endsection
