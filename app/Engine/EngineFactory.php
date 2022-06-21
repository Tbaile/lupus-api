<?php

namespace App\Engine;

use ReflectionClass;
use RuntimeException;

final class EngineFactory
{
    public function __construct(private array $chain)
    {
    }

    public function make(): CharacterVoteHandler
    {
        return $this->builder($this->chain);
    }

    private function builder(?array $chain): CharacterVoteHandler
    {
        if (!isset($chain[1])) {
            return $this->resolve($chain[0]);
        }
        return $this->resolve($chain[0], $this->builder(array_slice($chain, 1)));
    }

    private function resolve(string $class, ?CharacterVoteHandler $next = null): CharacterVoteHandler
    {
        $instance = (new ReflectionClass($class))->newInstance($next);
        if ($instance instanceof CharacterVoteHandler) {
            return $instance;
        } else {
            throw new RuntimeException("Class [$class] is not instance of CharacterVoteHandler.");
        }
    }
}
