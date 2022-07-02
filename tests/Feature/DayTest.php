<?php

use App\Models\Day;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function PHPUnit\Framework\assertTrue;

uses(RefreshDatabase::class);

// TODO: when API is ready, throw this test out of the window, not a feature test.
test('days in game are tested', function () {
    /** @var Game $game */
    $game = Game::factory()->create();
    $day = new Day();
    $game->days()->save($day);
    assertTrue($day->is($game->days->first()));
});

test('can fetch game from day', function () {
    /** @var Game $game */
    $game = Game::factory()->create();
    $day = new Day();
    $game->days()->save($day);
    assertTrue($day->game->is($game));
});
