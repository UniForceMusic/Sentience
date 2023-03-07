<?php

namespace src\app;

use src\router\Router;
use src\util\Url;

class App
{
    protected Router $router;

    public function __construct(array $routes)
    {
        $this->router = new Router(Url::getRequestUri(), $routes);
    }

    public function execute()
    {
        $route = $this->router->getMatch();

        if (!$route) {
            Response::routeNotFound($this->router->getRoutesAsStrings());
            return;
        }

        $callable = $route->getCallable();
        $request = new Request($route->getTemplateValues());

        $callable($request);
    }
}

?>