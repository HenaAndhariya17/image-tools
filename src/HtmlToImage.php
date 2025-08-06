<?php

namespace Scrapify\ImageTools;

use Illuminate\Support\Facades\File;
use Exception;

class HtmlToImage
{
    private $apiKey = 'bedc32e975d616481eb456a5c450a615';

    /**
     * Convert HTML URL to an image (PNG/JPG/JPEG) using ScreenshotLayer API
     */
    public function convert(string $htmlOrUrl, string $format = 'png'): array
    {
        $allowedFormats = ['png', 'jpg', 'jpeg'];
        $format = strtolower($format);

        if (!in_array($format, $allowedFormats)) {
            throw new Exception("Invalid format. Allowed: png, jpg, jpeg");
        }

        // Create output directory
        $outputDir = storage_path('app/public/html-images');
        if (!File::exists($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
        }

        // Generate filename & path
        $timestamp = time();
        $fileName  = "html_to_image_{$timestamp}.{$format}";
        $imagePath = $outputDir . DIRECTORY_SEPARATOR . $fileName;

        // ScreenshotLayer API URL
        $apiUrl = "https://api.screenshotlayer.com/api/capture?access_key={$this->apiKey}"
            . "&url=" . urlencode($htmlOrUrl)
            . "&format=PNG&viewport=1280x2000&fullpage=1";

        try {
            // Fetch the screenshot
            $imageData = @file_get_contents($apiUrl);

            if (!$imageData) {
                throw new Exception("Failed to fetch image from ScreenshotLayer API.");
            }

            // Save image
            File::put($imagePath, $imageData);

        } catch (\Throwable $e) {
            throw new Exception("HTML to Image conversion failed: " . $e->getMessage());
        }

        if (!File::exists($imagePath)) {
            throw new Exception("Failed to generate image file.");
        }

        return [
            'success'  => true,
            'filename' => $fileName,
            'url'      => asset('storage/html-images/' . $fileName),
            'path'     => $imagePath,
        ];
    }
}
