<?php

use Illuminate\Support\Facades\Route;

Route::get('/ai-home', function () {
    return view('ai-companion::home');
});