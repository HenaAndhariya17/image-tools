<?php

namespace Scrapify\ImageTools;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;

class ConvertImage
{
    /**
     * Convert an uploaded image to a specified format.
     *
     * @param UploadedFile $imageFile
     * @param string $format
     * @return array
     * @throws Exception
     */
    public function convert(UploadedFile $imageFile, string $format): array
    {
        // 1️⃣ Validate upload
        if (!$imageFile->isValid()) {
            throw new Exception("Invalid file upload.");
        }

        // 2️⃣ Supported formats
        $validFormats = ['jpg', 'png', 'gif', 'webp', 'avif', 'pdf'];
        $format = strtolower($format);

        if (!in_array($format, $validFormats)) {
            throw new Exception("Unsupported conversion format.");
        }

        // 3️⃣ Prepare storage path
        $imageDir = storage_path('app/public/converted-images');
        if (!File::exists($imageDir)) {
            File::makeDirectory($imageDir, 0755, true);
        }

        // 4️⃣ Generate output filename
        $baseName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = $baseName . '_' . time() . '.' . $format;
        $outputPath = $imageDir . '/' . $filename;

        // 5️⃣ Create GD image resource
        $sourcePath = $imageFile->getRealPath();
        $info = getimagesize($sourcePath);
        if (!$info) {
            throw new Exception("Failed to read image info.");
        }

        $mime = $info['mime'];
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourcePath);
                break;
            default:
                throw new Exception("Unsupported source image type: {$mime}");
        }

        // 6️⃣ Conversion logic
        switch ($format) {
            case 'jpg':
                imagejpeg($image, $outputPath, 85);
                break;

            case 'png':
                imagepng($image, $outputPath, 6);
                break;

            case 'gif':
                imagegif($image, $outputPath);
                break;

            case 'webp':
                imagewebp($image, $outputPath, 80);
                break;

            case 'avif':
                if (function_exists('imageavif')) {
                    imageavif($image, $outputPath, 80);
                } else {
                    imagedestroy($image);
                    throw new Exception("AVIF conversion is not supported on this server.");
                }
                break;

            case 'pdf':
                // Convert to PDF using Dompdf
                $tempJpg = $imageDir . '/' . $baseName . '_temp.jpg';
                imagejpeg($image, $tempJpg, 85);

                $html = '<html><body style="margin:0;padding:0;">
                            <img src="' . $tempJpg . '" style="width:100%;height:auto;">
                         </body></html>';

                $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');
                $pdf->save($outputPath);

                unlink($tempJpg);
                break;
        }

        imagedestroy($image);

        // 7️⃣ Return response
        return [
            'filename' => $filename,
            'file'     => base64_encode(File::get($outputPath)),
            'url'      => asset('storage/converted-images/' . $filename),
        ];
    }
}
