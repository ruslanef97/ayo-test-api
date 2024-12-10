<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResource('/cities', App\Http\Controllers\Api\CityController::class); // City Module
Route::apiResource('/teams', App\Http\Controllers\Api\TeamController::class); // Team Module
Route::apiResource('/players', App\Http\Controllers\Api\PlayerController::class); // Player Module
Route::apiResource('/matches', App\Http\Controllers\Api\GameMatchController::class); // Match Module

// Match Result Module - Start
Route::get('/match-results', [App\Http\Controllers\Api\MatchResultController::class, 'index'])->name('match-results.index'); //list data
Route::post('/match-results', [App\Http\Controllers\Api\MatchResultController::class, 'store'])->name('match-results.store'); //create data
Route::get('/match-results/{result}', [App\Http\Controllers\Api\MatchResultController::class, 'show'])->name('match-results.show'); //view single data
Route::delete('/match-results/{result}', [App\Http\Controllers\Api\MatchResultController::class, 'destroy'])->name('match-results.destroy'); //delete data
// Match Result Module - End

// Report - Start
Route::get('/report/match-results', [App\Http\Controllers\Api\ReportController::class, 'matchResult'])->name('report.match-result'); //match result report
// Report - End