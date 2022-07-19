<?php

use App\Engine\Checks\AlreadyVotedCheck;
use App\Engine\Checks\CharacterAliveCheck;
use App\Engine\Checks\Exceptions\CharacterShouldBeAliveException;
use App\Engine\Checks\Exceptions\UserAlreadyVotedExeption;
use App\Engine\EngineData;
use App\Enums\CharacterEnum;
use Illuminate\Support\Collection;

uses()->group('game', 'engine', 'checks');

it('throws exception if character is dead', function () {
    $aliveCheck = new CharacterAliveCheck(null);
    $fakeData = mock(EngineData::class)->expect(
        isAlive: fn() => false
    );
    $aliveCheck->handle($fakeData);
})->throws(CharacterShouldBeAliveException::class);

it('throws exception if already voted once', function () {
    $voteExistsCheck = new AlreadyVotedCheck(null);
    $fakeData = mock(EngineData::class)->expect(
        getActions: fn() => new Collection([true]),
        getCharacter: fn() => CharacterEnum::FAIRY()
    );
    $voteExistsCheck->handle($fakeData);
})->throws(UserAlreadyVotedExeption::class);
