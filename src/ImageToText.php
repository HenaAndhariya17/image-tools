<?php

namespace Scrapify\ImageTools;

use Illuminate\Http\UploadedFile;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Exception;

class ImageToText
{
    private $image;
    private $text;

    public function __construct(UploadedFile $image)
    {
        $this->image = $image;
    }

    public function process()
    {
        try {
            $realPath = $this->image->getRealPath();

            $tesseract = new TesseractOCR($realPath);

            // ✅ Detect OS and set path dynamically
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows path
                $tesseract->executable('C:\Program Files\Tesseract-OCR\tesseract.exe');
            } else {
                // Linux/Hostinger path
                $tesseract->executable('/usr/bin/tesseract');
            }

            $this->text = $tesseract->lang('eng')->run();

            return $this->text;
        } catch (Exception $e) {
            throw new Exception("Failed to extract text: " . $e->getMessage());
        }
    }
}

