<?php

namespace src\clients;

use src\httpclient\HttpClient;
use src\httpclient\HttpRequest;
use src\httpclient\HttpResponse;

abstract class Client
{
    protected HttpClient $httpClient;
    protected string $authHeader = 'Authorization';
    protected string $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->httpClient = new HttpClient();
        $this->baseUrl = $baseUrl;
    }

    protected function addAuth(HttpRequest $request): HttpResponse
    {
        return $request->execute();
    }
}
