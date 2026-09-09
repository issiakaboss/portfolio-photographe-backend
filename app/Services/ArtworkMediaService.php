<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ArtworkMediaService
{
    public function thumbnailUrl(string $path, int $width = 960): ?string
    {
        $disk = Storage::disk('public');
        $source = $disk->path($path);

        if (! is_file($source)) {
            return null;
        }

        $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));
        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return null;
        }

        $cacheName = sha1($path . '|' . filemtime($source) . '|' . $width) . '.webp';
        $thumbnailPath = 'thumbnails/' . $cacheName;

        if (! $disk->exists($thumbnailPath)) {
            $this->createThumbnail($source, $disk->path($thumbnailPath), $width, $extension);
        }

        return $disk->exists($thumbnailPath) ? '/storage/' . $thumbnailPath : null;
    }

    private function createThumbnail(string $source, string $target, int $width, string $extension): void
    {
        $dimensions = getimagesize($source);
        if ($dimensions === false || $dimensions[0] < 1 || $dimensions[1] < 1) {
            return;
        }

        $image = match ($extension) {
            'jpg', 'jpeg' => imagecreatefromjpeg($source),
            'png' => imagecreatefrompng($source),
            'webp' => imagecreatefromwebp($source),
            default => false,
        };

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
