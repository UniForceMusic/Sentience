<?php

namespace src\clients;

use src\httpclient\HttpClient;
use src\httpclient\HttpRequest;
use src\httpclient\HttpResponse;

abstract class BearerAuthClient extends Client
{
    protected ?string $authToken;

    public function __construct(?string $authToken = null, ?string $baseUrl = null)
    {
        $this->authHeader = $authToken;

        parent::__construct($baseUrl);
    }

    protected function setHttpClientDefaults(HttpClient $httpClient): HttpClient
    {
        if ($this->authToken) {
            $httpClient->header('authorization', $this->authToken);
        }

        return $httpClient;
    }
}
