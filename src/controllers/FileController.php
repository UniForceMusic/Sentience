<?php

namespace src\controllers;

use src\app\Request;
use src\app\Response;
use src\util\Headers;

class FileController extends Controller
{
    public function serveFile(Request $request): void
    {
        $filePath = $request->getVar('filePath');

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

        Headers::serveFile();
        Headers::contentLength($fileContents);
        Headers::cacheControl(Headers::CACHE_PUBLIC, $_ENV['FILE_CACHE']);
        Response::ok(
            $fileContents,
            $mimeType
        );
    }
}
