<?php

namespace src\app;

use Throwable;
use Closure;
use ReflectionFunction;
use ReflectionMethod;
use Service;
use src\router\Route;
use src\router\HttpRouter;

class HttpApp
{
    protected HttpRouter $router;
    protected Service $service;

    public function __construct(array $routes, Service $service)
    {
        $this->router = new HttpRouter($routes);
        $this->service = $service;
    }

    public function execute(): void
    {
        try {
            $route = $this->router->getMatch();
            if (!$route) {
                Response::routeNotFound($this->router->getRoutes());
                return;
            }

            $args = $this->getArgs($route, $this->service);
            if (!is_array($args)) {
                Response::internalServerError('error getting arguments for callable');
                return;
            }

            $modifiedArgs = $this->executeMiddleware($route, $args);
            if (!is_array($modifiedArgs)) {
                return;
            }

            $callable = $route->getCallable();
            if (is_array($callable)) {
                $callable = $this->arrayToCallable($callable);

                if (!$callable) {
                    return;
                }
            }

            $callable(...$modifiedArgs);
        } catch (Throwable $error) {
            $this->handleException($error);
        }

    }

    protected function arrayToCallable(array $callable): ?callable
    {
        $className = $callable[0];
        $methodName = $callable[1];
        $controller = new $className();

        if (!method_exists($controller, $methodName)) {
            Response::internalServerError([
                'error' => [
                    'text' => sprintf('class does not have a public method named: "%s"', $methodName)
                ]
            ]);
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

    protected function getArgs(Route $route, Service $service): ?array
    {
        $callable = $route->getCallable();
        if (!$callable) {
            return null;
        }

        if (is_array($callable)) {
            $arguments = $this->getMethodArgs($callable[0], $callable[1]);
        } else {
            $arguments = $this->getFunctionArgs($callable);
        }

        $serviceMethods = get_class_methods($service);

        $args = [];
        foreach ($arguments as $argument) {
            $name = $argument->getName();

            if ($name == 'request') {
                $args['request'] = new Request($route->getTemplateValues(), $route->getPayload());
                continue;
            }

            if (!in_array($name, ['request', ...$serviceMethods])) {
                $args[$name] = null;
                continue;
            }

            $callable = [$service, $name];
            $args[$name] = $callable();
        }

        return $args;
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

    protected function executeMiddleware(Route $route, array $args): ?array
    {
        $middleware = $route->getMiddleware();

        $modifiedArgs = $args;

        foreach ($middleware as $middlewareClass) {
            $callable = [(new $middlewareClass()), 'execute'];
            $modifiedArgs = $callable($modifiedArgs);

            if (!$modifiedArgs) {
                return null;
            }
        }

        return $modifiedArgs;
    }
}
