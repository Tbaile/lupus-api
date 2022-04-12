<?php

namespace App\Engine\Services;

use App\Engine\EngineFactory;
use App\Engine\Handler;
use App\Enums\CharacterEnum;
use App\Models\Game;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GameServiceImpl implements GameService
{
    private Handler $handler;

    public function __construct(EngineFactory $engine)
    {
        $this->handler = $engine->make();
    }

    function handleRequest(Request $request, Game $game): Response
    {
        return $this->handler->handle($request, $game);
    }
}
