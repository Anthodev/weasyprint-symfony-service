<?php

declare(strict_types=1);

namespace Tests\Service;

use App\Service\HtmlTransformer;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class HtmlTransformerTest extends TestCase
{
    private HtmlTransformer $htmlTransformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->htmlTransformer = new HtmlTransformer();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        if (file_exists(sys_get_temp_dir() . '/test.html')) {
            unlink(sys_get_temp_dir() . '/test.html');
        }

        if (file_exists(sys_get_temp_dir() . '/test.pdf')) {
            unlink(sys_get_temp_dir() . '/test.pdf');
        }
    }

    public function testTransformToPdf(): void
    {
        self::assertFileDoesNotExist(sys_get_temp_dir() . '/test.html');
        self::assertFileDoesNotExist(sys_get_temp_dir() . '/test.pdf');

        $this->htmlTransformer->transformToPdf($this->getHtml(), 'test');

        self::assertFileExists(sys_get_temp_dir() . '/test.pdf');
        self::assertFileDoesNotExist(sys_get_temp_dir() . '/test.html');
    }

    public function testTransformToPdfWithInvalidHtml(): void
    {
        self::assertFileDoesNotExist(sys_get_temp_dir() . '/test.html');
        self::assertFileDoesNotExist(sys_get_temp_dir() . '/test.pdf');

        $this->expectException(InvalidArgumentException::class);

        $this->expectExceptionMessage('data is not html code');

        $this->htmlTransformer->transformToPdf('test', 'test');
        self::assertFileDoesNotExist(sys_get_temp_dir() . '/test.html');
        self::assertFileDoesNotExist(sys_get_temp_dir() . '/test.pdf');
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
