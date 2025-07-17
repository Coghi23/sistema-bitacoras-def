<?php
use App\Http\Controllers\InstitucionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Template-administrador');
});


Route::resources([
    'institucion' => InstitucionController::class,
]);
