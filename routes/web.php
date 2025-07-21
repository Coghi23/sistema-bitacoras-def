<?php
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\EspecialidadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Template-administrador');
});


Route::resource('institucion', InstitucionController::class);
Route::resource('especialidad', EspecialidadController::class);
