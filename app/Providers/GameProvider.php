<?php

namespace App\Providers;

use App\Engine\EngineFactory;
use App\Engine\Services\GameService;
use App\Engine\Services\GameServiceImpl;
use Illuminate\Support\ServiceProvider;

class GameProvider extends ServiceProvider
{

    /**
     * All the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        GameService::class => GameServiceImpl::class
    ];

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
