<?php

namespace App\Engine;

use ReflectionClass;
use RuntimeException;

final class EngineFactory
{
    public function __construct(private array $chain)
    {
    }

    public function make(): Handler
    {
        return $this->builder($this->chain);
    }

    private function builder(?array $chain): Handler
    {
        if (!isset($chain[1])) {
            return $this->resolve($chain[0]);
        }
        return $this->resolve($chain[0], $this->builder(array_slice($chain, 1)));
    }

    private function resolve(string $class, ?Handler $next = null): Handler
    {
        $instance = (new ReflectionClass($class))->newInstance($next);
        if ($instance instanceof Handler) {
            return $instance;
        } else {
            throw new RuntimeException("Class [$class] is not instance of Handler.");
        }
    }
}
