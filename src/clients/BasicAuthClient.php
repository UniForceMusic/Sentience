<?php

namespace src\clients;

use src\httpclient\HttpRequest;
use src\httpclient\HttpResponse;

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
            $this::getBasicAuthHeader($this->user, $this->pass)
        )->execute();
    }

    public static function getBasicAuthHeader(string $username, string $password): string
    {
        return sprintf(
            'Basic %s',
            base64_encode(
                sprintf(
                    '%s:%s',
                    $username,
                    $password
                )
            )
        );
    }
}
