<?php

namespace App\Engine\Services;

use App\Engine\EngineData;
use App\Engine\EngineFactory;
use App\Engine\Votes\CharacterVoteHandler;
use Symfony\Component\HttpFoundation\Response;

class GameServiceImpl implements GameService
{
    private CharacterVoteHandler $handler;

    public function __construct(EngineFactory $engine)
    {
        $this->handler = $engine->make();
    }

    function handleRequest(EngineData $engineData): ?Response
    {
        return $this->handler->handle($engineData);
    }
}
