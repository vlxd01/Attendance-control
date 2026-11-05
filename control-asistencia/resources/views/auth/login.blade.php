<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión · Control de Asistencia</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="pagina-login">
    {{-- ¿Qué pestaña se muestra? /login (docente) o /login?tipo=estudiante --}}
    @php $esEstudiante = request('tipo') === 'estudiante'; @endphp

    <div class="login">
        {{-- Panel izquierdo: presentación del sistema --}}
        <section class="login-portada">
            <span class="marca-sello marca-sello-grande">CA</span>
            <h1>Control de<br>Asistencia</h1>
            <p>Registro diario de asistencia por curso, marcación de entrada y salida, y reportes por estudiante.</p>
            <small>Universidad Francisco Gavidia · Grupo Angry Birdgos</small>
        </section>

        {{-- Panel derecho: formulario de inicio de sesión --}}
        <section class="login-formulario">
            <h2>Iniciar sesión</h2>

            {{-- Dos accesos: Docente y Estudiante (son simples enlaces) --}}
            <nav class="pestanas">
                <a href="{{ route('login') }}" class="{{ $esEstudiante ? '' : 'activa' }}">Docente</a>
                <a href="{{ route('login', ['tipo' => 'estudiante']) }}" class="{{ $esEstudiante ? 'activa' : '' }}">Estudiante</a>
            </nav>

            @if (session('ok'))
                <div class="aviso aviso-ok">{{ session('ok') }}</div>
            @endif

            @if ($esEstudiante)
                {{-- Acceso del estudiante: carnet + contraseña --}}
                <p class="tenue">Marca tu entrada y salida del día</p>
                <form action="{{ route('estudiante.login') }}" method="POST" class="formulario">
                    @csrf
                    <label class="campo">
                        <span>Carnet</span>
                        <input type="text" name="carnet" value="{{ old('carnet') }}" maxlength="8" placeholder="RR123456" class="mayusculas" required autofocus>
                        @error('carnet') <small class="error">{{ $message }}</small> @enderror
                    </label>
                    <label class="campo">
                        <span>Contraseña</span>
                        <input type="password" name="password" required>
                        @error('password') <small class="error">{{ $message }}</small> @enderror
                    </label>
                    <button type="submit" class="boton boton-principal boton-ancho">Entrar al portal</button>
                </form>
            @else
                {{-- Acceso del docente: correo + contraseña --}}
                <p class="tenue">Acceso para docentes</p>
                <form action="{{ route('login.ingresar') }}" method="POST" class="formulario">
                    @csrf {{-- Token de seguridad obligatorio en formularios POST --}}
                    <label class="campo">
                        <span>Correo</span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email') <small class="error">{{ $message }}</small> @enderror
                    </label>
                    <label class="campo">
                        <span>Contraseña</span>
                        <input type="password" name="password" required>
                        @error('password') <small class="error">{{ $message }}</small> @enderror
                    </label>
                    <label class="casilla">
                        <input type="checkbox" name="recordar"> Recordarme
                    </label>
                    <button type="submit" class="boton boton-principal boton-ancho">Entrar</button>
                </form>
            @endif
        </section>
    </div>
</body>
</html>
