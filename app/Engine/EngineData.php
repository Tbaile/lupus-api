<?php

namespace App\Engine;

use App\Enums\CharacterEnum;
use App\Models\Game;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Spatie\Enum\Enum;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use TypeError;

class EngineData
{
    private CharacterEnum $character;

    public function __construct(
        private readonly Request $request,
        private readonly Game $game
    ) {
        try {
            $this->character = CharacterEnum::from(
                $this->game->users()->whereId($this->request->user()?->id)->firstOrFail()->pivot->character
            );
        } catch (TypeError|ModelNotFoundException) {
            throw new UnprocessableEntityHttpException();
        }
    }

    /**
     * Returns the request data.
     *
     * @return \Illuminate\Http\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Returns the Game that the request is made on.
     *
     * @return \App\Models\Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }

    /**
     * Returns the user's character.
     *
     * @return \App\Enums\CharacterEnum|\Spatie\Enum\Enum
     */
    public function getCharacter(): CharacterEnum|Enum
    {
        return $this->character;
    }
}
