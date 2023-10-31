<?php

namespace src\app;

use Closure;
use ReflectionFunction;
use ReflectionMethod;
use Throwable;
use Service;

abstract class App
{
    protected Service $service;

    protected function arrayToCallable(array $callable, callable $methodNotFound): ?callable
    {
        $className = $callable[0];
        $methodName = $callable[1];
        $controller = new $className();

        if (!method_exists($controller, $methodName)) {
            $methodNotFound($className, $methodName);
            return null;
        }

        return [$controller, $methodName];
    }

    protected function handleException(Throwable $error): void
    {
        Response::internalServerError([
            'error' => [
                'text' => $error->getMessage(),
                'type' => $error::class,
                'file' => $error->getFile(),
                'line' => $error->getLine(),
                'trace' => (($_ENV['STACK_TRACE'] ?? false)) ? $error->getTrace() : 'disabled'
            ]
        ]);
    }

    protected function getFunctionArgs(string|Closure $callable): array
    {
        $reflectionFunction = new ReflectionFunction($callable);
        return $reflectionFunction->getParameters();
    }

    protected function getMethodArgs(string|object $class, string $method): array
    {
        $reflectionMethod = new ReflectionMethod($class, $method);
        return $reflectionMethod->getParameters();
    }
}
