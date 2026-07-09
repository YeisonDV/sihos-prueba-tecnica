<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\ReporteController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use App\Http\Controllers\PacienteController;

Route::middleware(['auth'])->group(function () {
    Route::get('/pacientes-view', function () {
        return view('pacientes.index');
    })->name('pacientes.view');


    Route::get('/departamentos', [CatalogoController::class, 'departamentos']);
    Route::get('/tipos-documento', [CatalogoController::class, 'tiposDocumento']);
    Route::get('/generos', [CatalogoController::class, 'generos']);
    Route::get('/municipios/{departamentoId}', [CatalogoController::class, 'municipiosPorDepartamento']);
    Route::get('/reportes/pacientes-por-departamento', [ReporteController::class, 'pacientesPorDepartamento']);

    Route::apiResource('pacientes', PacienteController::class)->except(['create', 'edit']);
});


require __DIR__.'/auth.php';
