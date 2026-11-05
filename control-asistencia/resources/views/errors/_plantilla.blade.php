{{-- Plantilla sencilla para páginas de error en español --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $codigo }} · Control de Asistencia</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="pagina-login">
    <section class="tarjeta pagina-error">
        <p class="antetitulo">Error {{ $codigo }}</p>
        <h1>{{ $titulo }}</h1>
        <p class="tenue">{{ $texto }}</p>
        <a href="{{ url('/') }}" class="boton boton-principal">Volver al inicio</a>
    </section>
</body>
</html>
