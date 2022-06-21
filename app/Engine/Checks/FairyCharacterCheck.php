<?php

namespace App\Engine\Checks;

use App\Engine\EngineData;
use App\Enums\CharacterEnum;

class FairyCharacterCheck extends CheckHandler
{
    /**
     * @inerhitDoc
     */
    protected function processing(EngineData $engineData): ?bool
    {
        return $engineData->getCharacter()->equals(CharacterEnum::FAIRY()) ? null : false;
    }
}
