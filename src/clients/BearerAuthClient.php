<?php

namespace src\clients;

use src\httpclient\HttpRequest;
use src\httpclient\HttpResponse;

abstract class BearerAuthClient extends Client
{
    protected string $token;

    public function __construct(string $baseUrl)
    {
        parent::__construct($baseUrl);

        $this->token = $this->getAuthToken();
    }

    protected function getAuthToken(): string
    {
        return '';
    }

    protected function execute(HttpRequest $request): HttpResponse
    {
        return $request->header(
            $this->authHeader,
            sprintf('Bearer %s', $this->token)
        )->execute();
    }
}
