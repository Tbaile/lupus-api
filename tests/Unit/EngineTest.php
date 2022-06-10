<?php

use App\Engine\EngineData;
use App\Engine\EngineFactory;
use App\Engine\FairyVote;
use App\Engine\WolfVote;
use App\Exceptions\NotImplemented;
use App\Models\Game;
use Illuminate\Http\Request;

use function PHPUnit\Framework\assertTrue;

test('can make engine', function (array $chain) {
    $handler = (new EngineFactory($chain))->make();
    assertTrue($handler instanceof $chain[0]);
})->with([
    [
        [
            WolfVote::class,
            FairyVote::class
        ],
        [
            FairyVote::class
        ],
        [
            WolfVote::class
        ]
    ]
]);
