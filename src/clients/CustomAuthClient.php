<?php

namespace src\clients;

use src\httpclient\HttpRequest;
use src\httpclient\HttpResponse;

class CustomAuthClient
{
    protected function execute(HttpRequest $request): HttpResponse
    {
        return $request->execute();
    }
}
