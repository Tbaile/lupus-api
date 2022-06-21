<?php

namespace App\Engine\Votes;

use App\Engine\Checks\CharacterAliveCheck;
use App\Engine\Checks\FairyCharacterCheck;
use App\Engine\EngineData;
use App\Exceptions\NotImplemented;
use Symfony\Component\HttpFoundation\Response;

final class FairyVote extends CharacterVoteHandler
{
    protected array $checks = [
        FairyCharacterCheck::class,
        CharacterAliveCheck::class
    ];

    protected function processing(EngineData $engineData): ?Response
    {
        if ($this->runChecks($engineData)) {
            throw new NotImplemented();
        }
        return null;
    }
}
