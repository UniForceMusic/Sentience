<?php

namespace src\router;

use src\util\Url;

class HttpRouter
{
    protected string $uri;
    protected string $method;
    protected array $routes;

    public function __construct(array $routes)
    {
        $this->uri = ($_ENV['SERVER_IS_NESTED'])
            ? Url::getRequestUri()
            : Url::getPath();
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        $this->routes = $routes;
    }

    public function getMatch(): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->isMatch($this->uri, $this->method)) {
                return $route;
            }
        }

        return null;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
