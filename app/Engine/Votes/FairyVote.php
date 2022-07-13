<?php

namespace App\Engine\Votes;

use App\Engine\Checks\CharacterAliveCheck;
use App\Engine\EngineData;
use App\Enums\CharacterEnum;
use App\Exceptions\NotImplemented;
use Symfony\Component\HttpFoundation\Response;

final class FairyVote extends CharacterVoteHandler
{
    protected array $checks = [
        CharacterAliveCheck::class
    ];

    protected function processing(EngineData $engineData): ?Response
    {
        if (CharacterEnum::FAIRY()->equals($engineData->getCharacter())) {
            $this->runChecks($engineData);
            throw new NotImplemented();
        }
        return null;
    }
}
