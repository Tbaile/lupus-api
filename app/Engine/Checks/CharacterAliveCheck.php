<?php

namespace App\Engine\Checks;

use App\Engine\Checks\Exceptions\CharacterShouldBeAliveException;
use App\Engine\EngineData;

class CharacterAliveCheck extends CheckHandler
{
    /**
     * @inheritDoc
     */
    protected function processing(EngineData $engineData): void
    {
        if (!$engineData->isAlive()) {
            throw new CharacterShouldBeAliveException();
        }
    }
}
