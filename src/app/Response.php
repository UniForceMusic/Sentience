<?php

namespace src\app;

class Response
{
    public static function renderContent($content)
    {
        if (!$content) {
            return;
        }

        if (in_array(gettype($content), ["array", "object"])) {
            header('content-Type: application/json; charset=utf-8');
            echo json_encode($content);
        } else {
            header('Content-Type: text/plain; charset=utf-8');
            echo strval($content);
        }
    }

    public static function routeNotFound(array $routes)
    {
        header('Content-Type: text/plain; charset=utf-8');
        http_response_code(404);

        echo "Invalid route. Did you mean any of these routes?\n";

        foreach ($routes as $route) {
            echo sprintf("\n- %s", $route);
        }
    }

    public static function ok($content = null)
    {
        static::renderContent($content);
        http_response_code(200);
    }

    public static function created($content = null)
    {
        static::renderContent($content);
        http_response_code(201);
    }

    public static function accepted($content = null)
    {
        static::renderContent($content);
        http_response_code(202);
    }

    public static function nonAuthoritativeInformation($content = null)
    {
        static::renderContent($content);
        http_response_code(203);
    }

    public static function noContent($content = null)
    {
        static::renderContent($content);
        http_response_code(204);
    }

    public static function resetContent($content = null)
    {
        static::renderContent($content);
        http_response_code(205);
    }

    public static function partialContent($content = null)
    {
        static::renderContent($content);
        http_response_code(206);
    }

    public static function multiStatus($content = null)
    {
        static::renderContent($content);
        http_response_code(207);
    }

    public static function alreadyReported($content = null)
    {
        static::renderContent($content);
        http_response_code(208);
    }

    public static function IMUsed($content = null)
    {
        static::renderContent($content);
        http_response_code(226);
    }

    public static function badRequest($content = null)
    {
        static::renderContent($content);
        http_response_code(400);
    }

    public static function unauthorized($content = null)
    {
        static::renderContent($content);
        http_response_code(401);
    }

    public static function paymentRequired($content = null)
    {
        static::renderContent($content);
        http_response_code(402);
    }

    public static function forbidden($content = null)
    {
        static::renderContent($content);
        http_response_code(403);
    }

    public static function notFound($content = null)
    {
        static::renderContent($content);
        http_response_code(404);
    }
}

?>