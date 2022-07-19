<?php

use App\Engine\EngineData;
use App\Engine\EngineFactory;
use App\Engine\Services\GameService;
use App\Engine\Votes\FairyVote;
use App\Enums\CharacterEnum;
use App\Exceptions\NotImplemented;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery\MockInterface;

uses(RefreshDatabase::class)
    ->group('game', 'engine', 'fairy')
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
        $mock->shouldReceive('isAlive')
            ->andReturn(true);
        $mock->shouldReceive('getActions')
            ->andReturn(new Collection());
    });
    $engine->handleRequest($fakeData);
})->throws(NotImplemented::class);
