<?php

namespace Scrapify\ImageTools;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Exception;

class CompressImage
{
    public function compress(UploadedFile $imageFile): array
    {
        if (!$imageFile->isValid()) {
            throw new Exception("Invalid file upload.");
        }

        $imageDir = storage_path('app/public/compressed-images');
        if (!File::exists($imageDir)) {
            File::makeDirectory($imageDir, 0755, true);
        }

        $baseName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = $baseName . '_' . time() . '.jpg';
        $outputPath = $imageDir . '/' . $filename;

        // ✅ Compress using GD
        $this->compressWithGD($imageFile->getRealPath(), $outputPath, 60);

        return [
            'filename' => $filename,
            'file'     => base64_encode(File::get($outputPath)), // Base64 for Dropzone
            'url'      => asset('storage/compressed-images/' . $filename),
            'size'     => filesize($outputPath),
        ];
    }

    private function compressWithGD($sourcePath, $outputPath, $quality = 60)
    {
        $info = getimagesize($sourcePath);
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                // Convert PNG to JPEG
                $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                $white = imagecolorallocate($bg, 255, 255, 255);
                imagefilledrectangle($bg, 0, 0, imagesx($image), imagesy($image), $white);
                imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                $image = $bg;
                break;
            default:
                throw new Exception("Unsupported image type.");
        }

        imagejpeg($image, $outputPath, $quality);
        imagedestroy($image);
    }
}
