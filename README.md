📸 Scrapify Image Tools Library

A Laravel package for powerful **image processing** including compression, conversion, cropping, resizing, rotation, HTML-to-image conversion, upscaling, background removal, and OCR (image-to-text).  

This library wraps several industry-standard packages into an easy-to-use Laravel API.

---

## 📦 Installation

```bash
composer require scrapify-dev/image-tools
````

### Requirements

* **PHP**: `^8.2`
* **Laravel**: `^9.0 | ^10.0 | ^11.0 | ^12.0`
* **Dependencies**:

  * `intervention/image:^3.0`
  * `barryvdh/laravel-dompdf:^3.1`
  * `spatie/browsershot:^5.0`
  * `spatie/pdf-to-image:^1.2`
  * `thiagoalessio/tesseract_ocr:^2.13`
  * `illuminate/support` (as per Laravel version)

---

## 🚀 Quick Start Example

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

**Description:** Reduces file size with minimal quality loss. PNGs are auto-converted to JPG for better compression.

**Usage:**

```php
use Scrapify\ImageTools\CompressImage;

$compressor = new CompressImage();
$result = $compressor->compress($request->file('image_file'));
```

**Controller Example:**
[Click to view full code](#) *(keep your code snippet here)*

**Route:**

```php
Route::get('/compress-image', [CompressImageController::class, 'compressImageView'])->name('compress.image.view');
Route::post('/compress-image', [CompressImageController::class, 'compressImage'])->name('compress.image');
```

---

## 2️⃣ Convert Image

**Supported Formats:** `jpg`, `png`, `gif`, `webp`, `avif`, `pdf`

```php
use Scrapify\ImageTools\ConvertImage;

$converter = new ConvertImage();
$result = $converter->convert($request->file('image_file'), 'webp');
```

**Route:**

```php
Route::get('/convert-image', [ConvertImageController::class, 'convertImageView'])->name('convert.image.view');
Route::post('/convert-image', [ConvertImageController::class, 'convertImage'])->name('convert.image');
```

---

## 3️⃣ Crop Image

**Parameters:** `x`, `y`, `width`, `height`

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

**Dependencies:**

```bash
npm install puppeteer
```

```php
use Scrapify\ImageTools\HtmlToImage;

$htmlToImage = new HtmlToImage();
$result = $htmlToImage->convert('<h1>Hello</h1>', 'png');
```

---

## 7️⃣ Upscale Image

```php
use Scrapify\ImageTools\UpscaleImage;

$upscaler = new UpscaleImage();
$result = $upscaler->upscale($file, 2.0);
```

---

## 8️⃣ Remove Background

**Python Dependencies:**

```bash
pip install rembg
```

```php
use Scrapify\ImageTools\RemoveBG;

$removeBG = new RemoveBG();
$result = $removeBG->remove($file);
```

## 9️⃣ Image to Text

**Description:**
Extracts text from an image using OCR, powered by the [`thiagoalessio/tesseract_ocr`](https://github.com/thiagoalessio/tesseract-ocr-for-php) PHP library.
No need to install Tesseract manually — the library handles it for you.

**Usage:**

```php
use Scrapify\ImageTools\ImageToText;

$imageTool = new ImageToText($file);
$text = $imageTool->process();

// Example output
return response()->json([
    'success' => true,
    'extracted_text' => $text
]);
```


