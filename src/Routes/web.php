<?php

use Illuminate\Support\Facades\Route;
use Schmosbyy\AiCompanion\Http\Controllers\AiController;

Route::post('/handle-input', [AiController::class,'ask'])->name('handle.input');
Route::get('/ai-home', function () {
    return view('ai-companion::home');
});
