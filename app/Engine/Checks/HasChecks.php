<?php

namespace App\Engine\Checks;

use App\Engine\EngineData;
use ReflectionClass;
use RuntimeException;

trait HasChecks
{
    /**
     * An array of classes that are checks to run against before running the actual request.
     * @var array<\App\Engine\Checks\CheckHandler> $checks
     */
    protected array $checks = [];

    /**
     * Execute all checks.
     *
     * @param  \App\Engine\EngineData  $engineData
     * @return void
     * @throws \ReflectionException
     */
    protected function runChecks(EngineData $engineData): void
    {
        if (!empty($this->checks)) {
            $this->builder($this->checks)->handle($engineData);
        }
    }

    /**
     * Build recusively all the chain of checks.
     *
     * @param  array  $checks
     * @return \App\Engine\Checks\CheckHandler
     * @throws \ReflectionException
     */
    private function builder(array $checks): CheckHandler
    {
        if (!isset($checks[1])) {
            return $this->resolve($checks[0]);
        }
        return $this->resolve($checks[0], $this->builder(array_slice($checks, 1)));
    }

    /**
     * Resolve a class string to a check handler.
     *
     * @param  string  $class
     * @param  \App\Engine\Checks\CheckHandler|null  $next
     * @return \App\Engine\Checks\CheckHandler
     * @throws \ReflectionException
     */
    private function resolve(string $class, ?CheckHandler $next = null): CheckHandler
    {
        $instance = (new ReflectionClass($class))->newInstance($next);
        if ($instance instanceof CheckHandler) {
            return $instance;
        } else {
            throw new RuntimeException("Class [$class] is not instance of CheckHandler.");
        }
    }
}
