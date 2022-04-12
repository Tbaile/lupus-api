<?php

namespace App\Engine\Services;

use App\Models\Game;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface GameService
{
    /**
     * Handles the request given for the selected game.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Game  $game
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function handleRequest(Request $request, Game $game): Response;
}
