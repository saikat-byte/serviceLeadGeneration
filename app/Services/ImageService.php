<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    public function processUserAvatar(?string $path, int $userId, string $userName): ?string
    {
        if (!$path) return null;

        $disk = Storage::disk('public');

        // Path na thakle return korbe jate app crash na kore
        if (!$disk->exists($path)) {
            return $path;
        }

        $slug = Str::slug($userName);
        $expectedPrefix = "{$userId}-{$slug}-profile-";

        // File jodi aage thekei optimized thake tahole processor e dhukbei na
        if (str_ends_with($path, '.webp') && str_contains(basename($path), $expectedPrefix)) {
            return $path;
        }

        $absolutePath = $disk->path($path);
        $image = $this->createImage($absolutePath);

        $width = imagesx($image);
        $height = imagesy($image);
        $cropSize = min($width, $height);
        $sourceX = (int) (($width - $cropSize) / 2);
        $sourceY = (int) (($height - $cropSize) / 2);

        $hash = substr(md5(uniqid()), 0, 5); // Thumbnail reload korar jonno
        $newFilename = "{$expectedPrefix}{$hash}.webp";
        $directory = 'users/avatars';
        $finalPath = "{$directory}/{$newFilename}";

        if (!$disk->exists($directory)) {
            $disk->makeDirectory($directory);
        }

        $saved = false;

        foreach ([500, 400, 300] as $size) {
            $canvas = imagecreatetruecolor($size, $size);
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
            imagefill($canvas, 0, 0, $transparent);

            imagecopyresampled($canvas, $image, 0, 0, $sourceX, $sourceY, $size, $size, $cropSize, $cropSize);

            foreach ([85, 75, 60, 45] as $quality) {
                ob_start();
                imagewebp($canvas, null, $quality);
                $data = ob_get_clean();

                if (strlen($data) <= 100 * 1024) {
                    $disk->put($finalPath, $data);
                    $saved = true;
                    imagedestroy($canvas);
                    break 2;
                }
            }
            imagedestroy($canvas);
        }

        imagedestroy($image);

        if (!$saved) {
            throw new \RuntimeException('Unable to compress profile image below 100 KB.');
        }

        // Original unoptimized file ta delete kora hochhe
        if ($path !== $finalPath && $disk->exists($path)) {
            $disk->delete($path);
        }

        // Ei user er purono image gulo cleanup kora hocche
        $this->cleanupOldAvatars($userId, $disk, $directory, $newFilename);

        return $finalPath;
    }

    private function cleanupOldAvatars(int $userId, $disk, string $directory, string $currentFilename): void
    {
        $files = $disk->files($directory);
        foreach ($files as $file) {
            $basename = basename($file);
            // Onno karo profile pic jeno delete na hoy tai prefix check
            if (str_starts_with($basename, "{$userId}-") && $basename !== $currentFilename) {
                $disk->delete($file);
            }
        }
    }

    private function createImage(string $path)
    {
        $info = getimagesize($path);

        return match ($info['mime'] ?? null) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png'  => imagecreatefrompng($path),
            'image/webp' => imagecreatefromwebp($path),
            'image/gif'  => imagecreatefromgif($path),
            default => throw new \InvalidArgumentException('Unsupported image format.')
        };
    }
}