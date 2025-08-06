<?php

declare(strict_types=1);

namespace Tests\Helpers;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use RuntimeException;

final class ImageTestHelper
{
    /**
     * Create a real test image file with specified dimensions.
     */
    public static function createTestImage(
        string $filename,
        int $width,
        int $height,
        string $format = 'png',
        int $sizeKb = 1
    ): UploadedFile {
        $extension = $format === 'jpeg' ? 'jpg' : $format;
        $mimeType = $format === 'jpeg' ? 'image/jpeg' : "image/{$format}";

        // Create a temporary file
        $tempPath = tempnam(sys_get_temp_dir(), 'test_image_');
        $tempFile = $tempPath.'.'.$extension;
        rename($tempPath, $tempFile);

        // Create a simple colored image using GD
        $image = imagecreatetruecolor($width, $height);
        if ($image === false) {
            throw new RuntimeException('Failed to create test image');
        }

        // Fill with a solid color (blue)
        $blue = imagecolorallocate($image, 0, 100, 255);
        if ($blue === false) {
            imagedestroy($image);
            throw new RuntimeException('Failed to allocate color for test image');
        }

        imagefill($image, 0, 0, $blue);

        // Save the image
        $success = match ($format) {
            'png' => imagepng($image, $tempFile),
            'jpg', 'jpeg' => imagejpeg($image, $tempFile, 90),
            default => throw new InvalidArgumentException("Unsupported format: {$format}")
        };

        imagedestroy($image);

        if (! $success) {
            unlink($tempFile);
            throw new RuntimeException('Failed to save test image');
        }

        // Create UploadedFile from the real file
        return new UploadedFile(
            $tempFile,
            $filename,
            $mimeType,
            null,
            true // test mode
        );
    }

    /**
     * Create a test image file that's too small (under 1KB).
     */
    public static function createSmallTestImage(string $filename = 'small.png'): UploadedFile
    {
        return self::createTestImage($filename, 10, 10, 'png');
    }

    /**
     * Create a test image file that's too large (but still a valid image).
     */
    public static function createLargeTestFile(string $filename, int $sizeKb): UploadedFile
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION) ?: 'png';
        $format = $extension === 'jpg' ? 'jpeg' : $extension;
        $mimeType = $format === 'jpeg' ? 'image/jpeg' : "image/{$format}";

        // Create a temporary file
        $tempPath = tempnam(sys_get_temp_dir(), 'large_test_');
        $tempFile = $tempPath.'.'.$extension;
        rename($tempPath, $tempFile);

        // For large files, we need to create larger images or lower quality
        // Let's create a very large image that will result in a large file
        $width = min(4000, (int) sqrt($sizeKb * 200)); // Rough calculation for large dimensions
        $height = $width;

        $image = imagecreatetruecolor($width, $height);
        if ($image === false) {
            throw new RuntimeException('Failed to create large test image');
        }

        // Fill with random colors to make file larger
        for ($x = 0; $x < $width; $x += 10) {
            for ($y = 0; $y < $height; $y += 10) {
                $color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
                if ($color !== false) {
                    imagefilledrectangle($image, $x, $y, $x + 10, $y + 10, $color);
                }
            }
        }

        // Save with low quality to make file larger
        $success = match ($format) {
            'png' => imagepng($image, $tempFile, 0), // 0 = no compression
            'jpg', 'jpeg' => imagejpeg($image, $tempFile, 30), // Low quality = larger file
            default => throw new InvalidArgumentException("Unsupported format: {$format}")
        };

        imagedestroy($image);

        if (! $success) {
            unlink($tempFile);
            throw new RuntimeException('Failed to save large test image');
        }

        // If the file is still not large enough, pad it (this will make it invalid for strict image validation)
        $currentSize = filesize($tempFile);
        $targetSize = $sizeKb * 1024;

        if ($currentSize < $targetSize) {
            // Append non-image data to make it larger (this makes it technically invalid but tests might need this)
            $padding = str_repeat('A', $targetSize - $currentSize);
            file_put_contents($tempFile, $padding, FILE_APPEND);
        }

        return new UploadedFile(
            $tempFile,
            $filename,
            $mimeType,
            null,
            true
        );
    }

    /**
     * Create a non-image file for testing validation.
     */
    public static function createNonImageFile(string $filename, string $mimeType = 'application/pdf'): UploadedFile
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'non_image_');
        file_put_contents($tempPath, 'This is not an image file content');

        return new UploadedFile(
            $tempPath,
            $filename,
            $mimeType,
            null,
            true
        );
    }
}
