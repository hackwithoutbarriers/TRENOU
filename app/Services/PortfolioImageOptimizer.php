<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class PortfolioImageOptimizer
{
    /**
     * Convert uploaded portfolio images to WebP format and reduce their size.
     *
     * @param  array<int, string>  $paths
     * @return array<int, string>
     */
    public function optimize(array $paths): array
    {
        $optimized = [];
        $disk = Storage::disk('public');
        $imageManager = ImageManager::gd();

        foreach ($paths as $path) {
            if (blank($path)) {
                continue;
            }

            $absolutePath = $disk->path($path);

            if (! file_exists($absolutePath)) {
                $optimized[] = $path;

                continue;
            }

            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            if ($extension === 'webp') {
                $optimized[] = $path;

                continue;
            }

            $webpPath = preg_replace('/\.[^.]+$/', '.webp', $path);
            $webpAbsolutePath = $disk->path($webpPath);

            $imageManager->read($absolutePath)
                ->resizeDown(1600, 1200)
                ->toWebp(72)
                ->save($webpAbsolutePath);

            if ($path !== $webpPath && file_exists($absolutePath)) {
                unlink($absolutePath);
            }

            $optimized[] = $webpPath;
        }

        return $optimized;
    }
}
