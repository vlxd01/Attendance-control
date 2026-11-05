<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Mi panel') · Portal del estudiante</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    {{-- Misma barra que el docente, pero con el menú del estudiante --}}
    <header class="barra">
        <div class="barra-interior">
            <a href="{{ route('estudiante.panel') }}" class="marca">
                <span class="marca-sello">CA</span>
                <span>
                    <strong>Portal del estudiante</strong>
                    <small>Angry Birdgos · UFG</small>
                </span>
            </a>

            <nav class="menu">
                <a href="{{ route('estudiante.panel') }}" class="activo">Mi panel</a>
            </nav>

            <div class="usuario">
                <span>{{ auth('estudiante')->user()->nombres }}</span>
                <form action="{{ route('estudiante.salir') }}" method="POST">
                    @csrf
                    <button type="submit" class="boton boton-texto">Salir</button>
                </form>
            </div>
        </div>
    </header>

    <main class="contenedor">
        @if (session('ok'))
            <div class="aviso aviso-ok">{{ session('ok') }}</div>
        @endif
        @if (session('error'))
            <div class="aviso aviso-error">{{ session('error') }}</div>
        @endif

        @yield('contenido')
    </main>

    <footer class="pie">
        Universidad Francisco Gavidia · Desarrollo Web · Grupo Angry Birdgos
    </footer>
</body>
</html>
