<?php

namespace App\Engine\Checks;

use App\Engine\EngineData;

abstract class CheckHandler
{
    public function __construct(private readonly ?CheckHandler $successor = null)
    {
    }

    /**
     * Handle the request, if nothing is returned, pass the request to the next in chain.
     *
     * @param  \App\Engine\EngineData  $engineData
     * @return void
     */
    final public function handle(EngineData $engineData): void
    {
        $this->processing($engineData);
        if (!is_null($this->successor)) {
            $this->successor->handle($engineData);
        }
    }

    /**
     * Process the request and return null to move forward in chain or false to return error.
     *
     * @param  \App\Engine\EngineData  $engineData
     * @return void
     */
    abstract protected function processing(EngineData $engineData): void;
}
