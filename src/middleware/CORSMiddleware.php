<?php

namespace src\middleware;

use src\app\Request;
use src\util\Headers;

class CORSMiddleware extends Middleware
{
    public function execute(Request $request, ...$args): ?array
    {
        Headers::cors(
            $request,
            env('ACCESS_CONTROL_RETURN_ORIGIN', true)
        );

        return $args;
    }
}
