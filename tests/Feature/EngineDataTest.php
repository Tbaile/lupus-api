<?php

use App\Engine\EngineData;
use App\Enums\CharacterEnum;
use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery\MockInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

uses(RefreshDatabase::class)->group('engine');

it('correctly fetches charater inside the game', function () {
    $user = User::factory()->create();
    $game = Game::factory()->create();
    $game->users()->attach($user, ['character' => CharacterEnum::FAIRY()]);
    $request = $this->mock(Request::class, function (MockInterface $mock) use ($user) {
        $mock->shouldReceive('user')->andReturn($user);
    });
    $enginedata = new EngineData($request, $game);
    $this->assertTrue($enginedata->getCharacter()->equals(CharacterEnum::FAIRY()));
});

it('throws exception if the user doesn\'t exists', function () {
    (new EngineData(new Request(), Game::factory()->create()))->getUser();
})->throws(UnprocessableEntityHttpException::class);

it('throws exeption if the user doesn\'t belongs to the game', function () {
    $user = User::factory()->create();
    $request = $this->mock(Request::class, function (MockInterface $mock) use ($user) {
        $mock->shouldReceive('user')->andReturn($user);
    });
    (new EngineData($request, Game::factory()->create()))->getUser();
})->throws(UnprocessableEntityHttpException::class);
