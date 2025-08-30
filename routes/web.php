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
use App\Http\Controllers\QrController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\PermisosController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {


    return view('welcome');


});



Route::resource('bitacora', BitacoraController::class);


Route::middleware(['auth', 'verified'])->group(function () {
    // Rutas de recursos con protección para directores en acciones de escritura

    Route::resource('role', RoleController::class);
    Route::resource('permisos', PermisosController::class);

    Route::resource('usuario', UsuarioController::class)->except(['store', 'update', 'destroy']);
    Route::resource('usuario', UsuarioController::class)->only(['store', 'update', 'destroy'])->middleware('director.readonly');
    
    // Ruta para reenviar email de configuración de contraseña
    Route::post('/usuario/{usuario}/resend-password-setup', [UsuarioController::class, 'resendPasswordSetup'])
        ->name('usuario.resend-password-setup')
        ->middleware('director.readonly');

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

    Route::view('evento', EventoController::class);

    Route::view('/template-administrador', 'Template-administrador')->name('template-administrador');
    Route::view('/template-profesor', 'Template-profesor')->name('template-profesor');
    Route::view('/template-soporte', 'Template-soporte')->name('template-soporte');
    

});

    // Rutas para gestión de QR temporales
    Route::middleware(['auth'])->group(function () {
        // Para profesores
        Route::middleware('role:profesor')->group(function () {
            Route::get('/profesor/llaves', [QrController::class, 'indexProfesor'])->name('profesor.llaves.index');
            Route::post('/qr/generar', [QrController::class, 'generarQr'])->name('qr.generar');
        });
        
        // Ruta temporal para debug (sin middleware de rol)
        Route::get('/test-profesor-llaves', [QrController::class, 'indexProfesor'])->name('test.profesor.llaves');
        
        // NUEVAS RUTAS PARA PROFESOR-LLAVE (estructura separada)
        Route::middleware('role:profesor')->group(function () {
            Route::get('/profesor-llave', [App\Http\Controllers\ProfesorLlaveController::class, 'index'])->name('profesor-llave.index');
            Route::get('/profesor-llave/scanner', [App\Http\Controllers\ProfesorLlaveController::class, 'scanner'])->name('profesor-llave.scanner');
            Route::post('/profesor-llave/generar-qr', [App\Http\Controllers\ProfesorLlaveController::class, 'generarQr'])->name('profesor-llave.generar-qr');
            Route::post('/profesor-llave/escanear-qr', [App\Http\Controllers\ProfesorLlaveController::class, 'escanearQr'])->name('profesor-llave.escanear-qr');
            Route::get('/profesor-llave/qrs-realtime', [App\Http\Controllers\ProfesorLlaveController::class, 'getQRsRealTime'])->name('profesor-llave.qrs-realtime');
        });
        
        // Para administradores
        Route::middleware('role:administrador|director')->group(function () {
            Route::get('/admin/qr', [QrController::class, 'indexAdmin'])->name('admin.qr.index');
            
            // Rutas para datos en tiempo real del administrador
            Route::get('/admin/llaves/realtime', [LlaveController::class, 'getLlavesRealTime'])->name('admin.llaves.realtime');
            Route::get('/admin/qr/realtime-data', [LlaveController::class, 'getQRsTemporalesRealTime'])->name('admin.qr.realtime');
        });
        
        // Ruta para escanear QR (disponible para ambos roles)
        Route::post('/qr/escanear', [QrController::class, 'escanearQr'])->name('qr.escanear');
    });

        // Rutas específicas por rol (usar la misma ruta pero con diferentes nombres)
        Route::middleware(['role:administrador|director'])->group(function () {
            Route::get('/template-administrador', function () {
                return view('Template-administrador');
            })->name('template-administrador');
        });
        
        // Rutas para profesor
        Route::middleware('role:profesor')->group(function () {
            Route::get('/template-profesor', function () {
                return view('Template-profesor');
            })->name('template-profesor');
        });
        
        // Rutas para soporte
        Route::middleware('role:soporte')->group(function () {
            Route::get('/template-soporte', function () {
                return view('Template-soporte');
            })->name('template-soporte');
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

})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Ruta temporal para limpiar caché desde el navegador (eliminar después de usar por seguridad)
Route::get('/clear-cache', function () {
    \Artisan::call('cache:clear');
    \Artisan::call('config:clear');
    \Artisan::call('view:clear');
    \Artisan::call('route:clear');
    if (\Artisan::output()) {
        return 'Cache cleared!';
    }else {
        return 'No cache to clear.';
    }
});

require __DIR__.'/auth.php';

