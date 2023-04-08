<?php

namespace src\app;

use Closure;
use ReflectionFunction;
use ReflectionMethod;
use Service;
use Throwable;
use src\router\Router;
use src\util\Url;

class App
{
    protected Router $router;
    protected Service $service;

    public function __construct(array $routes, Service $service)
    {
        $this->router = new Router(Url::getRequestUri(), $routes);
        $this->service = $service;
    }

    public function execute()
    {
        $route = $this->router->getMatch();

        if (!$route) {
            Response::routeNotFound($this->router->getRoutes());
            return;
        }

        $callable = $route->getCallable();

        if (is_array($callable)) {
            $callable = $this->arrayToCallable($callable);

            if (!$callable) {
                return;
            }
        }

        $request = new Request($route->getTemplateValues());
        $args = $this->getArgs($callable, $request, $this->service);

        if (!$args) {
            return;
        }

        $modifiedArgs = $this->executeMiddleware($args, $route->getMiddleware());

        if (!$modifiedArgs) {
            return;
        }

        try {
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

    protected function handleException(Throwable $error)
    {
        Response::internalServerError([
            'error' => [
                'text' => $error->getMessage(),
                'type' => $error::class,
                'file' => $error->getFile(),
                'line' => $error->getLine(),
                // 'trace' => $error->getTrace()
            ]
        ]);
    }

    protected function getArgs(array|string|Closure $callable, Request $request, Service $service): ?array
    {
        $serviceMethods = get_class_methods($service);

        if (is_array($callable)) {
            $arguments = $this->getMethodArgs($callable[0], $callable[1]);
        } else {
            $arguments = $this->getFunctionArgs($callable);
        }

        $args = [];
        foreach ($arguments as $argument) {
            $name = $argument->getName();

            if ($name == 'request') {
                $args['request'] = $request;
                continue;
            }

            if (!in_array($name, ['request', ...$serviceMethods])) {
                Response::internalServerError([
                    'error' => [
                        'text' => sprintf('parameter: "%s" is not defined in the service class', $name)
                    ]
                ]);
                return null;
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

    protected function getMethodArgs(object $class, string $method): array
    {
        $reflectionMethod = new ReflectionMethod($class, $method);
        return $reflectionMethod->getParameters();
    }

    protected function executeMiddleware(array $args, array $middleware): ?array
    {
        $modifiedArgs = $args;

        foreach ($middleware as $middlewareClass) {
            $callable = [$middlewareClass, 'execute'];
            $modifiedArgs = $callable($modifiedArgs);

            if (!$modifiedArgs) {
                return null;
            }
        }

        return $modifiedArgs;
    }
}

?>