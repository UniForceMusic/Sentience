<?php

namespace src\app;

use src\router\Route;

class Response
{
    public static function renderContent($content, string|null $customContentType = null)
    {
        if (!$content) {
            return;
        }

        if (in_array(gettype($content), ['array', 'object'])) {
            header('content-Type: application/json; charset=utf-8');
            echo json_encode($content);
        } else {
            header('Content-Type: text/plain; charset=utf-8');
            echo strval($content);
        }

        if ($customContentType) {
            header(sprintf('Content-type: %s', $customContentType));
        }
    }

    public static function routeNotFound(array $routes)
    {
        $routeStrings = array_map(
            function (Route $route) {
                return $route->getPath();
            },
            $routes
        );

        static::renderContent([
            'error' => [
                'text' => 'the route was invalid',
                'available_routes' => $routeStrings
            ]
        ]);

        http_response_code(404);
    }

    public static function ok($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(200);
    }

    public static function created($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(201);
    }

    public static function accepted($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(202);
    }

    public static function nonAuthoritativeInformation($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(203);
    }

    public static function noContent($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(204);
    }

    public static function resetContent($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(205);
    }

    public static function partialContent($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(206);
    }

    public static function multiStatus($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(207);
    }

    public static function alreadyReported($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(208);
    }

    public static function imUsed($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(226);
    }

    public static function badRequest($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(400);
    }

    public static function unauthorized($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(401);
    }

    public static function paymentRequired($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(402);
    }

    public static function forbidden($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(403);
    }

    public static function notFound($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(404);
    }

    public static function methodNotAllowed($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(405);
    }

    public static function notAcceptable($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(406);
    }

    public static function proxyAuthenticationRequired($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(407);
    }

    public static function requestTimeout($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(408);
    }

    public static function conflict($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(409);
    }

    public static function gone($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(410);
    }

    public static function lengthRequired($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(411);
    }

    public static function preconditionFailed($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(412);
    }

    public static function payloadTooLarge($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(413);
    }

    public static function uriTooLong($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(414);
    }

    public static function unsupportedMediaType($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(415);
    }

    public static function rangeNotSatisfiable($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(416);
    }

    public static function expectationFailed($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(417);
    }

    public static function teapot($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(418);
    }

    public static function misdirectedRequest($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(421);
    }

    public static function unprocessableContent($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(422);
    }

    public static function locked($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(423);
    }

    public static function failedDependency($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(424);
    }

    public static function tooEarly($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(425);
    }

    public static function upgradeRequired($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(426);
    }

    public static function preconditionRequired($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(428);
    }

    public static function tooManyRequests($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(429);
    }

    public static function requestHeaderFieldsTooLarge($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(431);
    }

    public static function unavailableForLegalReasons($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(451);
    }

    public static function internalServerError($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(500);
    }

    public static function notImplemented($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(501);
    }

    public static function badGateway($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(502);
    }

    public static function serviceUnavailable($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(503);
    }

    public static function gatewayTimeout($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(504);
    }

    public static function httpVersionNotSupported($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(505);
    }

    public static function variantAlsoNegotiates($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(506);
    }

    public static function insufficientStorage($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(507);
    }

    public static function loopDetected($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(508);
    }

    public static function notExtended($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(510);
    }

    public static function networkAuthenticationRequired($content = null, string|null $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(511);
    }
}

?>