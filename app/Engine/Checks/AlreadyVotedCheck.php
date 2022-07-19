<?php

namespace App\Engine\Checks;

use App\Engine\Checks\Exceptions\UserAlreadyVotedExeption;
use App\Engine\EngineData;

class AlreadyVotedCheck extends CheckHandler
{

    /**
     * @inheritDoc
     */
    protected function processing(EngineData $engineData): void
    {
        if ($engineData->getActions($engineData->getCharacter())->isNotEmpty()) {
            throw new UserAlreadyVotedExeption();
        }
    }
}
