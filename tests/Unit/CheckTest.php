<?php

use App\Engine\Checks\CharacterAliveCheck;
use App\Engine\Checks\Exceptions\CharacterShouldBeAliveException;
use App\Engine\EngineData;

uses()->group('game', 'engine', 'checks');

it('throws exception if character is dead', function () {
    $aliveCheck = new CharacterAliveCheck(null);
    $fakeData = mock(EngineData::class)->expect(
        isAlive: fn() => false
    );
    $aliveCheck->handle($fakeData);
})->throws(CharacterShouldBeAliveException::class);
