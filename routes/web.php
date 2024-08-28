<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('auth.index');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

Route::get('/loaderio-50f1bbd2e4cb90fc832d72fbed3c62e4', function() {
    return response("loaderio-50f1bbd2e4cb90fc832d72fbed3c62e4");
});

Route::middleware(['auth', 'ping.task'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

    Route::get('/', [DashboardController::class, 'root'])->name('root');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::get('/station', [StationController::class, 'index'])->name('station.index');

    Route::get('/manage', [ManageController::class, 'index'])->name('manage.index');

    Route::get('/logs', [LogsController::class, 'index'])->name('logs.index');

    Route::get('/me/settings', [MeController::class, 'settings'])->name('me.settings');
    Route::post('/me/submit', [MeController::class, 'submit'])->name('me.submit');
    
    Route::middleware('admin')->group(function () {
        Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
        Route::get('/scan/scanner', [ScanController::class, 'scanner'])->name('scan.scanner');
        Route::post('/scan/form', [ScanController::class, 'form'])->name('scan.form');

        Route::get('/manage/params', [ManageController::class, 'params'])->name('manage.params');
        Route::get('/manage/station', [ManageController::class, 'station'])->name('manage.station');

        Route::get('/download', [DownloadController::class, 'index'])->name('download.index');
        Route::get('/download/test', [DownloadController::class, 'test'])->name('download.test');
        
        Route::get('/users', [UsersController::class, 'index'])->name('users.index');
        Route::get('/users/add', [UsersController::class, 'add'])->name('users.add');
        Route::get('/users/edit', [UsersController::class, 'edit'])->name('users.edit');
    });

    Route::get('/uml', function () {
        abort(404);
    });
});