# Documentación: Manejo del Estado en Eventos

## 1. Modelo (`app/Models/Evento.php`)
- El campo `estado` está en `$fillable`, permitiendo su asignación masiva.

## 2. Migración (`database/migrations/2025_06_02_003001_evento.php`)
- El campo `estado` está definido como:
  ```php
  $table->enum('estado', ['en_espera', 'en_proceso', 'completado'])->default('en_espera')->after('prioridad');
  ```

## 3. Controlador (`app/Http/Controllers/EventoController.php`)
- El método `update` valida que `estado` sea obligatorio y uno de los tres valores.
- Asigna el valor validado a `$evento->estado` y lo guarda.
- Maneja errores de validación y otros, retornando mensajes claros.
- Usa logs para depuración.

## 4. Vistas Blade
- `index_soporte.blade.php`: muestra y permite editar el estado con un `<select>`.
- `index_profesor.blade.php` y `index.blade.php`: muestran el estado en la tabla.

## 5. JavaScript (en `index_soporte.blade.php`)
- La función `guardarEstado(id)` envía el nuevo estado al backend usando PATCH y JSON.
- Maneja la respuesta y muestra mensajes al usuario.

## 6. Rutas
- La ruta PATCH `/evento/{id}` apunta a `update` en el controlador.

## 7. Flujo resumido
1. El usuario selecciona un nuevo estado en el modal.
2. El JS envía el valor al backend.
3. El backend valida y guarda el nuevo estado si es válido.
4. El frontend muestra el resultado.

---

**Notas:**
- Si el estado no cambia, revisa los logs de Laravel para errores de validación o base de datos.
- El sistema solo acepta los valores: `en_espera`, `en_proceso`, `completado`.
- El flujo es robusto y seguro si se siguen estas reglas.
