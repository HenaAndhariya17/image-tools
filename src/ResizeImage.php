<?php

namespace Scrapify\ImageTools;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Exception;

class ResizeImage
{
    /**
     * Resize an image file to given dimensions.
     *
     * @param UploadedFile $imageFile
     * @param int $width
     * @param int $height
     * @return array
     * @throws Exception
     */
    public function resize(UploadedFile $imageFile, int $width, int $height): array
    {
        if (!$imageFile->isValid()) {
            throw new Exception("Invalid file upload.");
        }

        // Ensure output directory exists
        $imageDir = storage_path('app/public/resized-images');
        if (!File::exists($imageDir)) {
            File::makeDirectory($imageDir, 0755, true);
        }

        // Generate new filename
        $baseName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = $baseName . '_resized_' . time() . '.jpg';
        $outputPath = $imageDir . '/' . $filename;

        // Resize the image using GD
        $this->resizeWithGD($imageFile->getRealPath(), $outputPath, $width, $height);

        return [
            'filename' => $filename,
            'file'     => base64_encode(File::get($outputPath)), // For frontend preview/download
            'url'      => asset('storage/resized-images/' . $filename),
        ];
    }

    /**
     * Resize image with GD Library.
     */
    private function resizeWithGD(string $sourcePath, string $outputPath, int $width, int $height)
    {
        $info = getimagesize($sourcePath);
        $mime = $info['mime'];

        // Create image resource from source
        switch ($mime) {
            case 'image/jpeg':
                $src = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $src = imagecreatefrompng($sourcePath);
                break;
            default:
                throw new Exception("Unsupported image type for resizing.");
        }

        // Get original dimensions
        $origWidth = imagesx($src);
        $origHeight = imagesy($src);

        // Create blank true color image for resized output
        $dst = imagecreatetruecolor($width, $height);

        // Fill background white for PNGs to avoid black background
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);

        // Resample (resize)
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);

        // Save as JPEG (you can change to PNG if needed)
        imagejpeg($dst, $outputPath, 85);

        // Free resources
        imagedestroy($src);
        imagedestroy($dst);
    }
}
