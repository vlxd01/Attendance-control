<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Inicio') · Control de Asistencia</title>
    {{-- Hoja de estilos propia (CSS plano, sin npm ni Vite) --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    {{-- Barra superior con el menú principal --}}
    <header class="barra">
        <div class="barra-interior">
            <a href="{{ route('dashboard') }}" class="marca">
                <span class="marca-sello">CA</span>
                <span>
                    <strong>Control de Asistencia</strong>
                    <small>Angry Birdgos · UFG</small>
                </span>
            </a>

            {{-- request()->routeIs() marca como "activo" el enlace de la sección actual --}}
            <nav class="menu">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'activo' : '' }}">Inicio</a>
                <a href="{{ route('estudiantes.index') }}" class="{{ request()->routeIs('estudiantes.*') ? 'activo' : '' }}">Estudiantes</a>
                <a href="{{ route('cursos.index') }}" class="{{ request()->routeIs('cursos.*') ? 'activo' : '' }}">Cursos</a>
                <a href="{{ route('asistencias.index') }}" class="{{ request()->routeIs('asistencias.index') ? 'activo' : '' }}">Tomar asistencia</a>
                <a href="{{ route('asistencias.consulta') }}" class="{{ request()->routeIs('asistencias.consulta') ? 'activo' : '' }}">Consulta por fecha</a>
                <a href="{{ route('reportes.index') }}" class="{{ request()->routeIs('reportes.*') ? 'activo' : '' }}">Reportes</a>
                <a href="{{ route('marcaciones.index') }}" class="{{ request()->routeIs('marcaciones.*') ? 'activo' : '' }}">Marcaciones</a>
            </nav>

            <div class="usuario">
                <span>{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="boton boton-texto">Salir</button>
                </form>
            </div>
        </div>
    </header>

    <main class="contenedor">
        {{-- Mensajes flash: se guardan en la sesión con ->with('ok', '...') --}}
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
