📸 Scrapify Image Tools Library

A Laravel package for powerful **image processing** including compression, conversion, cropping, resizing, rotation, HTML-to-image conversion, upscaling, background removal (remove.bg API), and OCR (image-to-text).

This library wraps several industry-standard packages into an easy-to-use Laravel API.

---

## 📦 Installation

```bash
composer require scrapify-dev/image-tools
```

---

## ⚙️ Requirements

* **PHP:** `^8.2`
* **Laravel:** `^9.0` | `^10.0` | `^11.0` | `^12.0`
* **Dependencies:**

  * `intervention/image:^3.0`
  * `barryvdh/laravel-dompdf:^3.1`
  * `spatie/browsershot:^5.0`
  * `spatie/pdf-to-image:^1.2`
  * `thiagoalessio/tesseract_ocr:^2.13`
  * `illuminate/support` *(as per Laravel version)*

---

## 🚀 Quick Start

```php
use Scrapify\ImageTools\CompressImage;

$compressor = new CompressImage();
$result = $compressor->compress($request->file('image_file'));

return response()->json([
    'filename' => $result['filename'],
    'url'      => $result['url'],
    'size_kb'  => round($result['size'] / 1024, 2),
]);
```

---

## 📑 Table of Contents

1. [Compress Image](#1-compress-image)
2. [Convert Image](#2-convert-image)
3. [Crop Image](#3-crop-image)
4. [Resize Image](#4-resize-image)
5. [Rotate Image](#5-rotate-image)
6. [HTML to Image](#6-html-to-image)
7. [Upscale Image](#7-upscale-image)
8. [Remove Background](#8-remove-background)
9. [Image to Text (OCR)](#9-image-to-text)

---

## 1️⃣ Compress Image

Reduces file size with minimal quality loss. PNGs are auto-converted to JPG for better compression.

```php
use Scrapify\ImageTools\CompressImage;

$compressor = new CompressImage();
$result = $compressor->compress($request->file('image_file'));
```

---

## 2️⃣ Convert Image

Supported formats: `jpg`, `png`, `gif`, `webp`, `avif`, `pdf`

```php
use Scrapify\ImageTools\ConvertImage;

$converter = new ConvertImage();
$result = $converter->convert($request->file('image_file'), 'webp');
```

---

## 3️⃣ Crop Image

```php
use Scrapify\ImageTools\CropImage;

$cropper = new CropImage();
$result = $cropper->crop($file, [
    'x' => 100,
    'y' => 50,
    'width' => 200,
    'height' => 150
]);
```

---

## 4️⃣ Resize Image

```php
use Scrapify\ImageTools\ResizeImage;

$resizer = new ResizeImage();
$result = $resizer->resize($file, 500, 300);
```

---

## 5️⃣ Rotate Image

```php
use Scrapify\ImageTools\RotateImage;

$rotator = new RotateImage();
$result = $rotator->rotate($file, 90);
```

---

## 6️⃣ HTML to Image

Converts a live HTML page or URL to an image using **ScreenshotLayer API** (no Puppeteer required).

```php
use Scrapify\ImageTools\HtmlToImage;

$htmlToImage = new HtmlToImage();
$result = $htmlToImage->convert('https://example.com', 'png');

if ($result['success']) {
    echo "Image saved at: " . $result['url'];
}
```

**Example Class Implementation:**

```php
private $apiKey = 'YOUR_SCREENSHOTLAYER_API_KEY';
```

Make sure to replace with your actual API key.

---

## 7️⃣ Upscale Image

```php
use Scrapify\ImageTools\UpscaleImage;

$upscaler = new UpscaleImage();
$result = $upscaler->upscale($file, 2.0);
```

---

## 8️⃣ Remove Background (remove.bg API)

Removes the background of an image using the **remove.bg API**.

```php
use Scrapify\ImageTools\RemoveBG;

$removeBG = new RemoveBG();
$result = $removeBG->remove($request->file('image_file'));
```

**Example Class Implementation:**

```php
$apiKey = 'YOUR_REMOVE_BG_API_KEY';
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

curl_close($ch);
```

---

## 9️⃣ Image to Text (OCR)

Extracts text from an image using [`thiagoalessio/tesseract_ocr`](https://github.com/thiagoalessio/tesseract-ocr-for-php).
No need to install Tesseract manually — it runs directly from PHP.

```php
use Scrapify\ImageTools\ImageToText;

$imageTool = new ImageToText($file);
$text = $imageTool->process();
```

