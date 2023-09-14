<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\HtmlTransformer;
use http\Exception\InvalidArgumentException;
use JsonException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class PdfGeneratorController
{
    /**
     * @throws JsonException
     */
    #[Route(path: '/generate', methods: [Request::METHOD_GET])]
    public function generate(
        Request $request,
        HtmlTransformer $htmlTransformer,
    ): Response {
        $html = $request->query->get('html');
        $filename = $request->query->get('filename');

        if (
            null === $html
            || null === $filename
        ) {
            throw new InvalidArgumentException('Missing query parameter');
        }

        try {
            $isTransformed = $htmlTransformer->transformToPdf($html, $filename);
        } catch (\InvalidArgumentException) {
            throw new InvalidArgumentException('Error during transformation');
        }

        $fileStream = new Stream(sys_get_temp_dir() . '/' . $filename . '.pdf');

        $response = new BinaryFileResponse(
            file: $fileStream,
            status: Response::HTTP_OK,
            headers: [
                'content-type' => 'application/pdf',
            ],
            contentDisposition: ResponseHeaderBag::DISPOSITION_INLINE,
        );

        $response->deleteFileAfterSend();

        return $response;
    }
}
