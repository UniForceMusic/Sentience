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
            echo sprintf("\n- %s", $route->getPath());
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

    public static function imUsed($content = null)
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

    public static function methodNotAllowed($content = null)
    {
        static::renderContent($content);
        http_response_code(405);
    }

    public static function notAcceptable($content = null)
    {
        static::renderContent($content);
        http_response_code(406);
    }

    public static function proxyAuthenticationRequired($content = null)
    {
        static::renderContent($content);
        http_response_code(407);
    }

    public static function requestTimeout($content = null)
    {
        static::renderContent($content);
        http_response_code(408);
    }

    public static function conflict($content = null)
    {
        static::renderContent($content);
        http_response_code(409);
    }

    public static function gone($content = null)
    {
        static::renderContent($content);
        http_response_code(410);
    }

    public static function lengthRequired($content = null)
    {
        static::renderContent($content);
        http_response_code(411);
    }

    public static function preconditionFailed($content = null)
    {
        static::renderContent($content);
        http_response_code(412);
    }

    public static function payloadTooLarge($content = null)
    {
        static::renderContent($content);
        http_response_code(413);
    }

    public static function uriTooLong($content = null)
    {
        static::renderContent($content);
        http_response_code(414);
    }

    public static function unsupportedMediaType($content = null)
    {
        static::renderContent($content);
        http_response_code(415);
    }

    public static function rangeNotSatisfiable($content = null)
    {
        static::renderContent($content);
        http_response_code(416);
    }

    public static function expectationFailed($content = null)
    {
        static::renderContent($content);
        http_response_code(417);
    }

    public static function teapot($content = null)
    {
        static::renderContent($content);
        http_response_code(418);
    }

    public static function misdirectedRequest($content = null)
    {
        static::renderContent($content);
        http_response_code(421);
    }

    public static function unprocessableContent($content = null)
    {
        static::renderContent($content);
        http_response_code(422);
    }

    public static function locked($content = null)
    {
        static::renderContent($content);
        http_response_code(423);
    }

    public static function failedDependency($content = null)
    {
        static::renderContent($content);
        http_response_code(424);
    }

    public static function tooEarly($content = null)
    {
        static::renderContent($content);
        http_response_code(425);
    }

    public static function upgradeRequired($content = null)
    {
        static::renderContent($content);
        http_response_code(426);
    }

    public static function preconditionRequired($content = null)
    {
        static::renderContent($content);
        http_response_code(428);
    }

    public static function tooManyRequests($content = null)
    {
        static::renderContent($content);
        http_response_code(429);
    }

    public static function requestHeaderFieldsTooLarge($content = null)
    {
        static::renderContent($content);
        http_response_code(431);
    }

    public static function unavailableForLegalReasons($content = null)
    {
        static::renderContent($content);
        http_response_code(451);
    }

    public static function internalServerError($content = null)
    {
        static::renderContent($content);
        http_response_code(500);
    }

    public static function notImplemented($content = null)
    {
        static::renderContent($content);
        http_response_code(501);
    }

    public static function badGateway($content = null)
    {
        static::renderContent($content);
        http_response_code(502);
    }

    public static function serviceUnavailable($content = null)
    {
        static::renderContent($content);
        http_response_code(503);
    }

    public static function gatewayTimeout($content = null)
    {
        static::renderContent($content);
        http_response_code(504);
    }

    public static function httpVersionNotSupported($content = null)
    {
        static::renderContent($content);
        http_response_code(505);
    }

    public static function variantAlsoNegotiates($content = null)
    {
        static::renderContent($content);
        http_response_code(506);
    }

    public static function insufficientStorage($content = null)
    {
        static::renderContent($content);
        http_response_code(507);
    }

    public static function loopDetected($content = null)
    {
        static::renderContent($content);
        http_response_code(508);
    }

    public static function notExtended($content = null)
    {
        static::renderContent($content);
        http_response_code(510);
    }

    public static function networkAuthenticationRequired($content = null)
    {
        static::renderContent($content);
        http_response_code(511);
    }
}

?>