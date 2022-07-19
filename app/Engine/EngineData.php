<?php

namespace App\Engine;

use App\Enums\CharacterEnum;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use TypeError;

class EngineData
{
    private CharacterEnum $character;
    private User $user;
    private bool $alive;

    public function __construct(
        private readonly Request $request,
        private readonly Game $game
    ) {
        try {
            $this->user = $this->getGame()->users()->where('id', $this->getRequest()->user()?->id)->firstOrFail();
            $this->character = CharacterEnum::from($this->getUser()->pivot->character); // @phpstan-ignore-line
            $this->alive = !$this->getUser()->pivot->death; // @phpstan-ignore-line
        } catch (TypeError|ModelNotFoundException) {
            throw new UnprocessableEntityHttpException();
        }
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
     * Returns the request data.
     *
     * @return \Illuminate\Http\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Returns the user that the request is made on, throws exception if not in the game.
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Returns the user's character.
     *
     * @return \App\Enums\CharacterEnum
     */
    public function getCharacter(): CharacterEnum
    {
        return $this->character;
    }

    /**
     * @return bool
     */
    public function isAlive(): bool
    {
        return $this->alive;
    }

    /**
     * Returns the actions for the latest day of the day based on character given.
     *
     * @param  \App\Enums\CharacterEnum  $characterEnum
     * @return \Illuminate\Support\Collection<\App\Models\Action>
     */
    public function getActions(CharacterEnum $characterEnum): Collection
    {
        return $this->getGame()->days()->latest()->first()
            ->actions()->whereJsonContains('data->character', $characterEnum)->get();
    }
}
