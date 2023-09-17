<?php

namespace src\middleware;

interface Middleware
{
    /**
     * Return null if the middleware rejects the request
     */
    public function execute($args): ?array;
}
