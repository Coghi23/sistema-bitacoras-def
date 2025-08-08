<?php
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\SubareaController;
use App\Http\Controllers\SeccionController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\RecintoController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\TipoRecintoController;
use App\Http\Controllers\EstadoRecintoController;
use App\Http\Controllers\LlaveController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Template-profesor');
});

Route::resource('bitacora', BitacoraController::class);

Route::resource('institucion', InstitucionController::class);

Route::resource('seccion', SeccionController::class);

Route::resource('subarea', SubareaController::class);

Route::resource('especialidad', EspecialidadController::class);

Route::resource('recinto', RecintoController::class);

Route::resource('horario', HorarioController::class);

Route::resource('tipoRecinto', TipoRecintoController::class);

Route::resource('estadoRecinto', EstadoRecintoController::class);

Route::resource('llave', LlaveController::class);