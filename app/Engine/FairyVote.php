<?php

namespace App\Engine;

use App\Exceptions\NotImplemented;
use Symfony\Component\HttpFoundation\Response;

final class FairyVote extends Handler
{
    protected function processing(EngineData $engineData): ?Response
    {
        throw new NotImplemented();
    }
}
