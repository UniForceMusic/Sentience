<?php

namespace src\app;

use Throwable;
use Service;
use src\router\Route;
use src\router\HttpRouter;

class HttpApp extends App implements AppInterface
{
    protected HttpRouter $router;

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

            $request = new Request($route);

            $args = $this->getArgs($route, $this->service, $request);
            if (!is_array($args)) {
                Response::internalServerError('error getting arguments for callable');
                return;
            }

            $modifiedArgs = $this->executeMiddleware($route, $args, $this->service, $request);
            if (!is_array($modifiedArgs)) {
                return;
            }

            $callable = $route->getCallable();
            if (is_array($callable)) {
                $callable = $this->arrayToCallable(
                    $callable,
                    function (string $class, string $method): void {
                        Response::internalServerError([
                            'error' => [
                                'text' => sprintf('class: %s does not have a public method named: %s', $class, $method)
                            ]
                        ]);
                    }
                );

                if (!$callable) {
                    return;
                }
            }

            $callable(...$modifiedArgs);
        } catch (Throwable $error) {
            $this->handleException($error);
        }
    }

    public function handleException(Throwable $error): void
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

    protected function getArgs(Route $route, Service $service, Request $request): ?array
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
                $args['request'] = $request;
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

    protected function executeMiddleware(Route $route, array $args, Service $service, Request $request): ?array
    {
        $middleware = $route->getMiddleware();

        $modifiedArgs = $args;

        foreach ($middleware as $callable) {
            $modifiedArgs = $this->executeMiddlewareCallable(
                $callable,
                $modifiedArgs,
                $service,
                $request
            );

            if (is_null($modifiedArgs)) {
                return null;
            }
        }

        return $modifiedArgs;
    }
}
