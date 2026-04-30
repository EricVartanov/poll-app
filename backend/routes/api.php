<?php

use App\Http\Controllers\Api\PollController;
use Illuminate\Support\Facades\Route;

Route::post('/polls', [PollController::class, 'store']);
Route::get('/polls/{short_code}', [PollController::class, 'show']);
Route::post('/polls/{short_code}/vote', [PollController::class, 'vote']);
