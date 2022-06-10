<?php

namespace App\Engine\Services;

use App\Engine\EngineData;
use Symfony\Component\HttpFoundation\Response;

interface GameService
{
    /**
     * Handles the request given for the selected game.
     *
     * @param  \App\Engine\EngineData  $engineData
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    function handleRequest(EngineData $engineData): ?Response;
}
