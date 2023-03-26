<?php

namespace src\controllers;

use src\app\Request;
use src\app\Response;

class ExampleController extends Controller
{
    public function example(Request $request)
    {
        Response::ok($this);
        Response::ok($request->getTemplateValues());
    }
}

?>