<?php

namespace Tests\Support;

use Illuminate\Http\UploadedFile;
use RuntimeException;

trait CreatesUploadedImages
{
    protected function fakePng(string $name, int $width = 300, int $height = 200): UploadedFile
    {
        $chunk = static fn (string $type, string $data): string => pack('N', strlen($data))
            .$type.$data.pack('N', crc32($type.$data));
        $row = "\0".str_repeat("\xff", $width * 3);
        $compressed = gzcompress(str_repeat($row, $height));

        if (! is_string($compressed)) {
            throw new RuntimeException('Could not create the PNG test image.');
        }

        $image = "\x89PNG\r\n\x1a\n"
            .$chunk('IHDR', pack('NNCCCCC', $width, $height, 8, 2, 0, 0, 0))
            .$chunk('IDAT', $compressed)
            .$chunk('IEND', '');

        return UploadedFile::fake()->createWithContent($name, $image);
    }
}
