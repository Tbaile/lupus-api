<?php

namespace App\Engine\Votes;

use App\Engine\EngineData;
use App\Enums\CharacterEnum;
use Symfony\Component\HttpFoundation\Response;

/**
 * This class is the base of the chain of responsability pattern that is going to be used in the game.
 */
abstract class CharacterVoteHandler
{
    protected ?CharacterEnum $character = null;

    public function __construct(private readonly ?CharacterVoteHandler $successor = null)
    {
    }

    /**
     * Handle the request, if nothing is returned, pass the request to the next in chain.
     *
     * @param  \App\Engine\EngineData  $engineData
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    final public function handle(EngineData $engineData): ?Response
    {
        $processed = $this->processing($engineData);
        if (is_null($processed) && !is_null($this->successor)) {
            $processed = $this->successor->handle($engineData);
        }
        return $processed;
    }

    /**
     * Process the request and return null to move forward in chain or a Response to return the response.
     *
     * @param  \App\Engine\EngineData  $engineData
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    abstract protected function processing(EngineData $engineData): ?Response;
}
