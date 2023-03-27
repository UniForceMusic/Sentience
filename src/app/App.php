<?php

namespace src\app;

use Throwable;
use src\router\Router;
use src\util\Url;

class App
{
    protected Router $router;
    protected array $serviceArgs;

    public function __construct(array $routes, array $serviceArgs)
    {
        $this->router = new Router(Url::getRequestUri(), $routes);
        $this->serviceArgs = $serviceArgs;
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
        }

        if (!$callable) {
            Response::internalServerError([
                'error' => [
                    'text' => 'there was an error executing the method or function'
                ]
            ]);
            return;
        }

        $request = new Request($route->getTemplateValues());

        try {
            $callable($request);
        } catch (Throwable $error) {
            $this->handleException($error);
        }

    }

    protected function arrayToCallable(array $callable): ?callable
    {
        $className = $callable[0];
        $methodName = $callable[1];
        $controller = new $className(...$this->serviceArgs);

        if (!method_exists($controller, $methodName)) {
            return null;
        }

        return [$controller, $methodName];
    }

    protected function handleException(Throwable $error)
    {
        Response::internalServerError([
            'error' => [
                'text' => $error->getMessage(),
                'file' => $error->getFile(),
                'line' => $error->getLine(),
                // 'trace' => $error->getTrace()
            ]
        ]);
    }
}

?>