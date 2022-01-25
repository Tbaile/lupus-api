<?php

namespace App\Engine;

use App\Models\Game;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class FairyVote extends Handler
{
    protected function processing(Request $request, Game $game): ?Response
    {
        throw new NotImplemented();
    }
}
