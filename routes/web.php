<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::prefix('install')->group(function () {
    Route::get('/', [\App\Http\Controllers\InstallController::class, 'step1'])->name('install.step1');
    Route::post('/', [\App\Http\Controllers\InstallController::class, 'processStep1']);
    
    Route::get('/step-2', [\App\Http\Controllers\InstallController::class, 'step2'])->name('install.step2');
    Route::post('/step-2', [\App\Http\Controllers\InstallController::class, 'processStep2']);
    
    Route::get('/complete', [\App\Http\Controllers\InstallController::class, 'complete'])->name('install.complete');
    Route::post('/complete', [\App\Http\Controllers\InstallController::class, 'processComplete']);
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('servers', \App\Http\Controllers\ServerController::class)->except(['edit', 'update']);
    Route::get('servers/{server}/settings', [\App\Http\Controllers\ServerController::class, 'settings'])->name('servers.settings');
    Route::post('servers/{server}/start', [\App\Http\Controllers\ServerController::class, 'start'])->name('servers.start');
    Route::post('servers/{server}/stop', [\App\Http\Controllers\ServerController::class, 'stop'])->name('servers.stop');
    Route::post('servers/{server}/restart', [\App\Http\Controllers\ServerController::class, 'restart'])->name('servers.restart');
    Route::get('servers/{server}/users', [\App\Http\Controllers\ServerUserController::class, 'index'])->name('servers.users.index');
    Route::post('servers/{server}/users', [\App\Http\Controllers\ServerUserController::class, 'store'])->name('servers.users.store');
    Route::put('servers/{server}/users/{user}', [\App\Http\Controllers\ServerUserController::class, 'update'])->name('servers.users.update');
    Route::delete('servers/{server}/users/{user}', [\App\Http\Controllers\ServerUserController::class, 'destroy'])->name('servers.users.destroy');
    Route::post('servers/{server}/password/ftp', [\App\Http\Controllers\ServerPasswordController::class, 'updateFtp'])->name('servers.password.ftp');
    Route::post('servers/{server}/password/rcon', [\App\Http\Controllers\ServerPasswordController::class, 'updateRcon'])->name('servers.password.rcon');
    Route::post('servers/{server}/auto-restart', [\App\Http\Controllers\ServerController::class, 'toggleAutoRestart'])->name('servers.auto_restart.toggle');
    Route::post('servers/{server}/script', [\App\Http\Controllers\ServerController::class, 'updateScript'])->name('servers.script.update');
    Route::post('servers/{server}/connection', [\App\Http\Controllers\ServerController::class, 'updateConnection'])->name('servers.connection.update');
    Route::post('servers/{server}/general', [\App\Http\Controllers\ServerController::class, 'updateGeneral'])->name('servers.general.update');
    
    // Live Players
    Route::get('servers/{server}/players', [\App\Http\Controllers\ServerPlayerController::class, 'index'])->name('servers.players');
    Route::get('servers/{server}/players/list', [\App\Http\Controllers\ServerPlayerController::class, 'list'])->name('servers.players.list');
    

    // Web FTP
    Route::get('servers/{server}/ftp', [\App\Http\Controllers\WebFtpController::class, 'index'])->name('servers.ftp');
    Route::post('servers/{server}/ftp/upload', [\App\Http\Controllers\WebFtpController::class, 'upload'])->name('servers.ftp.upload');
    Route::get('servers/{server}/ftp/download', [\App\Http\Controllers\WebFtpController::class, 'download'])->name('servers.ftp.download');
    Route::post('servers/{server}/ftp/delete', [\App\Http\Controllers\WebFtpController::class, 'delete'])->name('servers.ftp.delete');
    Route::post('servers/{server}/ftp/mkdir', [\App\Http\Controllers\WebFtpController::class, 'mkdir'])->name('servers.ftp.mkdir');
    Route::post('servers/{server}/ftp/rename', [\App\Http\Controllers\WebFtpController::class, 'rename'])->name('servers.ftp.rename');
    Route::get('servers/{server}/ftp/edit', [\App\Http\Controllers\WebFtpController::class, 'edit'])->name('servers.ftp.edit');
    Route::post('servers/{server}/ftp/update', [\App\Http\Controllers\WebFtpController::class, 'update'])->name('servers.ftp.update');

    // Web RCON
    Route::get('servers/{server}/rcon', [\App\Http\Controllers\WebRconController::class, 'index'])->name('servers.rcon');
    Route::post('servers/{server}/rcon/execute', [\App\Http\Controllers\WebRconController::class, 'execute'])->name('servers.rcon.execute');



    // API Keys
    Route::get('/api-keys', [\App\Http\Controllers\ApiKeyController::class, 'index'])->name('api-keys.index');
    Route::post('/api-keys', [\App\Http\Controllers\ApiKeyController::class, 'store'])->name('api-keys.store');
    Route::delete('/api-keys/{apiKey}', [\App\Http\Controllers\ApiKeyController::class, 'destroy'])->name('api-keys.destroy');

    Route::middleware('admin')->group(function () {
        Route::resource('hosts', \App\Http\Controllers\HostController::class)->except(['show', 'edit']);
        Route::resource('games', \App\Http\Controllers\GameController::class)->except(['show']);
        Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show', 'edit', 'create']);
        Route::get('logs', [\App\Http\Controllers\LogController::class, 'index'])->name('logs.index');
    });
});
