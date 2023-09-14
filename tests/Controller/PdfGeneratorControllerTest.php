<?php

declare(strict_types=1);

namespace Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class PdfGeneratorControllerTest extends WebTestCase
{
    public function testGenerate(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: '/generate',
            parameters: ['html' => json_encode(trim($this->getHtml())), 'filename' => 'test'],
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/pdf');
        self::assertResponseHeaderSame('content-disposition', 'inline; filename=test.pdf');
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
