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
                'trace' => (($_ENV['APP_STACK_TRACE'] ?? false)) ? $error->getTrace() : 'disabled'
            ]
        ]);
    }

    protected function executeMiddlewareCallable(string|array|callable|Closure $callable, array $args, Service $service, ?Request $request = null, ?array $words = null, ?array $flags = null): ?array
    {
        if (is_array($callable)) {
            $arguments = $this->getMethodArgs($callable[0], $callable[1]);
            $callable[0] = new $callable[0]();
        } else {
            $arguments = $this->getFunctionArgs($callable);
        }

        $serviceMethods = get_class_methods($service);

        /**
         * When a named argument is used in a middleware method or function,
         * The value disappears from ...$args
         * 
         * If a named argument is found, it needs to be added back after executing the middleware
         */
        $addBackAfterExecuting = [];

        foreach ($arguments as $argument) {
            $name = $argument->getName();

            if (key_exists($name, $args)) {
                $addBackAfterExecuting[] = $name;
                continue;
            }

            if (in_array($name, $serviceMethods)) {
                $addBackAfterExecuting[] = $name;

                $serviceCallable = [$service, $name];
                $args[$name] = $serviceCallable();

                continue;
            }

            if (in_array($name, ['request', 'words', 'flags'])) {
                $addBackAfterExecuting[] = $name;

                switch ($name) {
                    case 'request':
                        $args[$name] = $request;
                        break;
                    case 'words':
                        $args[$name] = $words;
                        break;
                    case 'flags':
                        $args[$name] = $flags;
                        break;
                }

                continue;
            }
        }

        $modifiedArgs = $callable(...$args);
        if (!is_array($modifiedArgs)) {
            return null;
        }

        foreach ($addBackAfterExecuting as $key) {
            $modifiedArgs[$key] = $args[$key];
        }

        return $modifiedArgs;
    }

    protected function getFunctionArgs(string|callable|Closure $callable): array
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
