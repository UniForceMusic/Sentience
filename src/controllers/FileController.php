<?php

namespace src\controllers;

use src\app\Request;
use src\app\Response;
use src\util\Header;

class FileController extends Controller
{
    public function serveFile(Request $request)
    {
        $filePath = sprintf(
            '%s/%s',
            getFileDir(),
            $request->getTemplateValue('filePath')
        );

        if (str_contains($filePath, '/../')) {
            Response::forbidden();
            return;
        }

        if (!file_exists($filePath)) {
            Response::notFound();
            return;
        }

        $fileContents = file_get_contents($filePath);
        $mimeType = mime_content_type($filePath);

        if (!$mimeType) {
            Response::unsupportedMediaType();
            return;
        }

        Header::serveFile();
        Header::contentLength($fileContents);
        Response::ok(
            $fileContents,
            $mimeType
        );
    }
}
