<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\ESPController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/esp/station', [ESPController::class, 'station']);
Route::post('/esp/start', [ESPController::class, 'start']);
Route::post('/esp/data', [ESPController::class, 'data']);
Route::post('/esp/eta', [ESPController::class, 'eta']);
Route::post('/esp/end', [ESPController::class, 'end']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/datetime', function() {
        return date('Y-m-d H:i:s');
    })->name('datetime');

    Route::get('/dashboard/load', [DashboardController::class, 'load'])->name('dashboard.load');
    Route::get('/dashboard/statistics', [DashboardController::class, 'statistics'])->name('dashboard.statistics');
    Route::get('/dashboard/table', [DashboardController::class, 'table'])->name('dashboard.table');
    Route::post('/dashboard/action', [DashboardController::class, 'action'])->name('dashboard.action');

    Route::get('/station/load', [StationController::class, 'load'])->name('station.load');
    Route::get('/station/update', [StationController::class, 'update'])->name('station.update');
    Route::get('/station/date', [StationController::class, 'date'])->name('station.date');

    Route::get('/manage/table', [ManageController::class, 'table'])->name('manage.table');
    Route::post('/manage/action', [ManageController::class, 'action'])->name('manage.action');

    Route::get('/logs/table', [LogsController::class, 'table'])->name('logs.table');

    Route::get('/me/username', [MeController::class, 'username'])->name('me.username');
    Route::get('/me/cert', [MeController::class, 'cert'])->name('me.cert');

    Route::middleware('admin')->group(function () {
        Route::get('/scan/verify', [ScanController::class, 'verify'])->name('scan.verify');
        Route::get('/scan/name', [ScanController::class, 'name'])->name('scan.name');
        Route::get('/scan/ping', [ScanController::class, 'ping'])->name('scan.ping');
        Route::post('/scan/submit', [ScanController::class, 'submit'])->name('scan.submit');

        Route::get('/manage/running', [ManageController::class, 'running'])->name('manage.running');
        Route::post('/manage/params/submit', [ManageController::class, 'params_submit'])->name('manage.params.submit');
        Route::post('/manage/station/update', [ManageController::class, 'station_update'])->name('manage.station.update');
        Route::get('/manage/station/name', [ManageController::class, 'station_name'])->name('manage.station.name');
        Route::post('/manage/station/config', [ManageController::class, 'station_config'])->name('manage.station.config');
        Route::post('/manage/station/delete', [ManageController::class, 'station_delete'])->name('manage.station.delete');
        
        Route::get('/download/session', [DownloadController::class, 'session'])->name('download.session');
        Route::get('/download/preview', [DownloadController::class, 'preview'])->name('download.preview');
        Route::get('/download/datacsv', [DownloadController::class, 'datacsv'])->name('download.datacsv');
        Route::get('/download/dataxlsx', [DownloadController::class, 'dataxlsx'])->name('download.dataxlsx');

        Route::get('/users/table', [UsersController::class, 'table'])->name('users.table');
        Route::get('/users/username', [UsersController::class, 'username'])->name('users.username');
        Route::post('/users/submit', [UsersController::class, 'submit'])->name('users.submit');
        Route::post('/users/delete', [UsersController::class, 'delete'])->name('users.delete');

        Route::get('/me/logs', [MeController::class, 'logs'])->name('me.logs');
    });
});