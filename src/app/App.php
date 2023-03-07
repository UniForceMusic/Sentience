<?php

namespace src\app;

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
            Response::routeNotFound($this->router->getRoutesAsStrings());
            return;
        }

        $callable = $this->arrayToCallable($route->getCallable());

        $request = new Request($route->getTemplateValues());

        $callable($request);
    }

    protected function arrayToCallable(array $callable): callable
    {
        $className = $callable[0];
        $methodName = $callable[1];
        $controller = new $className(...$this->serviceArgs);
        return [$controller, $methodName];
    }
}

?>