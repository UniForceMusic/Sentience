<?php

namespace src\controllers;

use src\app\Request;

class PagesController
{
    public function loadPage(Request $request): void
    {
        $pagePath = $request->getVar('pagePath');

        $absolutePagesPath = appendToBaseDir(
            BASEDIR,
            'src',
            'pages',
            $pagePath
        );

        foreach ($request->getVars() as $var => $value) {
            $$var = $value;
        }

        include_once($absolutePagesPath);
    }
}
