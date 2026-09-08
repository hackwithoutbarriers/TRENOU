<?php

namespace Tests\Unit;

use App\Services\PortfolioImageOptimizer;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

class PortfolioImageOptimizerTest extends TestCase
{
    public function test_paths_outside_public_storage_are_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new PortfolioImageOptimizer)->optimize(['../private/image.jpg']);
    }

    public function test_supported_images_are_converted_to_webp(): void
    {
        if (! function_exists('imagewebp')) {
            $this->markTestSkipped('GD WebP support is unavailable.');
        }

        Storage::fake('public');
        config(['filesystems.default' => 'public']);
        $image = imagecreatetruecolor(2, 2);
        $temporaryPath = tempnam(sys_get_temp_dir(), 'portfolio-test-');

        imagejpeg($image, $temporaryPath);
        imagedestroy($image);
        Storage::disk('public')->put('portfolio/source.jpg', file_get_contents($temporaryPath));
        unlink($temporaryPath);

        $optimized = (new PortfolioImageOptimizer)->optimize(['portfolio/source.jpg']);

        $this->assertSame(['portfolio/source.webp'], $optimized);
        Storage::disk('public')->assertExists('portfolio/source.webp');
        Storage::disk('public')->assertMissing('portfolio/source.jpg');
    }
}
