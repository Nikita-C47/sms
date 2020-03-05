<?php

namespace App\Components\Inherited;

use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Класс с собственной фабркой ответа для возможности отдавать файлы на кириллице.
 * @package App\Components\Inherited Классы, переопределяющие стандартный функционал.
 */
class CyrillicResponseFactory extends ResponseFactory
{
    /**
     * @param \SplFileInfo|string $file
     * @param null $name
     * @param array $headers
     * @param string $disposition
     * @return BinaryFileResponse
     */
    public function download($file, $name = null, array $headers = [], $disposition = 'attachment')
    {
        $response = new BinaryFileResponse($file, 200, $headers, true);

        if(is_null($name))
        {
            $name = basename($file);
        }

        return $response->setContentDisposition($disposition, $name, Str::ascii($name));
    }
}
