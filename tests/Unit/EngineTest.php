<?php

use App\Engine\EngineFactory;
use App\Engine\FairyVote;
use App\Engine\WolfVote;
use App\Exceptions\NotImplemented;
use App\Models\Game;

use function PHPUnit\Framework\assertTrue;

test('can make engine', function (array $chain) {
    $handler = (new EngineFactory($chain))->make();
    assertTrue($handler instanceof WolfVote);
    $handler->handle(new \Illuminate\Http\Request(), new Game());
})->with([
    [
        [
            WolfVote::class,
            FairyVote::class
        ]
    ]
])->throws(NotImplemented::class);
