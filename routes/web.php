<?php

use App\Http\Controllers\Api\v1\FileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


Route::get('/',function(){
    return Storage::files();
});

Route::get('/download/{path}', [FileController::class, 'getFile'])->where('path', '.*');

Route::get('/files/{path}', [FileController::class, 'listFilesInDirectory'])->where('path', '.*');