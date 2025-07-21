<?php
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\SeccionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Template-administrador');
});


Route::resource('institucion', InstitucionController::class);
Route::resource('seccion', SeccionController::class);
