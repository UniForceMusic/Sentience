<?php

namespace src\controllers;

use src\app\Request;
use src\app\Response;

class TestController
{
    public function test(Request $request)
    {
        Response::ok($request->getTemplateValues());
    }
}

?>