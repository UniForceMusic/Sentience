<?php

namespace src\controllers;

use src\app\Request;

class PagesController
{
    public function loadPage(Request $request): void
    {
        $pagePath = $request->getVar('filePath');

        foreach ($request->getVars() as $var => $value) {
            $$var = $value;
        }

        include_once($pagePath);
    }
}
