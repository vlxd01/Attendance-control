<?php

namespace Database\Seeders;

use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Marcacion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// Datos de ejemplo. Se ejecuta con: php artisan migrate --seed
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Usuario docente para iniciar sesión
        User::create([
            'name' => 'Docente Administrador',
            'email' => 'admin@asistencia.com',
            'password' => Hash::make('password'),
        ]);

        // 2) Cursos
        $cursos = [
            ['codigo' => 'DAW-01', 'nombre' => 'Desarrollo de Aplicaciones Web', 'docente' => 'Ing. Carlos Alberto Menjívar',
             'descripcion' => 'Desarrollo web del lado del servidor con PHP y Laravel.'],
            ['codigo' => 'BD-02', 'nombre' => 'Bases de Datos II', 'docente' => 'Inga. Patricia Elena Guardado',
             'descripcion' => 'Modelado relacional, SQL avanzado y procedimientos almacenados.'],
            ['codigo' => 'PRG-03', 'nombre' => 'Programación Orientada a Objetos', 'docente' => 'Ing. Roberto Ernesto Alvarado',
             'descripcion' => 'Clases, herencia, interfaces y buenas prácticas.'],
            ['codigo' => 'RED-01', 'nombre' => 'Redes de Computadoras', 'docente' => 'Ing. Mauricio José Rivas',
             'descripcion' => 'Fundamentos de redes, modelo OSI y TCP/IP.'],
        ];
        foreach ($cursos as $datos) {
            Curso::create($datos);
        }

        // 3) Estudiantes (carnet = iniciales de apellidos + 6 números)
        //    Todos con la contraseña "estudiante123" para entrar al portal del estudiante.
        //    Se encripta una sola vez (Hash::make) para que el seeder sea rápido.
        $claveEstudiante = Hash::make('estudiante123');
        $estudiantes = [
            ['RH100124', 'Jonathan Alexander', 'Rodríguez Hernández'],
            ['MC204523', 'María José', 'Martínez Cruz'],
            ['LA301224', 'Luis Fernando', 'López Ayala'],
            ['GP115623', 'Gabriela Beatriz', 'García Portillo'],
            ['HM220724', 'Kevin Alexis', 'Hernández Mejía'],
            ['RF318923', 'Andrea Sofía', 'Ramírez Flores'],
            ['CG127424', 'José Roberto', 'Castillo Guevara'],
            ['MR209123', 'Daniela Alejandra', 'Mejía Rivas'],
            ['PS145624', 'Carlos Ernesto', 'Pérez Serrano'],
            ['AV233823', 'Fátima Elizabeth', 'Argueta Villalobos'],
            ['OC156224', 'Diego Armando', 'Orellana Chávez'],
            ['QM248723', 'Karla Vanessa', 'Quintanilla Molina'],
            ['BL163024', 'Josué David', 'Bonilla Lemus'],
            ['SR251923', 'Alejandra Michelle', 'Sánchez Romero'],
            ['ZD172524', 'Bryan Josué', 'Zelaya Díaz'],
        ];
        foreach ($estudiantes as [$carnet, $nombres, $apellidos]) {
            // Correo: primer nombre + primer apellido, sin tildes
            $usuario = strtolower(\Illuminate\Support\Str::ascii(explode(' ', $nombres)[0] . '.' . explode(' ', $apellidos)[0]));
            Estudiante::create([
                'carnet' => $carnet,
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'correo' => $usuario . '@ufg.edu.sv',
                'password' => $claveEstudiante,
            ]);
        }

        // 4) Inscripciones (tabla pivote)
        $ids = Estudiante::orderBy('id')->pluck('id');
        Curso::where('codigo', 'DAW-01')->first()->estudiantes()->attach($ids);            // todos
        Curso::where('codigo', 'BD-02')->first()->estudiantes()->attach($ids->take(10));   // 10 primeros
        Curso::where('codigo', 'PRG-03')->first()->estudiantes()->attach($ids->slice(5));  // 10 últimos
        Curso::where('codigo', 'RED-01')->first()->estudiantes()->attach($ids->take(8));   // 8 primeros

        // 5) Asistencias de las últimas 6 clases (lunes y miércoles) para 2 cursos
        mt_srand(2026); // semilla fija: siempre se generan los mismos datos
        $fechas = [];
        $dia = Carbon::today()->subDay();
        while (count($fechas) < 6) {
            if ($dia->isMonday() || $dia->isWednesday()) {
                $fechas[] = $dia->toDateString();
            }
            $dia->subDay();
        }

        foreach (Curso::whereIn('codigo', ['DAW-01', 'BD-02'])->get() as $curso) {
            foreach ($curso->estudiantes as $estudiante) {
                foreach ($fechas as $fecha) {
                    $azar = mt_rand(1, 100);
                    $estado = $azar <= 78 ? 'presente' : ($azar <= 90 ? 'tarde' : 'ausente');

                    Asistencia::create([
                        'curso_id' => $curso->id,
                        'estudiante_id' => $estudiante->id,
                        'fecha' => $fecha,
                        'estado' => $estado,
                        'observacion' => $estado === 'tarde' ? 'Llegó 15 minutos tarde' : null,
                    ]);
                }
            }
        }

        // 5b) Algunas faltas justificadas (el motivo va en "observacion") para la demo
        $motivos = ['Cita médica en la Unidad de Salud', 'Incapacidad por gripe (constancia médica)', 'Representó a la UFG en torneo deportivo'];
        $registrosAusentes = Asistencia::where('estado', 'ausente')->orderBy('id')->take(3)->get();
        foreach ($registrosAusentes as $i => $registro) {
            $registro->update(['estado' => 'justificado', 'observacion' => $motivos[$i]]);
        }

        // 6) Marcaciones de entrada y salida de los últimos 5 días hábiles
        $dias = [];
        $dia = Carbon::today()->subDay();
        while (count($dias) < 5) {
            if (! $dia->isWeekend()) {
                $dias[] = $dia->toDateString();
            }
            $dia->subDay();
        }

        $todos = Estudiante::orderBy('id')->get();
        foreach ($dias as $fecha) {
            foreach ($todos as $estudiante) {
                if (mt_rand(1, 100) > 88) {
                    continue; // algunos días el estudiante no llegó
                }
                $entrada = Carbon::parse($fecha . ' 06:50')->addMinutes(mt_rand(0, 50));
                $salida = $entrada->copy()->addMinutes(mt_rand(240, 330)); // entre 4 h y 5 h 30 min

                Marcacion::create([
                    'estudiante_id' => $estudiante->id,
                    'fecha' => $fecha,
                    'hora_entrada' => $entrada->format('H:i:s'),
                    'hora_salida' => $salida->format('H:i:s'),
                ]);
            }
        }

        // Hoy: 5 estudiantes ya marcaron entrada (todavía sin salida).
        // El primero (RH100124) queda libre para probar "Marcar entrada" en la demostración.
        foreach ($todos->slice(1, 5) as $estudiante) {
            Marcacion::create([
                'estudiante_id' => $estudiante->id,
                'fecha' => Carbon::today()->toDateString(),
                'hora_entrada' => Carbon::parse('06:50')->addMinutes(mt_rand(0, 40))->format('H:i:s'),
            ]);
        }
    }
}
