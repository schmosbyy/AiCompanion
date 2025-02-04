<?php

namespace Schmosbyy\AiCompanion;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
class AiServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load Routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');

        // Load Views
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'yourpackage');
    }

    public function register()
    {
        //
    }
}