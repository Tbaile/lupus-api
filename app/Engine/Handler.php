<?php

namespace App\Engine;

use App\Models\Game;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * This class is the base of the chain of responsability pattern that is going to be used in the game.
 */
abstract class Handler
{
    public function __construct(private ?Handler $successor = null)
    {
    }

    /**
     * Handle the request, if nothing is returned, pass the request to the next in chain.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Game  $game
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    final public function handle(Request $request, Game $game): ?Response
    {
        $processed = $this->processing($request, $game);
        if (is_null($processed) && !is_null($this->successor)) {
            $processed = $this->successor->handle($request, $game);
        }
        return $processed;
    }

    /**
     * Process the request and return null to move forward in chain or a Response to return the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Game  $game
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    abstract protected function processing(Request $request, Game $game): ?Response;
}
