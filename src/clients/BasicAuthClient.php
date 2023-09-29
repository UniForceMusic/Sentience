<?php

namespace src\clients;

use src\httpclient\HttpRequest;
use src\httpclient\HttpResponse;
use src\util\Auth;

abstract class BasicAuthClient extends Client
{
    protected string $user;
    protected string $pass;

    public function __construct(string $baseUrl, string $user, string $pass)
    {
        $this->user = $user;
        $this->pass = $pass;

        parent::__construct($baseUrl);
    }

    protected function execute(HttpRequest $request): HttpResponse
    {
        return $request->header(
            $this->authHeader,
            Auth::getBasicAuthHeader($this->user, $this->pass)
        )->execute();
    }
}
