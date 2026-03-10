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
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AbsensiSholatController;
use App\Http\Controllers\iclockController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;


Route::get('devices', [DeviceController::class, 'Index'])->name('devices.index');

Route::get('/users', [UsersController::class, 'Index'])->name('users.index')->middleware('auth');
Route::get('get-user-permissions', [UsersController::class, 'getUserPermissions'])->name('users.get-user-permissions')->middleware('auth');
Route::get('get-permissions', [UsersController::class, 'getPermissions'])->name('users.get-permissions')->middleware('auth');
Route::post('new-user', [UsersController::class, 'store'])->name('users.new-user')->middleware('auth');
Route::post('drop-user', [UsersController::class, 'destroy'])->name('users.drop-user');

Route::get('devices-log', [DeviceController::class, 'DeviceLog'])->name('devices.DeviceLog')->middleware('auth');
Route::get('finger-log', [DeviceController::class, 'FingerLog'])->name('devices.FingerLog')->middleware('auth');
Route::get('attendance', [DeviceController::class, 'Attendance'])->name('devices.Attendance')->middleware('auth');
route::get('attphoto', [DeviceController::class, 'AttPhoto'])->name('devices.AttPhoto')->middleware('auth');
Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index')->middleware('auth');
Route::get('list-employees', [EmployeeController::class, 'ListEmployees'])->name('employee.list-employees')->middleware('auth');
Route::post('employees/upload-photo', [EmployeeController::class, 'UploadPhoto'])->name('employee.upload-photo')->middleware('auth');
Route::post('employees/EditEmployeeData', [EmployeeController::class, 'EditEmployeeData'])->name('employee.EditEmployeeData')->middleware('auth');
Route::post('devices/download', [DeviceController::class, 'Download'])->name('devices.download')->middleware('auth');
Route::post('devices/delete-data', [DeviceController::class, 'DeleteData'])->name('devices.delete-data')->middleware('auth');
Route::post('devices/delete-employee', [DeviceController::class, 'DeleteEmployee'])->name('devices.delete-employee')->middleware('auth');
Route::post('devices/get-device-config', [DeviceController::class, 'GetDeviceConfig'])->name('devices.get-device-config')->middleware('auth');
Route::post('devices/save-device-config', [DeviceController::class, 'SaveDeviceConfig'])->name('devices.save-device-config')->middleware('auth');
Route::post('devices/upload', [DeviceController::class, 'Upload'])->name('devices.upload')->middleware('auth');
Route::post('devices/restart', [DeviceController::class, 'RestartDevice'])->name('devices.restart')->middleware('auth');
Route::post('devices/clear-admin', [DeviceController::class, 'ClearAdmin'])->name('devices.clear-admin')->middleware('auth');
Route::post('devices/clear-log', [DeviceController::class, 'ClearLog'])->name('devices.clear-log')->middleware('auth');
Route::post('devices/enroll', [DeviceController::class, 'EnrollEmployee'])->name('devices.enroll')->middleware('auth');
Route::post('devices/set-photo-config', [DeviceController::class, 'SetPhotoConfig'])->name('devices.set-photo-config')->middleware('auth');
Route::post('devices/set-duplicate-time', [DeviceController::class, 'SetDuplicateTime'])->name('devices.set-duplicate-time')->middleware('auth');
Route::get('/attlog', [App\Http\Controllers\AttlogController::class, 'index']);

// handshake
Route::get('/iclock/cdata', [iclockController::class, 'handshake']);
// request dari device
Route::post('/iclock/cdata', [iclockController::class, 'receiveRecords']);
// endpoint fotos
Route::post('/iclock/fdata', [iclockController::class, 'fdata']);
//COMMANDS
Route::any('/iclock/devicecmd', [iclockController::class, 'deviceCmdResponse']);
//ping
Route::get('/iclock/ping', [iclockController::class, 'ping']);

Route::get('/iclock/test', [iclockController::class, 'test']);
Route::any('/iclock/getrequest', [iclockController::class, 'getrequest']);

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::get('/', function () {
    return redirect('devices') ;
});
