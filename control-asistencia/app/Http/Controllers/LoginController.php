<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Muestra el formulario de inicio de sesión
    public function mostrar()
    {
        return view('auth.login');
    }

    // Valida las credenciales e inicia sesión
    public function ingresar(Request $request)
    {
        $credenciales = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Auth::attempt busca al usuario por email y compara la contraseña encriptada
        if (Auth::attempt($credenciales, $request->boolean('recordar'))) {
            $request->session()->regenerate(); // Seguridad: nueva sesión
            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['email' => 'El correo o la contraseña son incorrectos.'])
            ->onlyInput('email');
    }

    // Inicio de sesión del ESTUDIANTE: carnet + contraseña, con el guard "estudiante"
    public function ingresarEstudiante(Request $request)
    {
        $request->merge(['carnet' => strtoupper(trim($request->input('carnet', '')))]);

        $credenciales = $request->validate([
            'carnet' => 'required',
            'password' => 'required',
        ]);

        // Mismo Auth::attempt, pero busca en la tabla estudiantes (config/auth.php)
        if (Auth::guard('estudiante')->attempt($credenciales)) {
            $request->session()->regenerate();
            return redirect()->route('estudiante.panel');
        }

        return back()
            ->withErrors(['carnet' => 'El carnet o la contraseña son incorrectos.'])
            ->onlyInput('carnet');
    }

    // Cierra la sesión del estudiante
    public function salirEstudiante(Request $request)
    {
        Auth::guard('estudiante')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login', ['tipo' => 'estudiante'])->with('ok', 'Sesión cerrada correctamente.');
    }

    // Cierra la sesión del docente
    public function salir(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('ok', 'Sesión cerrada correctamente.');
    }
}
