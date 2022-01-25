<?php

use App\Engine\FairyVote;
use App\Engine\WolfVote;

return [
    /*
     * Characters priority for the game.
     */
    'chain' => [
        FairyVote::class,
        WolfVote::class
    ]
];
