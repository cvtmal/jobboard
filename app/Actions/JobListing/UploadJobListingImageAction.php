<?php

declare(strict_types=1);

namespace App\Actions\JobListing;

use App\Models\JobListing;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Laravel\Facades\Image;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class UploadJobListingImageAction
{
    /**
     * Execute the action to upload and process a job listing image.
     *
     * @param  string  $type  'logo' or 'banner'
     *
     * @throws Throwable
     */
    public function execute(JobListing $jobListing, UploadedFile $file, string $type): bool
    {
        // Validate type
        if (! in_array($type, ['logo', 'banner'])) {
            throw new InvalidArgumentException('Invalid image type. Must be "logo" or "banner".');
        }

        // Delete existing image if it exists
        $this->deleteExistingImage($jobListing, $type);

        // Generate unique filename
        $filename = $this->generateFilename($file, $type, $jobListing->id);
        $path = "job-listing-images/{$type}s/{$filename}";

        // Process and resize image based on type
        $image = Image::read($file);
        $processedImage = $this->processImage($image, $type);

        // Get processed image dimensions
        $dimensions = [
            'width' => $processedImage->width(),
            'height' => $processedImage->height(),
        ];

        // Store the processed image
        $stored = Storage::disk('public')->put($path, (string) $processedImage->encode());

        if (! $stored) {
            throw new RuntimeException("Failed to store {$type} image.");
        }

        // Update job listing model with image metadata
        $updateData = [
            "{$type}_path" => $path,
            "{$type}_original_name" => $file->getClientOriginalName(),
            "{$type}_file_size" => $file->getSize(),
            "{$type}_mime_type" => $file->getMimeType(),
            "{$type}_dimensions" => $dimensions,
            "{$type}_uploaded_at" => now(),
            "use_company_{$type}" => false, // Set to use custom image
        ];

        return $jobListing->update($updateData);
    }

    /**
     * Delete existing image from storage.
     */
    private function deleteExistingImage(JobListing $jobListing, string $type): void
    {
        $pathColumn = "{$type}_path";

        if ($jobListing->{$pathColumn} && Storage::disk('public')->exists($jobListing->{$pathColumn})) {
            Storage::disk('public')->delete($jobListing->{$pathColumn});
        }
    }

    /**
     * Generate a unique filename for the image.
     */
    private function generateFilename(UploadedFile $file, string $type, int $jobListingId): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $random = Str::random(8);

        return "job-listing-{$jobListingId}-{$type}-{$timestamp}-{$random}.{$extension}";
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
