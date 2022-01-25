<?php

namespace App\Engine;

use App\Models\Day;
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
     * @param  \App\Models\Day  $day
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    final public function handle(Day $day, Request $request): ?Response
    {
        $processed = $this->processing($day, $request);
        if (is_null($processed) && !is_null($this->successor)) {
            $processed = $this->successor->handle($day, $request);
        }
        return $processed;
    }

    /**
     * Process the request and return null to move forward in chain or a Response to return the response.
     *
     * @param  \App\Models\Day  $day
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    abstract protected function processing(Day $day, Request $request): ?Response;
}
