<?php

namespace App\Engine\Votes;

use App\Engine\EngineData;
use App\Exceptions\NotImplemented;
use Symfony\Component\HttpFoundation\Response;

final class WolfVote extends CharacterVoteHandler
{
    protected function processing(EngineData $engineData): ?Response
    {
        throw new NotImplemented();
    }
}
