<?php

namespace Scrapify\ImageTools;

use Illuminate\Support\ServiceProvider;

class ImageToolsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Bind individual image tool classes to the service container
        $this->app->bind(CompressImage::class, function ($app) {
            return new CompressImage();
        });

        $this->app->bind(ConvertImage::class, function ($app) {
            return new ConvertImage();
        });

        $this->app->bind(CropImage::class, function ($app) {
            return new CropImage();
        });

        $this->app->bind(HtmlToImage::class, function ($app) {
            return new HtmlToImage();
        });

        $this->app->bind(RemoveBG::class, function ($app) {
            return new RemoveBG();
        });

        $this->app->bind(ResizeImage::class, function ($app) {
            return new ResizeImage();
        });

        $this->app->bind(RotateImage::class, function ($app) {
            return new RotateImage();
        });

        $this->app->bind(UpscaleImage::class, function ($app) {
            return new UpscaleImage();
        });

        // Note: ImageToText requires an UploadedFile instance in its constructor,
        // so it's typically instantiated directly: new ImageToText($uploadedFile)
        // rather than being bound to the service container for direct resolution.
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
