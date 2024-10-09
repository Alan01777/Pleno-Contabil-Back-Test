<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\FileController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\UserPushTokenController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::prefix('auth')->group(function () {
  Route::post('login', [AuthController::class, 'login'])->name('login');
  Route::post('register', [AuthController::class, 'register'])->name('register');

  Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('user', [AuthController::class, 'user'])->name('user');
  });

  Route::prefix('password')->name('password.')->group(function () {
    Route::post('recovery', [AuthController::class, 'sendPasswordRecoveryToken'])->name('send_recovery_token');
    Route::post('validate', [AuthController::class, 'validateToken'])->name('validate_token');
    Route::put('reset', [AuthController::class, 'resetPassword'])->name('update_password');
  });
});

// File routes
Route::middleware('auth:sanctum')->group(function () {
  Route::get('/files/{directory?}', [FileController::class, 'listFilesInDirectory'])->where('directory', '.*')->name('files.list');
  Route::get('/file/{path}', [FileController::class, 'getFile'])->where('path', '.*')->name('file.get');
  Route::post('/upload', [FileController::class, 'uploadFile'])->name('file.upload');
  Route::delete('/file/{path}', [FileController::class, 'deleteFile'])->where('path', '.*')->name('file.delete');
});


// Push Token Notifications
Route::middleware('auth:sanctum')->group(function () {
  Route::post('/pushToken', [UserPushTokenController::class, 'storeToken']);
});

Route::post('/minio-webhook', 'MinioController@handleWebhook');

// User routes
Route::middleware('auth:sanctum')->prefix('user')->group(function () {
  Route::get('/', [UserController::class, 'getUser'])->name('get');
  Route::put('/update', [UserController::class, 'updateUser'])->name('update');
});