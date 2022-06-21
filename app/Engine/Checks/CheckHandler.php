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
     * @return bool
     */
    final public function handle(EngineData $engineData): bool
    {
        $processed = $this->processing($engineData);
        if (is_null($processed) && !is_null($this->successor)) {
            $processed = $this->successor->handle($engineData);
        }
        return $processed ?? true;
    }

    /**
     * Process the request and return null to move forward in chain or false to return error.
     *
     * @param  \App\Engine\EngineData  $engineData
     * @return bool|null
     */
    abstract protected function processing(EngineData $engineData): ?bool;
}
