@extends('layouts.app')

@section('titulo', 'Tomar asistencia')

@section('contenido')
    <div class="encabezado">
        <div>
            <p class="antetitulo">Registro diario</p>
            <h1>Tomar asistencia</h1>
        </div>
    </div>

    {{-- Paso 1: elegir curso y fecha (se envía por GET) --}}
    <section class="tarjeta">
        <form method="GET" action="{{ route('asistencias.index') }}" class="filtros">
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
                <span>Fecha</span>
                <input type="date" name="fecha" value="{{ $fecha }}" max="{{ now()->toDateString() }}" required>
            </label>
            <button class="boton">Cargar lista</button>
        </form>

        {{-- Fechas ya registradas: permiten volver a editar --}}
        @if ($fechasTomadas->isNotEmpty())
            <div class="fechas">
                <span class="tenue">Fechas registradas:</span>
                @foreach ($fechasTomadas as $f)
                    <a href="{{ route('asistencias.index', ['curso_id' => $curso->id, 'fecha' => $f]) }}"
                       class="chip {{ $f == $fecha ? 'chip-activo' : '' }}">{{ \Carbon\Carbon::parse($f)->format('d/m') }}</a>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Paso 2: lista de inscritos con sus opciones --}}
    @if ($curso)
        <section class="tarjeta">
            <div class="tarjeta-titulo">
                <h2>{{ $curso->nombre }} · {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</h2>
                @if ($registros->isNotEmpty())
                    <span class="etiqueta">Editando asistencia ya guardada</span>
                @else
                    <span class="etiqueta etiqueta-nueva">Nueva</span>
                @endif
            </div>

            @error('fecha') <div class="aviso aviso-error">{{ $message }}</div> @enderror
            @error('estados') <div class="aviso aviso-error">{{ $message }}</div> @enderror

            @if ($estudiantes->isEmpty())
                <p class="vacio">Este curso no tiene estudiantes inscritos.
                    <a href="{{ route('cursos.show', $curso) }}">Inscribir estudiantes</a></p>
            @else
                <form action="{{ route('asistencias.guardar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="curso_id" value="{{ $curso->id }}">
                    <input type="hidden" name="fecha" value="{{ $fecha }}">

                    <table class="tabla tabla-asistencia">
                        <thead>
                            <tr><th>Estudiante</th><th>Estado</th><th>Observación / Motivo</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($estudiantes as $estudiante)
                                @php
                                    // Si ya existe registro se usa su estado; si no, "presente" por defecto
                                    $registro = $registros->get($estudiante->id);
                                    $estadoActual = old('estados.' . $estudiante->id, $registro->estado ?? 'presente');
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $estudiante->nombre_completo }}</strong>
                                        <small class="tenue bloque">{{ $estudiante->carnet }}</small>
                                    </td>
                                    <td>
                                        {{-- name="estados[ID]" envía un arreglo: estudiante_id => estado --}}
                                        <div class="estados">
                                            @foreach (\App\Models\Asistencia::ESTADOS as $estado)
                                                <label class="estado estado-{{ $estado }}">
                                                    <input type="radio" name="estados[{{ $estudiante->id }}]" value="{{ $estado }}"
                                                           {{ $estadoActual == $estado ? 'checked' : '' }}>
                                                    <span>{{ ucfirst($estado) }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        {{-- Si el estado es "justificado", este campo es el motivo y es obligatorio --}}
                                        <input type="text" name="observaciones[{{ $estudiante->id }}]" maxlength="255"
                                               value="{{ old('observaciones.' . $estudiante->id, $registro->observacion ?? '') }}"
                                               placeholder="{{ $estadoActual == 'justificado' ? 'Motivo (obligatorio)' : 'Opcional' }}"
                                               {{ $estadoActual == 'justificado' ? 'required' : '' }}
                                               class="campo-compacto {{ $estadoActual == 'justificado' ? 'campo-motivo' : '' }}">
                                        @error('observaciones.' . $estudiante->id) <small class="error bloque">{{ $message }}</small> @enderror
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="botones">
                        <button type="submit" class="boton boton-principal">Guardar asistencia</button>
                    </div>
                </form>
            @endif
        </section>

        {{-- JS mínimo: al elegir "Justificado" el campo de motivo se vuelve obligatorio y se resalta --}}
        <script>
            document.querySelectorAll('.tabla-asistencia input[type=radio]').forEach(function (radio) {
                radio.addEventListener('change', function () {
                    var campo = radio.closest('tr').querySelector('input[type=text]');
                    var justificado = radio.value === 'justificado';
                    campo.required = justificado;
                    campo.placeholder = justificado ? 'Motivo (obligatorio)' : 'Opcional';
                    campo.classList.toggle('campo-motivo', justificado);
                    if (justificado) campo.focus();
                });
            });
        </script>
    @endif
@endsection
