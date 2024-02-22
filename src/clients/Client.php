<?php

namespace src\clients;

use src\httpclient\HttpClient;

abstract class Client
{
    protected HttpClient $httpClient;

    public function __construct(?string $baseUrl = null)
    {
        $httpClient = new HttpClient();

        if ($baseUrl) {
            $httpClient->baseUrl($baseUrl);
        }

        $this->$httpClient = $this->setHttpClientDefaults($httpClient);
    }

    protected function setHttpClientDefaults(HttpClient $httpClient): HttpClient
    {
        return $httpClient;
    }
}
