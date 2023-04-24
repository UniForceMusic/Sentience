<?php

namespace src\httpclient;

use CurlHandle;
use Exception;
use SimpleXMLElement;

class HttpResponse
{
    protected string $url;
    protected int $code;
    protected string $http;
    protected array $headers;
    protected string $body;
    protected string $error;

    public function __construct(CurlHandle $curl)
    {
        $response = curl_exec($curl);
        $error = curl_error($curl);

        if (!empty($error)) {
            throw new Exception($error);
        }

        $splitDoubleEol = explode("\r\n\r\n", $response, 2);
        $splitHeadersEol = explode("\r\n", $splitDoubleEol[0]);
        $splitHttpSpace = explode(' ', $splitHeadersEol[0]);

        $this->url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        $this->http = trim($splitHttpSpace[0]);
        $this->code = intval($splitHttpSpace[1]);
        $this->headers = $this->unserializeHeaders($splitHeadersEol);
        $this->body = trim($splitDoubleEol[1]);
        $this->error = trim($error);

        curl_close($curl);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getHttp(): string
    {
        return $this->http;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getJson(): array
    {
        return json_decode($this->body, true);
    }

    public function getXml(): SimpleXMLElement
    {
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