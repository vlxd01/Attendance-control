{{-- Formulario compartido entre "crear" y "editar" --}}
<div class="rejilla-2">
    <label class="campo">
        <span>Carnet</span>
        <input type="text" name="carnet" maxlength="8" placeholder="RR123456" class="mayusculas"
               value="{{ old('carnet', $estudiante->carnet) }}" required>
        @error('carnet') <small class="error">{{ $message }}</small> @enderror
    </label>

    <label class="campo">
        <span>Correo</span>
        <input type="email" name="correo" placeholder="nombre@ufg.edu.sv"
               value="{{ old('correo', $estudiante->correo) }}" required>
        @error('correo') <small class="error">{{ $message }}</small> @enderror
    </label>

    <label class="campo">
        <span>Nombres</span>
        <input type="text" name="nombres" value="{{ old('nombres', $estudiante->nombres) }}" required>
        @error('nombres') <small class="error">{{ $message }}</small> @enderror
    </label>

    <label class="campo">
        <span>Apellidos</span>
        <input type="text" name="apellidos" value="{{ old('apellidos', $estudiante->apellidos) }}" required>
        @error('apellidos') <small class="error">{{ $message }}</small> @enderror
    </label>

    {{-- Contraseña del portal del estudiante: obligatoria al crear, opcional al editar --}}
    <label class="campo">
        <span>Contraseña del portal @if ($estudiante->exists) <em>(dejar vacía para no cambiarla)</em> @endif</span>
        <input type="password" name="password" minlength="6" autocomplete="new-password" {{ $estudiante->exists ? '' : 'required' }}>
        @error('password') <small class="error">{{ $message }}</small> @enderror
    </label>
</div>
