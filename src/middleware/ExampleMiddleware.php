<?php

namespace src\middleware;

use src\app\Response;

class ExampleMiddleware implements Middleware
{
    public static function execute($args): ?array
    {
        $request = $args['request'];
        if ($request->getParameter('killswitch') == 'true') {
            Response::internalServerError('early termination');
            return null;
        }

        if (isset($args['appName'])) {
            $args['appName'] = 'Modified by middleware';
        }

        $args['sessionId'] = 'abcdefghijklmnopqrstuvwxyz1234567890';

        return $args;
    }
}

?>