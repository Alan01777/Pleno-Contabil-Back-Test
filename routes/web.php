<?php

use App\Http\Controllers\Api\v1\FileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function(){
    return response()->json(['status' => 'OK', 'message' => 'Health check passed']);
});
