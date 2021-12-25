<?php
use Dawnstar\Developer\Http\Controllers\DeveloperController;
use Dawnstar\Developer\Http\Controllers\DatabaseBackupController;
use Dawnstar\Developer\Http\Controllers\VcsController;

Route::get('/', [DeveloperController::class, 'index'])->name('index');
Route::get('/command', [DeveloperController::class, 'command'])->name('command');
Route::get('/env', [DeveloperController::class, 'env'])->name('env');
Route::put('/env', [DeveloperController::class, 'envUpdate'])->name('env.update');
Route::post('/maintenance', [DeveloperController::class, 'maintenance'])->name('maintenance');

Route::prefix('database')->as('database.')->group(function () {
    Route::get('/', [DatabaseBackupController::class, 'index'])->name('index');
    Route::post('/download', [DatabaseBackupController::class, 'download'])->name('download');
    Route::delete('/delete', [DatabaseBackupController::class, 'delete'])->name('delete');
    Route::post('/export', [DatabaseBackupController::class, 'export'])->name('export');
    Route::post('/import', [DatabaseBackupController::class, 'import'])->name('import');
});
Route::prefix('vcs')->as('vcs.')->group(function () {
    Route::get('/', [VcsController::class, 'index'])->name('index');
    Route::post('/checkout', [VcsController::class, 'checkout'])->name('checkout');
    Route::post('/merge', [VcsController::class, 'merge'])->name('merge');
});