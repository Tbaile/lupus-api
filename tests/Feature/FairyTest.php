<?php

use App\Engine\EngineData;
use App\Engine\EngineFactory;
use App\Engine\FairyVote;
use App\Engine\Services\GameService;
use App\Enums\CharacterEnum;
use App\Exceptions\NotImplemented;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;

uses(RefreshDatabase::class)
    ->group('game', 'engine')
    ->beforeEach(function () {
        $this->app->singleton(EngineFactory::class, function () {
            return new EngineFactory(
                [
                    FairyVote::class
                ]
            );
        });
    });

it('can register a fairy vote', function () {
    /** @var GameService $engine */
    $engine = $this->app->make(GameService::class);
    $fakeData = $this->mock(EngineData::class, function (MockInterface $mock) {
        $mock->shouldReceive('getCharacter')
            ->andReturn(CharacterEnum::FAIRY());
    });
    $engine->handleRequest($fakeData);
})->throws(NotImplemented::class);
