<?php

namespace src\router;

class Router
{
    protected string $requestUri;
    protected array $routes;

    public function __construct(string $requestUri, array $routes)
    {
        $this->requestUri = $requestUri;
        $this->routes = $routes;
    }
}

?>