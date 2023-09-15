<?php

declare(strict_types=1);

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelBrowser;

class PdfGeneratorControllerTest extends WebTestCase
{
    private static HttpKernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        static::$client = static::createClient();
        static::$client->setServerParameters(
            [
                'CONTENT_TYPE' => 'application/json',
            ],
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        gc_collect_cycles();
    }

    /**
     * @throws \JsonException
     */
    public function testGenerate(): void
    {
        static::$client->request(
            method: Request::METHOD_POST,
            uri: '/generate',
            parameters: [
                'html' => json_encode(trim($this->getHtml()), JSON_THROW_ON_ERROR),
                'filename' => 'test',
            ],
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-disposition', 'attachment; filename=test.pdf');
    }

    /**
     * @throws \JsonException
     */
    public function testGenerateInline(): void
    {
        static::$client->request(
            method: Request::METHOD_POST,
            uri: '/generate',
            parameters: [
                'html' => json_encode(trim($this->getHtml()), JSON_THROW_ON_ERROR),
                'filename' => 'test',
                'contentDisposition' => 'inline',
            ],
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/pdf');
        self::assertResponseHeaderSame('content-disposition', 'inline; filename=test.pdf');
    }

    /**
     * @throws \JsonException
     */
    public function testGenerateMissingParameter(): void
    {
        static::$client->request(
            method: Request::METHOD_POST,
            uri: '/generate',
            parameters: [
                'html' => json_encode(trim($this->getHtml()), JSON_THROW_ON_ERROR),
            ],
        );

        self::assertResponseStatusCodeSame(400);

        $errorBody = json_decode(static::$client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('Missing query parameter', $errorBody['detail']);
    }

    private function getHtml(): string
    {
        return
            <<< HTML
                <!DOCTYPE html>
                <html lang="fr">
                <head>
                <title>Test</title>
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <style>
                    body {background-color:#ffffff;background-repeat:no-repeat;background-position:top left;background-attachment:fixed;}
                    h1{font-family:Arial, sans-serif;color:#000000;background-color:#ffffff;}
                    p {font-family:Georgia, serif;font-size:14px;font-style:normal;font-weight:normal;color:#000000;background-color:#ffffff;}
                </style>
                </head>
                    <body>
                        <h1>Just a new title</h1>
                        <p>And this is a test of a paragraph.</p>
                    </body>
                </html>
            HTML;
    }
}
