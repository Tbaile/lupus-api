<?php

namespace App\Engine\Checks\Exceptions;

use App\Exceptions\ForbiddenHttpException;

class CharacterShouldBeAliveException extends ForbiddenHttpException
{
    public function __construct()
    {
        parent::__construct("The character should be alive to perform the action.");
    }
}
