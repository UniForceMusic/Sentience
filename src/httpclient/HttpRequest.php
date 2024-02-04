<?php

namespace src\httpclient;

use src\util\Methods;

class HttpRequest extends HttpBase
{
    protected HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->baseUrl = null;
        $this->path = null;
        $this->url = null;
        $this->method = Methods::GET;
        $this->parameters = [];
        $this->headers = [];
        $this->cookies = [];
        $this->body = null;
        $this->secure = true;
        $this->retryCount = 0;
        $this->customOptions = [];
    }

    public function execute(): HttpResponse
    {
        $url = ($this->url)
            ? $this->url
            : appendToBaseUrl($this->baseUrl, $this->path);

        return $this->httpClient->execute(
            $url,
            $this->method,
            $this->parameters,
            $this->headers,
            $this->cookies,
            $this->body,
            $this->secure,
            $this->retryCount,
            $this->customOptions
        );
    }
}
