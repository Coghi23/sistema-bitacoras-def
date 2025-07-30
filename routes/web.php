<?php
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\SubareaController;
use App\Http\Controllers\SeccionController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\RecintoController;
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