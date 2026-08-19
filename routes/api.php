<?php
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json([
        'message' => 'Docker Laravel API is working!',
        'status' => 'success'
    ]);
});