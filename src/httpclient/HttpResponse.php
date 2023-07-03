<?php

namespace src\httpclient;

use CurlHandle;
use SimpleXMLElement;
use src\exceptions\InvalidCURLException;

class HttpResponse
{
    protected string $url;
    protected int $code;
    protected string $http;
    protected array $headers;
    protected string $body;
    protected string $error;
    protected bool $success;

    public function __construct(CurlHandle $curl)
    {
        $response = curl_exec($curl);
        $error = curl_error($curl);

        $this->success = empty($error);
        $this->error = trim($error);
        if ($error) {
            return;
        }

        $splitDoubleEol = explode("\r\n\r\n", $response, 2);
        $splitHeadersEol = explode("\r\n", $splitDoubleEol[0]);
        $splitHttpSpace = explode(' ', $splitHeadersEol[0]);

        $this->url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        $this->http = trim($splitHttpSpace[0]);
        $this->code = intval($splitHttpSpace[1]);
        $this->headers = $this->unserializeHeaders($splitHeadersEol);
        $this->body = trim($splitDoubleEol[1]);

        curl_close($curl);
    }

    public function hasError(): bool
    {
        return !$this->success;
    }

    public function getError(bool $asException = false): string|InvalidCURLException
    {
        if ($asException) {
            return new InvalidCURLException($this->error);
        }

        return $this->error;
    }

    public function getUrl(): bool|string
    {
        if (!$this->success) {
            return false;
        }

        return $this->url;
    }

    public function getHttp(): bool|string
    {
        if (!$this->success) {
            return false;
        }

        return $this->http;
    }

    public function getCode(): bool|int
    {
        if (!$this->success) {
            return false;
        }

        return $this->code;
    }

    public function getHeaders(): bool|array
    {
        if (!$this->success) {
            return false;
        }

        return $this->headers;
    }

    public function getBody(): bool|string
    {
        if (!$this->success) {
            return false;
        }

        return $this->body;
    }

    public function getJson(): bool|array
    {
        if (!$this->success) {
            return false;
        }

        return json_decode($this->body, true);
    }

    public function getXml(): bool|SimpleXMLElement
    {
        if (!$this->success) {
            return false;
        }

        return simplexml_load_string($this->body);
    }

    protected function unserializeHeaders(array $headers): array
    {
        $headers = array_slice($headers, 1);
        $headersArray = [];

        foreach ($headers as $header) {
            $headerSplitColon = explode(':', $header, 2);
            $key = strtolower(trim($headerSplitColon[0]));
            $value = trim($headerSplitColon[1]);

            $headersArray[$key] = $value;
        }

        return $headersArray;
    }
}

?>