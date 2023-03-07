<?php

namespace src\controllers;

use src\app\Request;

class TestController
{
    public function test(Request $request)
    {
        echo json_encode($request->getTemplateValue("int"));
    }
}

?>