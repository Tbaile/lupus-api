<?php

use App\Engine\Checks\Exceptions\CharacterShouldBeAliveException;
use App\Engine\Checks\Exceptions\UserAlreadyVotedExeption;
use App\Engine\EngineData;
use App\Engine\Votes\FairyVote;
use App\Enums\CharacterEnum;
use Illuminate\Support\Collection;

uses()->group('game', 'engine', 'fairy');

test('null on every other character', function ($character) {
    $fairyHandler = new FairyVote(null);
    $fakeData = mock(EngineData::class)->expect(
        getCharacter: fn() => $character
    );
    expect($fairyHandler->handle($fakeData))
        ->toBeNull();
})->with(
    fn() => collect(CharacterEnum::cases())->filter(fn(CharacterEnum $value) => !$value->equals(CharacterEnum::FAIRY()))
);

test('cannot vote if dead', function () {
    $fairyHandler = new FairyVote(null);
    $fakeData = mock(EngineData::class)->expect(
        getCharacter: fn() => CharacterEnum::FAIRY(),
        isAlive: fn() => false
    );
    expect($fairyHandler->handle($fakeData));
})->throws(CharacterShouldBeAliveException::class);

test('cannot vote if already voted', function () {
    $fairyHandler = new FairyVote(null);
    $fakeData = mock(EngineData::class)->expect(
        getCharacter: fn() => CharacterEnum::FAIRY(),
        isAlive: fn() => true,
        getActions: fn() => new Collection([1])
    );
    expect($fairyHandler->handle($fakeData));
})->throws(UserAlreadyVotedExeption::class);
