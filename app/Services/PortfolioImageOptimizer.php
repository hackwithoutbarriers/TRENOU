<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use InvalidArgumentException;

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
        $canEncodeWebp = function_exists('imagewebp');

        foreach ($paths as $path) {
            if (blank($path)) {
                continue;
            }

            $this->ensureSafeRelativePath($path);
            $absolutePath = $disk->path($path);
            $storageRoot = realpath($disk->path(''));
            $resolvedPath = realpath($absolutePath);

            if ($storageRoot === false || ($resolvedPath !== false && ! $this->isWithinRoot($resolvedPath, $storageRoot))) {
                throw new InvalidArgumentException('The portfolio image path is outside the public storage disk.');
            }

            if (! file_exists($absolutePath)) {
                $optimized[] = $path;

                continue;
            }

            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            if ($extension === 'webp') {
                $optimized[] = $path;

                continue;
            }

            if (! $canEncodeWebp) {
                Log::warning('WebP encoding is unavailable; keeping the original portfolio image.', [
                    'path' => $path,
                ]);
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

    private function ensureSafeRelativePath(string $path): void
    {
        if ($path === '' || str_starts_with($path, '/') || str_starts_with($path, '\\') || preg_match('/\A[A-Za-z]:[\\\\\/]/', $path) || str_contains(str_replace('\\', '/', $path), '../')) {
            throw new InvalidArgumentException('The portfolio image path must be relative to public storage.');
        }
    }

    private function isWithinRoot(string $path, string $root): bool
    {
        return $path === $root || str_starts_with($path, $root.DIRECTORY_SEPARATOR);
    }
}
