<?php

namespace Schmosbyy\AiCompanion;

use Illuminate\Support\ServiceProvider;
class AiServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load Routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');

        // Load Views
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'ai-companion');

        // Publish the config file so users can modify it if needed
        $this->publishes([
            __DIR__ . '/../config/ai.php' => config_path('ai.php'),
        ], 'config');

        // Merge config to provide default values
        $this->mergeConfigFrom(__DIR__ . '/../config/ai.php', 'ai');
    }

    public function register()
    {
        //
    }
}