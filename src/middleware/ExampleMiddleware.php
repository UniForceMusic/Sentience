<?php

namespace src\middleware;

use src\app\Request;
use src\app\Response;

class ExampleMiddleware implements Middleware
{
    public function execute($args): ?array
    {
        /** @var Request $request */
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
