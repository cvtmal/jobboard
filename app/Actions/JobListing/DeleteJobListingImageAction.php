<?php

declare(strict_types=1);

namespace App\Actions\JobListing;

use App\Models\JobListing;
use InvalidArgumentException;

final class DeleteJobListingImageAction
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

        // Use the model method to delete the image
        return match ($type) {
            'logo' => $jobListing->deleteCustomLogo(),
            'banner' => $jobListing->deleteCustomBanner(),
        };
    }
}
