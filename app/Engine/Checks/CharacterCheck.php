<?php

namespace App\Engine\Checks;

use App\Engine\EngineData;
use App\Enums\CharacterEnum;

abstract class CharacterCheck extends CheckHandler
{
    abstract function getCharacter(): CharacterEnum;

    /**
     * @inerhitDoc
     */
    protected function processing(EngineData $engineData): ?bool
    {
        return $engineData->getCharacter()->equals($this->getCharacter()) ? null : false;
    }
}
