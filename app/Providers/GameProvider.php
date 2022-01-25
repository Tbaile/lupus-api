<?php

namespace App\Providers;

use App\Engine\EngineFactory;
use Illuminate\Support\ServiceProvider;

class GameProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EngineFactory::class, function () {
            return new EngineFactory(config('engine.chain'));
        });
    }
}
