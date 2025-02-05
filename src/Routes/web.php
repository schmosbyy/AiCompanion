<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/handle-input', function (Request $request) {
    $prompt = $request->input('user_input');
    return "You entered: " . $prompt;
})->name('handle.input');
Route::get('/ai-home', function () {
    return view('ai-companion::home');
});
