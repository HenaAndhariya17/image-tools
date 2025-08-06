<?php

namespace Scrapify\ImageTools;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Exception;

class RotateImage
{
    /**
     * Rotate an uploaded image file.
     *
     * @param UploadedFile $imageFile
     * @param int $angle (90, 180, 270)
     * @return array
     * @throws Exception
     */
    public function rotate(UploadedFile $imageFile, int $angle): array
    {
        if (!$imageFile->isValid()) {
            throw new Exception("Invalid file upload.");
        }

        $imageDir = storage_path('app/public/rotated-images');
        if (!File::exists($imageDir)) {
            File::makeDirectory($imageDir, 0755, true);
        }

        $baseName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = $baseName . '_rotated_' . time() . '.jpg';
        $outputPath = $imageDir . '/' . $filename;

        // ✅ Rotate image with GD
        $this->rotateWithGD($imageFile->getRealPath(), $outputPath, $angle);

        return [
            'filename' => $filename,
            'file'     => base64_encode(File::get($outputPath)), // for Dropzone download
            'url'      => asset('storage/rotated-images/' . $filename),
        ];
    }

    private function rotateWithGD(string $sourcePath, string $outputPath, int $angle)
    {
        $info = getimagesize($sourcePath);
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $src = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $src = imagecreatefrompng($sourcePath);
                break;
            default:
                throw new Exception("Unsupported image type for rotation.");
        }

        // Rotate image (GD rotates counter-clockwise, so use 360-angle for clockwise)
        $rotated = imagerotate($src, 360 - $angle, 0);

        // Save rotated image
        imagejpeg($rotated, $outputPath, 90);

        imagedestroy($src);
        imagedestroy($rotated);
    }
}
