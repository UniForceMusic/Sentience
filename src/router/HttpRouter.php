<?php

namespace src\router;

use src\util\Url;

class HttpRouter
{
    protected string $requestUri;
    protected string $requestMethod;
    protected array $routes;

    public function __construct(array $routes)
    {
        $this->requestUri = Url::getRequestUri();
        $this->requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);
        $this->routes = $routes;
    }

    public function getMatch(): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->isMatch($this->requestUri, $this->requestMethod)) {
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

?>