<?php

use App\Engine\EngineData;
use App\Engine\Votes\FairyVote;
use App\Enums\CharacterEnum;

uses()->group('game', 'engine', 'fairy');

test('null on every other character', function ($character) {
    $fairyHandler = new FairyVote(null);
    $fakeData = mock(EngineData::class)->expect(
        getCharacter: fn() => $character
    );
    expect($fairyHandler->handle($fakeData))
        ->toBeNull();
})->with([
    CharacterEnum::WOLF()
]);
