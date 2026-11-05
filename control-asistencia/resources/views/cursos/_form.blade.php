{{-- Formulario compartido entre "crear" y "editar" --}}
<div class="rejilla-2">
    <label class="campo">
        <span>Código</span>
        <input type="text" name="codigo" maxlength="20" placeholder="DAW-01" class="mayusculas"
               value="{{ old('codigo', $curso->codigo) }}" required>
        @error('codigo') <small class="error">{{ $message }}</small> @enderror
    </label>

    <label class="campo">
        <span>Docente</span>
        <input type="text" name="docente" value="{{ old('docente', $curso->docente) }}">
        @error('docente') <small class="error">{{ $message }}</small> @enderror
    </label>
</div>

<label class="campo">
    <span>Nombre del curso</span>
    <input type="text" name="nombre" value="{{ old('nombre', $curso->nombre) }}" required>
    @error('nombre') <small class="error">{{ $message }}</small> @enderror
</label>

<label class="campo">
    <span>Descripción <em>(opcional)</em></span>
    <textarea name="descripcion" rows="3">{{ old('descripcion', $curso->descripcion) }}</textarea>
    @error('descripcion') <small class="error">{{ $message }}</small> @enderror
</label>
