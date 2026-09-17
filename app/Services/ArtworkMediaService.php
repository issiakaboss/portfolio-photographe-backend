<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ArtworkMediaService
{
    public function optimize(string $path, string $diskName = 'public', int $maxWidth = 1920, int $quality = 80): ?string
    {
        $disk = Storage::disk($diskName);
        $sourcePath = $disk->path($path);

        if (! is_file($sourcePath)) {
            return null;
        }

        $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        $image = $this->loadImage($sourcePath, $extension);
        if ($image === false) {
            return null;
        }

        $sourceWidth = imagesx($image);
        $sourceHeight = imagesy($image);
        $targetWidth = min($maxWidth, $sourceWidth);
        $targetHeight = (int) round($sourceHeight * ($targetWidth / $sourceWidth));
        $optimized = $this->resize($image, $targetWidth, $targetHeight);
        $optimizedPath = preg_replace('/\.[^.]+$/', '.webp', $path) ?: $path . '.webp';
        $targetPath = $disk->path($optimizedPath);

        if (! is_dir(dirname($targetPath))) {
            mkdir(dirname($targetPath), 0755, true);
        }

        $temporaryTargetPath = $targetPath . '.tmp';
        $saved = imagewebp($optimized, $temporaryTargetPath, $quality);
        imagedestroy($optimized);
        imagedestroy($image);

        if (! $saved) {
            return null;
        }

        rename($temporaryTargetPath, $targetPath);

        if ($optimizedPath !== $path) {
            $disk->delete($path);
        }

        return $optimizedPath;
    }

    public function thumbnailUrl(string $path, string $diskName = 'public', int $width = 480): ?string
    {
        $disk = Storage::disk($diskName);
        $source = $disk->path($path);

        if (! is_file($source)) {
            return null;
        }

        $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));
        if ($extension !== 'webp') {
            return null;
        }

        $cacheName = sha1($path . '|' . filemtime($source) . '|' . $width) . '.webp';
        $thumbnailPath = 'thumbnails/' . $cacheName;

        if (! $disk->exists($thumbnailPath)) {
            $this->createThumbnail($source, $disk->path($thumbnailPath), $width, $extension);
        }

        return $diskName === 'public' && $disk->exists($thumbnailPath)
            ? '/storage/' . $thumbnailPath
            : null;
    }

    private function loadImage(string $source, string $extension): \GdImage|false
    {
        return match ($extension) {
            'jpg', 'jpeg' => imagecreatefromjpeg($source),
            'png' => imagecreatefrompng($source),
            'webp' => imagecreatefromwebp($source),
            default => false,
        };
    }

    private function resize(\GdImage $image, int $width, int $height): \GdImage
    {
        $resized = imagecreatetruecolor($width, $height);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));

        return $resized;
    }

    private function createThumbnail(string $source, string $target, int $width, string $extension): void
    {
        $dimensions = getimagesize($source);
        if ($dimensions === false || $dimensions[0] < 1 || $dimensions[1] < 1) {
            return;
        }

        $image = $this->loadImage($source, $extension);

        if ($image === false) {
            return;
        }

        $height = (int) round($dimensions[1] * min(1, $width / $dimensions[0]));
        $thumbnail = imagecreatetruecolor(min($width, $dimensions[0]), $height);
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, imagesx($thumbnail), $height, $dimensions[0], $dimensions[1]);

        $directory = dirname($target);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        imagewebp($thumbnail, $target, 78);
        imagedestroy($thumbnail);
        imagedestroy($image);
    }
}
