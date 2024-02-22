<?php

namespace src\clients;

use src\httpclient\HttpClient;

abstract class BasicAuthClient extends Client
{
    protected string $username;
    protected string $password;

    public function __construct(string $username, string $password, ?string $baseUrl = null)
    {
        $this->username = $username;
        $this->password = $password;

        parent::__construct($baseUrl);
    }

    protected function setHttpClientDefaults(HttpClient $httpClient): HttpClient
    {
        return $httpClient->header(
            'authorization',
            sprintf(
                'Basic %s',
                base64_encode(
                    sprintf(
                        '%s:%s',
                        $this->username,
                        $this->password
                    )
                )
            )
        );
    }
}
