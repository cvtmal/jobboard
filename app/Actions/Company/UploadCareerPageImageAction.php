<?php

declare(strict_types=1);

namespace App\Actions\Company;

use App\Models\Company;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use RuntimeException;
use Throwable;

final class UploadCareerPageImageAction
{
    /**
     * Execute the action to upload and process a single career page image.
     *
     * @throws Throwable
     */
    public function execute(Company $company, UploadedFile $file): string
    {
        return $this->uploadSingleImage($file, $company->id);
    }

    /**
     * Upload a single career page image.
     */
    private function uploadSingleImage(UploadedFile $file, int $companyId): string
    {
        try {
            // Generate unique filename
            $filename = $this->generateFilename($file, $companyId);
            $path = "company-images/career-page/{$filename}";

            // Process the image
            $image = Image::read($file);

            // Validate minimum dimensions (as per frontend validation)
            if ($image->width() < 752 || $image->height() < 480) {
                throw new RuntimeException('Image dimensions must be at least 752x480 pixels.');
            }

            // Resize image if it's too large while maintaining aspect ratio
            // Max width: 1200px to keep file sizes reasonable
            if ($image->width() > 1200) {
                $image->scale(width: 1200);
            }

            // Store the processed image
            $stored = Storage::disk('public')->put($path, (string) $image->encode());

            if (! $stored) {
                throw new RuntimeException('Failed to store career page image.');
            }

            // Return the relative path (not the full URL)
            // The model's accessor will handle URL generation
            return $path;

        } catch (Throwable $e) {
            // Log the error and re-throw
            logger()->error('Failed to upload career page image', [
                'company_id' => $companyId,
                'filename' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate a unique filename for the career page image.
     */
    private function generateFilename(UploadedFile $file, int $companyId): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $random = Str::random(8);

        return "company-{$companyId}-career-{$timestamp}-{$random}.{$extension}";
    }
}
