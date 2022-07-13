<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ForbiddenHttpException extends HttpException
{
    public function __construct(string $message)
    {
        parent::__construct(403, $message);
    }
}
