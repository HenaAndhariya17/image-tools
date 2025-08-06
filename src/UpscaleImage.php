<?php

namespace Scrapify\ImageTools;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Exception;

class UpscaleImage
{
    /**
     * Upscale an uploaded image file.
     *
     * @param UploadedFile $imageFile
     * @param float $scaleFactor
     * @return array
     * @throws Exception
     */
    public function upscale(UploadedFile $imageFile, float $scaleFactor): array
    {
        if (!$imageFile->isValid()) {
            throw new Exception("Invalid file upload.");
        }

        $imageDir = storage_path('app/public/upscaled-images');
        if (!File::exists($imageDir)) {
            File::makeDirectory($imageDir, 0755, true);
        }

        $baseName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = $baseName . '_upscaled_' . time() . '.jpg';
        $outputPath = $imageDir . '/' . $filename;

        // ✅ Upscale image with GD
        $this->upscaleWithGD($imageFile->getRealPath(), $outputPath, $scaleFactor);

        return [
            'filename' => $filename,
            'file'     => base64_encode(File::get($outputPath)), // for Dropzone download
            'url'      => asset('storage/upscaled-images/' . $filename),
        ];
    }

    private function upscaleWithGD(string $sourcePath, string $outputPath, float $scaleFactor)
    {
        $info = getimagesize($sourcePath);
        $mime = $info['mime'];
        $width = $info[0];
        $height = $info[1];

        switch ($mime) {
            case 'image/jpeg':
                $src = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $src = imagecreatefrompng($sourcePath);
                break;
            default:
                throw new Exception("Unsupported image type for upscaling.");
        }

        // Calculate new dimensions
        $newWidth = (int)($width * $scaleFactor);
        $newHeight = (int)($height * $scaleFactor);

        // Create a new true color image
        $dst = imagecreatetruecolor($newWidth, $newHeight);

        // Copy and resize part of an image with resampling
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save upscaled image
        imagejpeg($dst, $outputPath, 90);

        imagedestroy($src);
        imagedestroy($dst);
    }
}