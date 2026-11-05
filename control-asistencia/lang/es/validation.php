<?php

// Mensajes de validación en español.
// Laravel los usa automáticamente porque APP_LOCALE=es en el archivo .env
return [
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser texto.',
    'email' => 'El campo :attribute debe ser un correo válido.',
    'unique' => 'El :attribute ya está registrado.',
    'exists' => 'El :attribute seleccionado no existe.',
    'in' => 'El valor de :attribute no es válido.',
    'date' => 'El campo :attribute no es una fecha válida.',
    'array' => 'El campo :attribute debe ser una lista.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'regex' => 'El formato de :attribute no es válido.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'before_or_equal' => 'La :attribute debe ser una fecha anterior o igual a :date.',
    'after_or_equal' => 'La :attribute debe ser una fecha posterior o igual a :date.',
    'max' => [
        'string' => 'El campo :attribute no debe tener más de :max caracteres.',
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'array' => 'El campo :attribute no debe tener más de :max elementos.',
    ],
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
    ],

    // Nombres "bonitos" de los campos para los mensajes
    'attributes' => [
        'carnet' => 'carnet',
        'nombres' => 'nombres',
        'apellidos' => 'apellidos',
        'correo' => 'correo',
        'codigo' => 'código',
        'nombre' => 'nombre',
        'docente' => 'docente',
        'descripcion' => 'descripción',
        'curso_id' => 'curso',
        'fecha' => 'fecha',
        'estados' => 'estados',
        'estados.*' => 'estado',
        'observaciones.*' => 'observación',
        'desde' => 'fecha inicial',
        'hasta' => 'fecha final',
        'email' => 'correo',
        'password' => 'contraseña',
        'estudiantes' => 'estudiantes',
    ],
];
