<?php

namespace src\app;

use src\router\Route;
use src\util\MimeTypes;

class Response
{
    public static function renderContent(mixed $content, ?string $customContentType = null)
    {
        if (!$content) {
            return;
        }

        $currentErrorReporting = error_reporting();
        error_reporting(0);

        $contentType = static::getContentType($content, $customContentType);

        header(sprintf('content-type: %s', $contentType));

        if ($contentType == MimeTypes::JSON) {
            echo json_encode($content);
        } else {
            echo strval($content);
        }

        error_reporting($currentErrorReporting);
    }

    public static function getContentType(mixed $content, ?string $customContentType): string
    {
        if ($customContentType) {
            return $customContentType;
        }

        if (in_array(gettype($content), ['array', 'object'])) {
            return MimeTypes::JSON;
        }

        return MimeTypes::TXT;
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

    public static function html(string $content)
    {
        static::renderContent($content, MimeTypes::HTML);
        http_response_code(200);
    }

    public static function ok(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(200);
    }

    public static function created(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(201);
    }

    public static function accepted(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(202);
    }

    public static function nonAuthoritativeInformation(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(203);
    }

    public static function noContent(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(204);
    }

    public static function resetContent(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(205);
    }

    public static function partialContent(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(206);
    }

    public static function multiStatus(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(207);
    }

    public static function alreadyReported(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(208);
    }

    public static function imUsed(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(226);
    }

    public static function badRequest(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(400);
    }

    public static function unauthorized(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(401);
    }

    public static function paymentRequired(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(402);
    }

    public static function forbidden(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(403);
    }

    public static function notFound(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(404);
    }

    public static function methodNotAllowed(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(405);
    }

    public static function notAcceptable(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(406);
    }

    public static function proxyAuthenticationRequired(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(407);
    }

    public static function requestTimeout(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(408);
    }

    public static function conflict(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(409);
    }

    public static function gone(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(410);
    }

    public static function lengthRequired(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(411);
    }

    public static function preconditionFailed(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(412);
    }

    public static function payloadTooLarge(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(413);
    }

    public static function uriTooLong(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(414);
    }

    public static function unsupportedMediaType(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(415);
    }

    public static function rangeNotSatisfiable(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(416);
    }

    public static function expectationFailed(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(417);
    }

    public static function teapot(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(418);
    }

    public static function misdirectedRequest(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(421);
    }

    public static function unprocessableContent(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(422);
    }

    public static function locked(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(423);
    }

    public static function failedDependency(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(424);
    }

    public static function tooEarly(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(425);
    }

    public static function upgradeRequired(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(426);
    }

    public static function preconditionRequired(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(428);
    }

    public static function tooManyRequests(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(429);
    }

    public static function requestHeaderFieldsTooLarge(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(431);
    }

    public static function unavailableForLegalReasons(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(451);
    }

    public static function internalServerError(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(500);
    }

    public static function notImplemented(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(501);
    }

    public static function badGateway(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(502);
    }

    public static function serviceUnavailable(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(503);
    }

    public static function gatewayTimeout(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(504);
    }

    public static function httpVersionNotSupported(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(505);
    }

    public static function variantAlsoNegotiates(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(506);
    }

    public static function insufficientStorage(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(507);
    }

    public static function loopDetected(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(508);
    }

    public static function notExtended(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(510);
    }

    public static function networkAuthenticationRequired(mixed $content = null, ?string $customContentType = null)
    {
        static::renderContent($content, $customContentType);
        http_response_code(511);
    }
}
