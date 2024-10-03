<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\FileController;
use App\Http\Controllers\Api\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
  Route::post('login', [AuthController::class, 'login'])->name('login');
  Route::post('register', [AuthController::class, 'register'])->name('register');

  Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('user', [AuthController::class, 'user'])->name('user');
  });
});

Route::middleware('auth:sanctum')->group(function () {
  Route::get('/files/{directory?}', [FileController::class, 'listFilesInDirectory'])->where('directory', '.*')->name('files.list');
  Route::get('/file/{path}', [FileController::class, 'getFile'])->where('path', '.*')->name('file.get');
  Route::post('/upload', [FileController::class, 'uploadFile'])->name('file.upload');
  Route::delete('/file/{path}', [FileController::class, 'deleteFile'])->where('path', '.*')->name('file.delete');
});

Route::middleware('auth:sanctum')->group(function () {
  Route::get('/user', [UserController::class, 'getUser'])->name('user.get');
  Route::put('/user/update', [UserController::class, 'updateUser'])->name('user.update');
});