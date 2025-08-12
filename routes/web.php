<?php
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\SubareaController;
use App\Http\Controllers\SeccionController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\RecintoController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\TipoRecintoController;
use App\Http\Controllers\EstadoRecintoController;
use App\Http\Controllers\LlaveController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {

    


    //return view('welcome');
    return view('Template-administrador');
    //return view('Template-profesor');
    //return view('Template-soporte');

});



Route::resource('bitacora', BitacoraController::class);

// Rutas que requieren autenticación
Route::middleware(['auth', 'verified'])->group(function () {
    // Rutas de recursos con protección para directores en acciones de escritura

    Route::resource('role', RoleController::class);
    
    Route::resource('usuario', UsuarioController::class)->except(['store', 'update', 'destroy']);
    Route::resource('usuario', UsuarioController::class)->only(['store', 'update', 'destroy'])->middleware('director.readonly');

    Route::resource('institucion', InstitucionController::class)->except(['store', 'update', 'destroy']);
    Route::resource('institucion', InstitucionController::class)->only(['store', 'update', 'destroy'])->middleware('director.readonly');

    Route::resource('seccion', SeccionController::class)->except(['store', 'update', 'destroy']);
    Route::resource('seccion', SeccionController::class)->only(['store', 'update', 'destroy'])->middleware('director.readonly');

    Route::resource('subarea', SubareaController::class)->except(['store', 'update', 'destroy']);
    Route::resource('subarea', SubareaController::class)->only(['store', 'update', 'destroy'])->middleware('director.readonly');

    Route::resource('especialidad', EspecialidadController::class)->except(['store', 'update', 'destroy']);
    Route::resource('especialidad', EspecialidadController::class)->only(['store', 'update', 'destroy'])->middleware('director.readonly');


    Route::resource('recinto', RecintoController::class);


    Route::resource('horario', HorarioController::class);
    Route::resource('tipoRecinto', TipoRecintoController::class);


    Route::resource('estadoRecinto', EstadoRecintoController::class);

    Route::resource('llave', LlaveController::class);

    Route::view('/template-administrador', 'template-administrador')->name('template-administrador');
    Route::view('/template-profesor', 'template-profesor')->name('template-profesor');
    Route::view('/template-soporte', 'template-soporte')->name('template-soporte');

});

Route::get('/dashboard', function () {
    $user = Auth::user();
    
    if ($user->hasRole('administrador')) {
        return redirect('/template-administrador');
    } elseif ($user->hasRole('director')) {
        return redirect('/template-administrador');
    } elseif ($user->hasRole('profesor')) {
        return redirect('/template-profesor');
    } elseif ($user->hasRole('soporte')) {
        return redirect('/template-soporte');
    }
    
    return view('template-soporte');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

