<?php

namespace src\middleware;

use src\app\Request;
use src\app\Response;

class ExampleMiddleware extends Middleware
{
    public function execute(Request $request, ...$args): ?array
    {
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
