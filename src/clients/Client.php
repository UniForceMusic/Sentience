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

    protected function execute(HttpRequest $request): HttpResponse
    {
        return $request->execute();
    }

    protected function appendToBaseUrl(?string $path, string $glue = '/'): ?string
    {
        if (!$path) {
            return null;
        }

        return sprintf(
            '%s%s%s',
            trim($this->baseUrl, $glue),
            $glue,
            trim($path, $glue),
        );
    }
}
