<?php

namespace App\Engine\Checks\Exceptions;

use App\Exceptions\ForbiddenHttpException;

class UserAlreadyVotedExeption extends ForbiddenHttpException
{
public function __construct()
    {
        parent::__construct('User already voted for today.');
    }
}
