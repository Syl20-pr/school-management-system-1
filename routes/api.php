<?php

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TimeTableApiController; // adjust if needed

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Routes for timetable
Route::prefix('timetable')->group(function () {
    Route::get('/', [TimeTableApiController::class, 'index']);
    Route::get('/{id}', [TimeTableApiController::class, 'show']);
    Route::get('/teacher/{teacherId}', [TimeTableApiController::class, 'teacherTimetable']);
    Route::get('/class/{classId}', [TimeTableApiController::class, 'classTimetable']);
});
