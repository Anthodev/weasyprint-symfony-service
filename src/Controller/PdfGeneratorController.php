<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\HtmlTransformer;
use InvalidArgumentException;
use JsonException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class PdfGeneratorController
{
    /**
     * @throws JsonException
     */
    #[Route(path: '/generate', methods: [Request::METHOD_POST])]
    public function generate(
        Request $request,
        HtmlTransformer $htmlTransformer,
    ): Response {
        $html = $request->request->get('html');
        $filename = $request->request->get('filename');
        $contentDisposition = $request->request->get('contentDisposition');

        if (
            null === $html
            || null === $filename
        ) {
            throw new BadRequestHttpException('Missing query parameter');
        }

        try {
            $htmlTransformer->transformToPdf($html, $filename);
        } catch (InvalidArgumentException) {
            throw new BadRequestHttpException('Error during transformation');
        }

        $fileStream = new Stream(sys_get_temp_dir() . '/' . $filename . '.pdf');

        $contentDisposition = match ($contentDisposition) {
            'inline' => ResponseHeaderBag::DISPOSITION_INLINE,
            default => ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        };

        $response = new BinaryFileResponse(
            file: $fileStream,
            status: Response::HTTP_OK,
            headers: [
                'Content-Type' => 'application/pdf',
            ],
            contentDisposition: $contentDisposition,
        );

        $response->deleteFileAfterSend();

        return $response;
    }
}
