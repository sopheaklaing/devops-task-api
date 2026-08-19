<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
Route::get('/test', function () {
    return response()->json([
        'message' => 'Docker Laravel API is working!',
        'status' => 'success'
    ]);
});
Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::get('/tasks/{task}', [TaskController::class, 'show']);
Route::put('/tasks/{task}', [TaskController::class, 'update']);
Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);