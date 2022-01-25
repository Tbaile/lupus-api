<?php

namespace App\Engine;

use App\Exceptions\NotImplemented;
use App\Models\Day;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class FairyVote extends Handler
{
    protected function processing(Day $day, Request $request): ?Response
    {
        throw new NotImplemented();
    }
}
