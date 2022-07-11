<?php

namespace App\Http\Controllers\Game;

use App\Engine\EngineData;
use App\Engine\Services\GameService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActionRequest;
use App\Models\Game;
use Symfony\Component\HttpFoundation\Response;

class ActionController extends Controller
{
    public function __construct(private readonly GameService $gameService)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreActionRequest  $request
     * @param  \App\Models\Game  $game
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(StoreActionRequest $request, Game $game): Response
    {
        return $this->gameService->handleRequest(new EngineData($request, $game));
    }
}
