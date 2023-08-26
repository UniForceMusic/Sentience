<?php

namespace src\httpclient;

use CurlHandle;
use JsonException;
use SimpleXMLElement;
use src\exceptions\CurlException;

class HttpResponse
{
    public const HTTP_EOL = "\r\n\r\n";

    protected CurlHandle $curlHandle;
    protected ?string $url;
    protected ?int $code;
    protected ?string $http;
    protected ?array $headers;
    protected ?string $body;

    public function __construct(CurlHandle $curlHandle, int $retryCount)
    {
        for ($i = 0; $i < $retryCount + 1; $i++) {
            $response = curl_exec($curlHandle);
            if ($response) {
                break;
            }
        }

        $error = curl_error($curlHandle);

        if (empty($error)) {
            $this->curlHandle = $curlHandle;
            $this->parseCurlHandle($curlHandle);
            $this->parseResponse($response);

            curl_close($curlHandle);
            return;
        }

        throw new CurlException($error);
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getHttp(): ?string
    {
        return $this->http;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function getHeaders(): ?array
    {
        return $this->headers;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function getHtml(): ?string
    {
        return replaceNonAsciiChars($this->body);
    }

    public function getJson(): ?array
    {
        $array = json_decode($this->body, true);

        if (!$array) {
            throw new JsonException(json_last_error_msg());
        }

        return $array;
    }

    public function getXml(): ?SimpleXMLElement
    {
        return simplexml_load_string($this->body);
    }

    public function getCurlInfo(int $curlOption): mixed
    {
        return curl_getinfo($this->curlHandle, $curlOption);
    }

    protected function parseCurlHandle(CurlHandle $curlHandle)
    {
        $this->url = curl_getinfo($curlHandle, CURLINFO_EFFECTIVE_URL);
        $this->code = curl_getinfo($curlHandle, CURLINFO_RESPONSE_CODE);
        $this->http = curl_getinfo($curlHandle, CURLINFO_HTTP_VERSION);
    }

    protected function parseResponse(string $response)
    {
        $splitDoubleEol = explode($this::HTTP_EOL, $response, 2);

        $this->body = trim($splitDoubleEol[1]);
        $this->parseHeaders($splitDoubleEol[0]);
    }

    protected function parseHeaders(string $headerLines)
    {
        $headerLines = explode($this::HTTP_EOL, $headerLines);
        $headerLines = array_slice($headerLines, 1);

        $headers = [];

        foreach ($headerLines as $headerLine) {
            $headerSplitColon = explode(':', $headerLine, 2);

            $key = strtolower(trim($headerSplitColon[0]));
            $value = trim($headerSplitColon[1]);

            $headers[$key] = $value;
        }

        $this->headers = $headers;
    }
}

?>