<?php

namespace src\controllers;

use src\app\Request;
use src\app\Response;

class ExampleController extends Controller
{
    public function example(Request $request, string $appName, string $appVersion, ?string $sessionId)
    {
        Response::ok([$appName, $appVersion, $sessionId]);
    }
}

?>