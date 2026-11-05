@extends('layouts.estudiante')

@section('titulo', 'Mi panel')

@section('contenido')
    <div class="encabezado">
        <div>
            <p class="antetitulo">{{ now()->translatedFormat('l j \d\e F, Y') }}</p>
            <h1>Hola, {{ $estudiante->nombres }}</h1>
        </div>
        <span class="codigo">{{ $estudiante->carnet }}</span>
    </div>

    {{-- Estado del día --}}
    <section class="tarjeta marcar">
        <div class="reloj">
            <span>Hora actual (El Salvador)</span>
            {{-- La hora sale del servidor; el pequeño script de abajo la actualiza cada segundo --}}
            <strong id="reloj">{{ now()->format('H:i:s') }}</strong>
        </div>

        <div class="cifras cifras-dia">
            <div class="cifra">
                <span>Entrada</span>
                <strong>{{ $hoy ? $hoy->entrada : '—' }}</strong>
            </div>
            <div class="cifra">
                <span>Salida</span>
                <strong>{{ $hoy && $hoy->salida ? $hoy->salida : '—' }}</strong>
            </div>
            <div class="cifra">
                <span>Estado de hoy</span>
                <strong class="estado-texto">
                    @if (! $hoy) Sin marcar
                    @elseif (! $hoy->hora_salida) En clase
                    @else Jornada completa
                    @endif
                </strong>
            </div>
        </div>

        {{-- Solo se muestra el botón que corresponde (el servidor también lo valida) --}}
        <div class="botones">
            @if (! $hoy)
                <form action="{{ route('estudiante.entrada') }}" method="POST">
                    @csrf
                    <button type="submit" class="boton boton-principal boton-grande">Marcar entrada</button>
                </form>
            @elseif (! $hoy->hora_salida)
                <form action="{{ route('estudiante.salida') }}" method="POST">
                    @csrf
                    <button type="submit" class="boton boton-principal boton-grande">Marcar salida</button>
                </form>
            @else
                <p class="tenue">Ya registraste tu entrada y tu salida de hoy ({{ $hoy->horas }}).</p>
            @endif
        </div>
    </section>

    {{-- Historial de marcaciones --}}
    <section class="tarjeta">
        <div class="tarjeta-titulo">
            <h2>Mi historial</h2>
            <span class="tenue">Total: <strong>{{ \App\Models\Marcacion::formatoHoras($totalMinutos) }}</strong></span>
        </div>
        @if ($historial->isEmpty())
            <p class="vacio">Todavía no tienes marcaciones.</p>
        @else
            <table class="tabla">
                <thead>
                    <tr><th>Fecha</th><th>Entrada</th><th>Salida</th><th class="num">Horas</th></tr>
                </thead>
                <tbody>
                    @foreach ($historial as $m)
                        <tr>
                            <td>{{ $m->fecha_formateada }}</td>
                            <td>{{ $m->entrada }}</td>
                            <td>{{ $m->salida ?? 'Pendiente' }}</td>
                            <td class="num">{{ $m->horas }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>

    <script>
        // Reloj en vivo: suma un segundo a la hora que envió el servidor
        const reloj = document.getElementById('reloj');
        let [h, m, s] = reloj.textContent.split(':').map(Number);
        setInterval(() => {
            s++; if (s === 60) { s = 0; m++; } if (m === 60) { m = 0; h = (h + 1) % 24; }
            reloj.textContent = [h, m, s].map(n => String(n).padStart(2, '0')).join(':');
        }, 1000);
    </script>
@endsection
