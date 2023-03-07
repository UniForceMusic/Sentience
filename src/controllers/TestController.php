<?php

namespace src\controllers;

use src\app\Request;
use src\app\Response;

class TestController extends Controller
{
    public function test(Request $request)
    {
        Response::ok($this);
        Response::ok($request->getTemplateValues());
    }
}

?>