<?php

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
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\AbsensiSholatController;
use App\Http\Controllers\iclockController;
use App\Http\Controllers\AuthController;


Route::get('devices', [DeviceController::class, 'Index'])->name('devices.index');
Route::get('devices-log', [DeviceController::class, 'DeviceLog'])->name('devices.DeviceLog')->middleware('auth');
Route::get('finger-log', [DeviceController::class, 'FingerLog'])->name('devices.FingerLog')->middleware('auth');
Route::get('attendance', [DeviceController::class, 'Attendance'])->name('devices.Attendance')->middleware('auth');
Route::post('devices/restart', [DeviceController::class, 'RestartDevice'])->name('devices.restart')->middleware('auth');
Route::post('devices/clear-admin', [DeviceController::class, 'ClearAdmin'])->name('devices.clear-admin')->middleware('auth');
Route::post('devices/clear-log', [DeviceController::class, 'ClearLog'])->name('devices.clear-log')->middleware('auth');

// handshake
Route::get('/iclock/cdata', [iclockController::class, 'handshake']);
// request dari device
Route::post('/iclock/cdata', [iclockController::class, 'receiveRecords']);
//COMMANDS
Route::any('/iclock/devicecmd', [iclockController::class, 'deviceCmdResponse']);

Route::get('/iclock/test', [iclockController::class, 'test']);
Route::any('/iclock/getrequest', [iclockController::class, 'getrequest']);

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::get('/', function () {
    return redirect('devices') ;
});
