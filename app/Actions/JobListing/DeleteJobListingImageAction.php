<?php

declare(strict_types=1);

namespace App\Actions\JobListing;

use App\Models\JobListing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

final readonly class DeleteJobListingImageAction
{
    /**
     * Execute the action to delete a job listing image.
     *
     * @param  string  $type  'logo' or 'banner'
     */
    public function execute(JobListing $jobListing, string $type): bool
    {
        // Validate type
        if (! in_array($type, ['logo', 'banner'])) {
            throw new InvalidArgumentException('Invalid image type. Must be "logo" or "banner".');
        }

        return DB::transaction(function () use ($jobListing, $type): bool {
            // Get the path field based on type
            $pathField = $type === 'logo' ? 'logo_path' : 'banner_path';
            $path = $jobListing->{$pathField};

            // If no path exists, nothing to delete
            if (! $path) {
                return true;
            }

            // Delete the file from storage
            $fileDeleted = Storage::disk('public')->delete($path);

            if ($fileDeleted) {
                // Reset all related fields based on type
                $updates = match ($type) {
                    'logo' => [
                        'logo_path' => null,
                        'logo_original_name' => null,
                        'logo_file_size' => null,
                        'logo_mime_type' => null,
                        'logo_dimensions' => null,
                        'logo_uploaded_at' => null,
                        'use_company_logo' => true, // Reset to use company logo
                    ],
                    'banner' => [
                        'banner_path' => null,
                        'banner_original_name' => null,
                        'banner_file_size' => null,
                        'banner_mime_type' => null,
                        'banner_dimensions' => null,
                        'banner_uploaded_at' => null,
                        'use_company_banner' => true, // Reset to use company banner
                    ],
                };

                $jobListing->update($updates);
            }

            return $fileDeleted;
        });
    }
}
