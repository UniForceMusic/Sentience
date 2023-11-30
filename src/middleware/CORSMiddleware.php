<?php

namespace src\middleware;

use src\app\Request;
use src\util\Headers;

class CORSMiddleware implements Middleware
{
    public function execute($args): ?array
    {
        /** @var Request $request */
        $request = $args['request'];

        Headers::cors($request, true);

        return $args;
    }
}
