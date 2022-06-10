<?php

use App\Engine\EngineData;
use App\Models\Game;
use Illuminate\Http\Request;
use Mockery\MockInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

it('throws an exception if the character is not valid', function () {
    $request = Mockery::mock(Request::class, function (MockInterface $mock) {
        $mock->shouldReceive('input')->with('character')->andReturn(null);
    });
    new EngineData($request, new Game());
})->throws(UnprocessableEntityHttpException::class);
