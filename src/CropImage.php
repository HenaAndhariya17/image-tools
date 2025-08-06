<?php

namespace Scrapify\ImageTools;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Exception;

class CropImage
{
    /**
     * Crop an image file based on given coordinates and size.
     *
     * @param UploadedFile $imageFile
     * @param array $cropData
     * @return array
     * @throws Exception
     */
    public function crop(UploadedFile $imageFile, array $cropData): array
    {
        if (!$imageFile->isValid()) {
            throw new Exception("Invalid file upload.");
        }

        if (!isset($cropData['x'], $cropData['y'], $cropData['width'], $cropData['height'])) {
            throw new Exception("Crop data is incomplete.");
        }

        // Create directory if it doesn't exist
        $imageDir = storage_path('app/public/cropped-images');
        if (!File::exists($imageDir)) {
            File::makeDirectory($imageDir, 0755, true);
        }

        // Generate output filename
        $baseName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = $baseName . '_cropped_' . time() . '.jpg';
        $outputPath = $imageDir . '/' . $filename;

        // Crop the image using GD
        $this->cropWithGD(
            $imageFile->getRealPath(),
            $outputPath,
            (int)$cropData['x'],
            (int)$cropData['y'],
            (int)$cropData['width'],
            (int)$cropData['height']
        );

        return [
            'filename' => $filename,
            'file'     => base64_encode(File::get($outputPath)), // Base64 for download
            'url'      => asset('storage/cropped-images/' . $filename),
        ];
    }

    private function cropWithGD($sourcePath, $outputPath, $x, $y, $width, $height)
    {
        $info = getimagesize($sourcePath);
        $mime = $info['mime'];

        // Create image resource
        switch ($mime) {
            case 'image/jpeg':
                $src = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $src = imagecreatefrompng($sourcePath);
                break;
            default:
                throw new Exception("Unsupported image type for cropping.");
        }

        // Create blank true color image for cropped area
        $dst = imagecreatetruecolor($width, $height);

        // Crop and copy
        imagecopyresampled($dst, $src, 0, 0, $x, $y, $width, $height, $width, $height);

        // Save cropped image
        imagejpeg($dst, $outputPath, 90);

        // Clean up
        imagedestroy($src);
        imagedestroy($dst);
    }
}
