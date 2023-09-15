<?php

declare(strict_types=1);

namespace App\Service;

use InvalidArgumentException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

readonly class HtmlTransformer
{
    public function transformToPdf(
        string $html,
        string $filename,
    ): void {
        if ($html === strip_tags($html)) {
            throw new InvalidArgumentException('data is not html code');
        }

        $filesystem = new Filesystem();
        $this->saveHtmlAsFile($html, $filename);
        $filesystem->touch(sys_get_temp_dir() . '/' . $filename . '.pdf');

        $isTransformed = proc_open(
            command: '/usr/bin/weasyprint -e UTF-8 ' . $filename . '.html ' . $filename . '.pdf',
            descriptor_spec: [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            pipes: $pipes,
            cwd: sys_get_temp_dir(),
        );

        $this->deleteHtmlFile($filename);

        if (false === $isTransformed) {
            throw new InvalidArgumentException('Error during transformation');
        }
    }

    private function saveHtmlAsFile(string $html, string $filename): void
    {
        $filesystem = new Filesystem();
        $filesystem->mkdir(sys_get_temp_dir());

        if ($filesystem->exists(sys_get_temp_dir() . '/' . $filename . '.html',)) {
            $filesystem->remove(
                sys_get_temp_dir() . '/' . $filename . '.html',
            );
        }

        file_put_contents(
            sys_get_temp_dir() . '/' . $filename . '.html',
            trim($html),
        );

        if (!$filesystem->exists(
            sys_get_temp_dir() . '/' . $filename . '.html',
        )) {
            throw new IOException('Error creating file');
        }
    }

    private function deleteHtmlFile(string $filename): void
    {
        $filesystem = new Filesystem();

        $filesystem->remove(
            sys_get_temp_dir() . '/' . $filename . '.html',
        );
    }
}
