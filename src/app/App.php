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
        $callable = $this->router->getMatch();

        if (!$callable) {
            http_response_code(400);
            exit();
        }

        $callable();
    }
}

?>