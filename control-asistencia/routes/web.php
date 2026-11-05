<?php

use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MarcacionController;
use App\Http\Controllers\PortalEstudianteController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;

// ---------- Rutas públicas (solo para quien NO ha iniciado sesión, ni docente ni estudiante) ----------
Route::middleware('guest:web,estudiante')->group(function () {
    Route::get('/login', [LoginController::class, 'mostrar'])->name('login');
    Route::post('/login', [LoginController::class, 'ingresar'])->name('login.ingresar');
    Route::post('/estudiante/login', [LoginController::class, 'ingresarEstudiante'])->name('estudiante.login');
});

// ---------- Portal del ESTUDIANTE (guard "estudiante") ----------
Route::middleware('auth:estudiante')->prefix('estudiante')->name('estudiante.')->group(function () {
    Route::get('/', [PortalEstudianteController::class, 'panel'])->name('panel');
    Route::post('/entrada', [PortalEstudianteController::class, 'marcarEntrada'])->name('entrada');
    Route::post('/salida', [PortalEstudianteController::class, 'marcarSalida'])->name('salida');
    Route::post('/salir', [LoginController::class, 'salirEstudiante'])->name('salir');
});

// ---------- Panel del DOCENTE (auth:web = solo usuarios docentes; un estudiante no entra aquí) ----------
Route::middleware('auth:web')->group(function () {
    Route::post('/logout', [LoginController::class, 'salir'])->name('logout');

    // Inicio / dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD completo (index, create, store, show, edit, update, destroy)
    Route::resource('estudiantes', EstudianteController::class);
    Route::resource('cursos', CursoController::class);

    // Inscribir estudiantes en un curso
    Route::put('/cursos/{curso}/inscripciones', [CursoController::class, 'inscribir'])->name('cursos.inscribir');

    // Tomar asistencia
    Route::get('/asistencias', [AsistenciaController::class, 'index'])->name('asistencias.index');
    Route::post('/asistencias', [AsistenciaController::class, 'guardar'])->name('asistencias.guardar');

    // Consulta de asistencias por fecha
    Route::get('/asistencias/consulta', [ConsultaController::class, 'index'])->name('asistencias.consulta');

    // Reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/csv', [ReporteController::class, 'exportarCsv'])->name('reportes.csv');
    Route::get('/reportes/estudiante/{estudiante}', [ReporteController::class, 'estudiante'])->name('reportes.estudiante');

    // Marcaciones de entrada y salida de los estudiantes
    Route::get('/marcaciones', [MarcacionController::class, 'index'])->name('marcaciones.index');
});
