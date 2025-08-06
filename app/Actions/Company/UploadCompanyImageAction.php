<?php

declare(strict_types=1);

namespace App\Actions\Company;

use App\Models\Company;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Laravel\Facades\Image;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class UploadCompanyImageAction
{
    /**
     * Execute the action to upload and process a company image.
     *
     * @param  string  $type  'logo' or 'banner'
     *
     * @throws Throwable
     */
    public function execute(Company $company, UploadedFile $file, string $type): bool
    {
        // Validate type
        if (! in_array($type, ['logo', 'banner'])) {
            throw new InvalidArgumentException('Invalid image type. Must be "logo" or "banner".');
        }

        // Delete existing image if it exists
        $this->deleteExistingImage($company, $type);

        // Generate unique filename
        $filename = $this->generateFilename($file, $type, $company->id);
        $path = "company-images/{$type}s/{$filename}";

        // Get image dimensions
        $image = Image::read($file);
        $dimensions = [
            'width' => $image->width(),
            'height' => $image->height(),
        ];

        // Process and resize image based on type
        $processedImage = $this->processImage($image, $type);

        // Store the processed image
        $stored = Storage::disk('public')->put($path, (string) $processedImage->encode());

        if (! $stored) {
            throw new RuntimeException("Failed to store {$type} image.");
        }

        // Update company model with image metadata
        $updateData = [
            "{$type}_path" => $path,
            "{$type}_original_name" => $file->getClientOriginalName(),
            "{$type}_file_size" => $file->getSize(),
            "{$type}_mime_type" => $file->getMimeType(),
            "{$type}_dimensions" => $dimensions,
            "{$type}_uploaded_at" => now(),
        ];

        return $company->update($updateData);
    }

    /**
     * Delete existing image from storage.
     */
    private function deleteExistingImage(Company $company, string $type): void
    {
        $pathColumn = "{$type}_path";

        if ($company->{$pathColumn} && Storage::disk('public')->exists($company->{$pathColumn})) {
            Storage::disk('public')->delete($company->{$pathColumn});
        }
    }

    /**
     * Generate a unique filename for the image.
     */
    private function generateFilename(UploadedFile $file, string $type, int $companyId): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $random = Str::random(8);

        return "company-{$companyId}-{$type}-{$timestamp}-{$random}.{$extension}";
    }

    /**
     * Process and resize image based on type.
     */
    private function processImage(ImageInterface $image, string $type): ImageInterface
    {
        return match ($type) {
            // Resize logo to various sizes, keeping aspect ratio
            // Create a square version optimized for display
            'logo' => $image->cover(400, 400),
            // Resize banner maintaining 3:1 aspect ratio
            'banner' => $image->cover(1200, 400),
            default => $image,
        };
    }
}
