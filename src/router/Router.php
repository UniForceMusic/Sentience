<?php

namespace src\router;

class Router
{
    protected string $requestUri;
    protected string $requestMethod;
    protected array $routes;

    public function __construct(string $requestUri, array $routes)
    {
        $this->requestUri = $requestUri;
        $this->requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
        $this->routes = $routes;
    }

    public function getMatch(): ?callable
    {
        foreach ($this->routes as $route) {
            if ($route->isMatch($this->requestUri, $this->requestMethod)) {
                return $route->getCallable();
            }
        }

        return null;
    }
}

?>