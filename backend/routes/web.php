<?php
use Illuminate\Support\Facades\Route;

// Controladores de Administración
use App\Http\Controllers\Administracion\AuthController;
use App\Http\Controllers\Administracion\UserController;
use App\Http\Controllers\Administracion\RolController;
use App\Http\Controllers\Administracion\BitacoraController;

// Controladores de Configuración Académica
use App\Http\Controllers\ConfiguracionAcademica\GestionController;
use App\Http\Controllers\ConfiguracionAcademica\DocenteController;
use App\Http\Controllers\ConfiguracionAcademica\MateriaController;
use App\Http\Controllers\ConfiguracionAcademica\GrupoController;
use App\Http\Controllers\ConfiguracionAcademica\AulaController;

// Controladores de Planificación
use App\Http\Controllers\Planificacion\HorarioController;

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren Autenticación)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function() {
        $stats = [
            'docentes' => \App\Models\ConfiguracionAcademica\Docente::where('estado', true)->count(),
            'materias' => \App\Models\ConfiguracionAcademica\Materia::count(),
            'aulas' => \App\Models\ConfiguracionAcademica\Aula::count(),
            'horarios' => \App\Models\Planificacion\Horario::count()
        ];
        
        $gestionActual = \App\Models\ConfiguracionAcademica\GestionAcademica::where('estado', true)->first();
        
        return view('dashboard', compact('stats', 'gestionActual'));
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | PAQUETE: ADMINISTRACIÓN
    |--------------------------------------------------------------------------
    */
    Route::prefix('administracion')->name('administracion.')->group(function () {
        
        // Usuarios
        Route::resource('usuarios', UserController::class);
        
        // Roles (si necesitas gestionar roles)
        Route::resource('roles', RolController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        
        // Bitácora
        Route::get('bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
    });

    /*
    |--------------------------------------------------------------------------
    | PAQUETE: CONFIGURACIÓN ACADÉMICA
    |--------------------------------------------------------------------------
    */
    Route::prefix('configuracion-academica')->name('configuracion-academica.')->group(function () {
        
        // Gestiones Académicas
        Route::resource('gestiones', GestionController::class);
        
        // Docentes
        Route::resource('docentes', DocenteController::class);
        
        // Materias
        Route::resource('materias', MateriaController::class);
        
        // Grupos
        Route::resource('grupos', GrupoController::class);
        
        // Aulas
        Route::resource('aulas', AulaController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | PAQUETE: PLANIFICACIÓN
    |--------------------------------------------------------------------------
    */
    Route::prefix('planificacion')->name('planificacion.')->group(function () {
        
        // Horarios
        Route::resource('horarios', HorarioController::class);
        
        // Rutas adicionales para horarios si necesitas
        Route::get('horarios/grupo/{id}', [HorarioController::class, 'porGrupo'])->name('horarios.grupo');
        Route::get('horarios/docente/{id}', [HorarioController::class, 'porDocente'])->name('horarios.docente');
    });
});

// ============================================
// EJEMPLO DE MIDDLEWARE PERSONALIZADO (OPCIONAL)
// Si necesitas restringir por roles
// ============================================
/*
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('administracion/usuarios', UserController::class);
});

Route::middleware(['auth', 'role:coordinador'])->group(function () {
    Route::resource('configuracion-academica/gestiones', GestionController::class);
});
*/