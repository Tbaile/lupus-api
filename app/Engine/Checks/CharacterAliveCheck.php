<?php

namespace App\Engine\Checks;

use App\Engine\EngineData;

class CharacterAliveCheck extends CheckHandler
{
    /**
     * @inheritDoc
     */
    protected function processing(EngineData $engineData): ?bool
    {
        return $engineData->isAlive();
    }
}
