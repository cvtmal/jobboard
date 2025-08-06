<?php

declare(strict_types=1);

namespace App\Actions\Company;

use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Throwable;

final class DeleteCompanyImageAction
{
    /**
     * Execute the action to delete a company image.
     *
     * @param  string  $type  'logo' or 'banner'
     *
     * @throws Throwable
     */
    public function execute(Company $company, string $type): bool
    {
        // Validate type
        if (! in_array($type, ['logo', 'banner'])) {
            throw new InvalidArgumentException('Invalid image type: '.$type);
        }

        $pathColumn = "{$type}_path";

        // Check if image exists
        if (! $company->{$pathColumn}) {
            return true; // Nothing to delete, consider it successful
        }

        $imagePath = $company->{$pathColumn};
        $deleted = true;

        // Delete main file from storage
        if (Storage::disk('public')->exists($imagePath)) {
            $deleted = Storage::disk('public')->delete($imagePath);
        }

        // Delete image variations
        $this->deleteImageVariations($imagePath);

        // Clear image metadata from database regardless of file deletion success
        // This ensures the database remains consistent even if the file was already missing
        $updateData = [
            "{$type}_path" => null,
            "{$type}_original_name" => null,
            "{$type}_file_size" => null,
            "{$type}_mime_type" => null,
            "{$type}_dimensions" => null,
            "{$type}_uploaded_at" => null,
        ];

        $updated = $company->update($updateData);

        return $deleted && $updated;
    }

    /**
     * Delete image variations with different dimensions.
     */
    private function deleteImageVariations(string $imagePath): void
    {
        $pathInfo = pathinfo($imagePath);
        $directory = $pathInfo['dirname'] ?? '';
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'] ?? '';

        // If we don't have enough path info, skip variation deletion
        if (empty($filename) || empty($directory)) {
            return;
        }

        // Get all files in the directory (only if directory exists)
        if (! Storage::disk('public')->exists($directory)) {
            return;
        }

        $allFiles = Storage::disk('public')->files($directory);

        // Find variations that match the pattern: filename_WIDTHxHEIGHT.extension
        foreach ($allFiles as $file) {
            $baseName = basename($file);

            // Pattern for variations with extension
            if (! empty($extension)) {
                $pattern = '/^'.preg_quote($filename, '/').'_\d+x\d+\.'.preg_quote($extension, '/').'$/';
            } else {
                // Pattern for variations without extension (fallback)
                $pattern = '/^'.preg_quote($filename, '/').'_\d+x\d+$/';
            }

            if (preg_match($pattern, $baseName)) {
                Storage::disk('public')->delete($file);
            }
        }
    }
}
