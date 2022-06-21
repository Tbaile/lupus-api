<?php

use App\Engine\Checks\CheckHandler;
use App\Engine\EngineData;

it('can create a dummy check', function () {
    $checkFalse = new class extends CheckHandler {
        protected function processing(EngineData $engineData): ?bool
        {
            return false;
        }
    };
    $checkNull = new class ($checkFalse) extends CheckHandler {
        protected function processing(EngineData $engineData): ?bool
        {
            return null;
        }
    };
    $this->assertFalse($checkNull->handle(
        Mockery::mock(EngineData::class)
    ));
});
