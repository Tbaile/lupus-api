<?php

namespace App\Engine;

use App\Enums\CharacterEnum;
use App\Exceptions\NotImplemented;
use Symfony\Component\HttpFoundation\Response;

final class FairyVote extends CharacterVoteHandler
{
    protected function processing(EngineData $engineData): ?Response
    {
        if ($engineData->getCharacter()->equals(CharacterEnum::FAIRY()) && $engineData->isAlive()) {
            throw new NotImplemented();
        }
        return null;
    }
}
