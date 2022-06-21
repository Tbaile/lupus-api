<?php

use App\Engine\Votes\FairyVote;
use App\Engine\Votes\WolfVote;

return [
    /*
     * Characters priority for the game.
     */
    'chain' => [
        FairyVote::class,
        WolfVote::class
    ]
];
