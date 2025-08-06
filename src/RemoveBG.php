<?php

namespace Scrapify\ImageTools;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Exception;

class RemoveBG
{
    protected $response = [];

    /**
     * Remove background from an uploaded image using Remove.bg API
     *
     * @param UploadedFile $imageFile
     * @param string|null $outputPath
     * @return string Path to the background-removed image
     * @throws Exception
     */
   public function remove(UploadedFile $imageFile, ?string $outputPath = null): string
    {
        // Direct API Key (no .env required)
        $apiKey = 'Bc9aS2gJTkgVFSsw2Yhk2W89'; 

        if (!$imageFile->isValid()) {
            throw new Exception("Invalid file upload.");
        }

        // Prepare output path if not given
        if ($outputPath === null) {
            $outputPath = storage_path('app/public/removed_' . time() . '.png');
        }

        // Prepare cURL request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.remove.bg/v1.0/removebg");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $postFields = [
            'image_file' => new \CURLFile(
                $imageFile->getRealPath(), 
                $imageFile->getMimeType(), 
                $imageFile->getClientOriginalName()
            ),
            'size' => 'auto',
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "X-Api-Key: $apiKey"
        ]);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception("cURL error: " . curl_error($ch));
        }

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode !== 200) {
            throw new Exception("Remove.bg API error (HTTP $statusCode): $result");
        }

        // Save the output image
        \Illuminate\Support\Facades\File::put($outputPath, $result);

        $this->response = [
            'status' => 'success',
            'output_path' => $outputPath
        ];

        return $outputPath;
    }

    /**
     * Get the last response
     *
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }
}
