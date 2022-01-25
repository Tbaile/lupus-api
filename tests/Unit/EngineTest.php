<?php

use App\Engine\EngineFactory;
use App\Engine\FairyVote;
use App\Engine\WolfVote;
use App\Exceptions\NotImplemented;
use App\Models\Day;

use function PHPUnit\Framework\assertTrue;

test('can make engine', function (array $chain) {
    $handler = (new EngineFactory($chain))->make();
    assertTrue($handler instanceof WolfVote);
    $handler->handle(new Day(), new \Illuminate\Http\Request());
})->with([
    [
        [
            WolfVote::class,
            FairyVote::class
        ]
    ]
])->throws(NotImplemented::class);
