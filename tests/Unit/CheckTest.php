<?php

use App\Engine\Checks\CheckHandler;
use App\Engine\Checks\FairyCharacterCheck;
use App\Engine\EngineData;
use App\Enums\CharacterEnum;
use Mockery\MockInterface;

it('can create a dummy check', function () {
    $checkFalse = new class extends CheckHandler {
        protected function processing(EngineData $engineData): ?bool
        {
            return false;
        }
    };
    $checkNull = new class ($checkFalse) extends CheckHandler {
        protected function processing(EngineData $engineData): ?bool
        {
            return null;
        }
    };
    $this->assertFalse($checkNull->handle(
        Mockery::mock(EngineData::class)
    ));
});

test('FairyCharacterCheck works properly ', function (CharacterEnum $characterEnum, bool $expects) {
    $engineData = Mockery::mock(EngineData::class, function (MockInterface $mock) use ($characterEnum) {
        $mock->shouldReceive('getCharacter')
            ->andReturn($characterEnum);
    });
    $fairyCharacterCheck = new FairyCharacterCheck();
    expect($fairyCharacterCheck->handle($engineData))
        ->toBe($expects);
})->with([
    [
        CharacterEnum::FAIRY(),
        true
    ],
    [
        CharacterEnum::WOLF(),
        false
    ]
]);
