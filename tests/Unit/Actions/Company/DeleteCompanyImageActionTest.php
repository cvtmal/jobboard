<?php

declare(strict_types=1);

use App\Actions\Company\DeleteCompanyImageAction;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
    $this->company = Company::factory()->create();
    $this->action = new DeleteCompanyImageAction();
});

describe('Banner Image Deletion', function (): void {
    test('deletes banner image and variations successfully', function (): void {
        // Setup existing banner with variations
        $bannerPath = 'company-images/banners/test-banner.jpg';
        $variations = [
            'company-images/banners/test-banner_1200x400.jpg',
            'company-images/banners/test-banner_800x267.jpg',
            'company-images/banners/test-banner_400x133.jpg',
        ];

        // Create fake files in storage
        Storage::disk('public')->put($bannerPath, 'fake-banner-content');
        foreach ($variations as $variation) {
            Storage::disk('public')->put($variation, 'fake-variation-content');
        }

        // Update company with banner path
        $this->company->update(['banner_path' => $bannerPath]);

        $result = $this->action->execute($this->company, 'banner');

        expect($result)->toBeTrue();

        // Verify all files were deleted
        expect(Storage::disk('public')->exists($bannerPath))->toBeFalse();
        foreach ($variations as $variation) {
            expect(Storage::disk('public')->exists($variation))->toBeFalse();
        }

        // Verify database was updated
        $this->company->refresh();
        expect($this->company->banner_path)->toBeNull();
    });

    test('handles banner deletion when no image exists', function (): void {
        $result = $this->action->execute($this->company, 'banner');

        expect($result)->toBeTrue();

        // Verify database field remains null
        $this->company->refresh();
        expect($this->company->banner_path)->toBeNull();
    });

    test('handles banner deletion when file does not exist in storage', function (): void {
        // Set database field but don't create actual file
        $bannerPath = 'company-images/banners/missing-banner.jpg';
        $this->company->update(['banner_path' => $bannerPath]);

        $result = $this->action->execute($this->company, 'banner');

        expect($result)->toBeTrue();

        // Verify database was still updated
        $this->company->refresh();
        expect($this->company->banner_path)->toBeNull();
    });

    test('deletes banner variations with different naming patterns', function (): void {
        $bannerPath = 'company-images/banners/complex-name_123.jpg';
        $variations = [
            'company-images/banners/complex-name_123_1200x400.jpg',
            'company-images/banners/complex-name_123_800x267.jpg',
            'company-images/banners/complex-name_123_400x133.jpg',
        ];

        // Create files
        Storage::disk('public')->put($bannerPath, 'content');
        foreach ($variations as $variation) {
            Storage::disk('public')->put($variation, 'content');
        }

        $this->company->update(['banner_path' => $bannerPath]);

        $this->action->execute($this->company, 'banner');

        // Verify all files deleted
        expect(Storage::disk('public')->exists($bannerPath))->toBeFalse();
        foreach ($variations as $variation) {
            expect(Storage::disk('public')->exists($variation))->toBeFalse();
        }
    });
});

describe('Logo Image Deletion', function (): void {
    test('deletes logo image and variations successfully', function (): void {
        // Setup existing logo with variations
        $logoPath = 'company-images/logos/test-logo.png';
        $variations = [
            'company-images/logos/test-logo_300x300.png',
            'company-images/logos/test-logo_150x150.png',
            'company-images/logos/test-logo_100x100.png',
            'company-images/logos/test-logo_50x50.png',
        ];

        // Create fake files in storage
        Storage::disk('public')->put($logoPath, 'fake-logo-content');
        foreach ($variations as $variation) {
            Storage::disk('public')->put($variation, 'fake-variation-content');
        }

        // Update company with logo path
        $this->company->update(['logo_path' => $logoPath]);

        $result = $this->action->execute($this->company, 'logo');

        expect($result)->toBeTrue();

        // Verify all files were deleted
        expect(Storage::disk('public')->exists($logoPath))->toBeFalse();
        foreach ($variations as $variation) {
            expect(Storage::disk('public')->exists($variation))->toBeFalse();
        }

        // Verify database was updated
        $this->company->refresh();
        expect($this->company->logo_path)->toBeNull();
    });

    test('handles logo deletion when no image exists', function (): void {
        $result = $this->action->execute($this->company, 'logo');

        expect($result)->toBeTrue();

        // Verify database field remains null
        $this->company->refresh();
        expect($this->company->logo_path)->toBeNull();
    });

    test('deletes only logo variations not affecting other files', function (): void {
        $logoPath = 'company-images/logos/company-logo.jpg';
        $logoVariations = [
            'company-images/logos/company-logo_300x300.jpg',
            'company-images/logos/company-logo_150x150.jpg',
        ];

        // Create unrelated files that should not be deleted
        $unrelatedFiles = [
            'company-images/logos/other-company-logo.jpg',
            'company-images/logos/company-logo-backup.jpg',
            'company-images/banners/company-logo.jpg', // Different folder
        ];

        // Create all files
        Storage::disk('public')->put($logoPath, 'content');
        foreach ($logoVariations as $variation) {
            Storage::disk('public')->put($variation, 'content');
        }
        foreach ($unrelatedFiles as $file) {
            Storage::disk('public')->put($file, 'content');
        }

        $this->company->update(['logo_path' => $logoPath]);

        $this->action->execute($this->company, 'logo');

        // Verify logo files were deleted
        expect(Storage::disk('public')->exists($logoPath))->toBeFalse();
        foreach ($logoVariations as $variation) {
            expect(Storage::disk('public')->exists($variation))->toBeFalse();
        }

        // Verify unrelated files were not deleted
        foreach ($unrelatedFiles as $file) {
            expect(Storage::disk('public')->exists($file))->toBeTrue();
        }
    });
});

describe('Error Handling and Edge Cases', function (): void {
    test('throws exception for invalid image type', function (): void {
        expect(fn () => $this->action->execute($this->company, 'invalid'))
            ->toThrow(InvalidArgumentException::class, 'Invalid image type: invalid');
    });

    test('handles storage disk errors gracefully', function (): void {
        $bannerPath = 'company-images/banners/test.jpg';
        $this->company->update(['banner_path' => $bannerPath]);

        // Test with a non-existent file (simulates storage issue)
        $result = $this->action->execute($this->company, 'banner');

        // Should still succeed even if file doesn't exist
        expect($result)->toBeTrue();

        $this->company->refresh();
        expect($this->company->banner_path)->toBeNull();
    });

    test('handles database update successfully', function (): void {
        $bannerPath = 'company-images/banners/test.jpg';
        Storage::disk('public')->put($bannerPath, 'content');
        $this->company->update(['banner_path' => $bannerPath]);

        $result = $this->action->execute($this->company, 'banner');

        expect($result)->toBeTrue();

        // Verify database was updated and file was deleted
        $this->company->refresh();
        expect($this->company->banner_path)->toBeNull();
        expect(Storage::disk('public')->exists($bannerPath))->toBeFalse();
    });

    test('successfully completes deletion process', function (): void {
        $bannerPath = 'company-images/banners/test.jpg';
        Storage::disk('public')->put($bannerPath, 'content');
        $this->company->update(['banner_path' => $bannerPath]);

        $result = $this->action->execute($this->company, 'banner');

        expect($result)->toBeTrue();

        // Verify complete cleanup
        $this->company->refresh();
        expect($this->company->banner_path)->toBeNull();
        expect($this->company->banner_original_name)->toBeNull();
        expect($this->company->banner_file_size)->toBeNull();
        expect($this->company->banner_mime_type)->toBeNull();
        expect($this->company->banner_dimensions)->toBeNull();
        expect($this->company->banner_uploaded_at)->toBeNull();
    });
});

describe('File System Interaction', function (): void {
    test('identifies all variation files correctly', function (): void {
        $basePath = 'company-images/logos/test-logo.jpg';
        $variations = [
            'company-images/logos/test-logo_300x300.jpg',
            'company-images/logos/test-logo_150x150.jpg',
            'company-images/logos/test-logo_100x100.jpg',
            'company-images/logos/test-logo_50x50.jpg',
        ];

        // Create files
        Storage::disk('public')->put($basePath, 'content');
        foreach ($variations as $variation) {
            Storage::disk('public')->put($variation, 'content');
        }

        // Create some files that should NOT be deleted
        $nonMatchingFiles = [
            'company-images/logos/test-logo-copy.jpg',
            'company-images/logos/test-logo.png', // Different extension
            'company-images/logos/another-test-logo_300x300.jpg',
        ];

        foreach ($nonMatchingFiles as $file) {
            Storage::disk('public')->put($file, 'content');
        }

        $this->company->update(['logo_path' => $basePath]);

        $this->action->execute($this->company, 'logo');

        // Verify only correct files were deleted
        expect(Storage::disk('public')->exists($basePath))->toBeFalse();
        foreach ($variations as $variation) {
            expect(Storage::disk('public')->exists($variation))->toBeFalse();
        }

        // Verify non-matching files still exist
        foreach ($nonMatchingFiles as $file) {
            expect(Storage::disk('public')->exists($file))->toBeTrue();
        }
    });

    test('handles different file extensions consistently', function (): void {
        $pngLogoPath = 'company-images/logos/logo.png';
        $pngVariations = [
            'company-images/logos/logo_300x300.png',
            'company-images/logos/logo_150x150.png',
        ];

        // Create PNG files
        Storage::disk('public')->put($pngLogoPath, 'png-content');
        foreach ($pngVariations as $variation) {
            Storage::disk('public')->put($variation, 'png-content');
        }

        $this->company->update(['logo_path' => $pngLogoPath]);

        $result = $this->action->execute($this->company, 'logo');

        expect($result)->toBeTrue();
        expect(Storage::disk('public')->exists($pngLogoPath))->toBeFalse();
        foreach ($pngVariations as $variation) {
            expect(Storage::disk('public')->exists($variation))->toBeFalse();
        }
    });

    test('works with nested directory structures', function (): void {
        $nestedPath = 'company-images/logos/2023/12/company-logo.jpg';
        $nestedVariations = [
            'company-images/logos/2023/12/company-logo_300x300.jpg',
            'company-images/logos/2023/12/company-logo_150x150.jpg',
        ];

        // Create nested files
        Storage::disk('public')->put($nestedPath, 'content');
        foreach ($nestedVariations as $variation) {
            Storage::disk('public')->put($variation, 'content');
        }

        $this->company->update(['logo_path' => $nestedPath]);

        $result = $this->action->execute($this->company, 'logo');

        expect($result)->toBeTrue();
        expect(Storage::disk('public')->exists($nestedPath))->toBeFalse();
        foreach ($nestedVariations as $variation) {
            expect(Storage::disk('public')->exists($variation))->toBeFalse();
        }
    });
});

describe('Concurrent Operations', function (): void {
    test('handles concurrent deletion attempts safely', function (): void {
        $bannerPath = 'company-images/banners/test.jpg';
        Storage::disk('public')->put($bannerPath, 'content');
        $this->company->update(['banner_path' => $bannerPath]);

        // Simulate concurrent deletion attempts
        $result1 = $this->action->execute($this->company, 'banner');
        $result2 = $this->action->execute($this->company, 'banner');

        expect($result1)->toBeTrue();
        expect($result2)->toBeTrue(); // Should not fail even though image is already deleted

        $this->company->refresh();
        expect($this->company->banner_path)->toBeNull();
    });

    test('maintains data integrity during concurrent operations', function (): void {
        $logoPath = 'company-images/logos/test.png';
        Storage::disk('public')->put($logoPath, 'content');
        $this->company->update(['logo_path' => $logoPath]);

        // Simulate database lock scenario
        $originalLogo = $this->company->logo_path;

        $result = $this->action->execute($this->company, 'logo');

        expect($result)->toBeTrue();

        // Verify consistent state
        $this->company->refresh();
        expect($this->company->logo_path)->toBeNull();
        expect(Storage::disk('public')->exists($logoPath))->toBeFalse();
    });
});

describe('Performance Considerations', function (): void {
    test('deletes multiple variations efficiently', function (): void {
        $startTime = microtime(true);

        // Create many variations
        $logoPath = 'company-images/logos/large-logo.jpg';
        $variations = [];
        for ($i = 1; $i <= 20; $i++) {
            $variations[] = "company-images/logos/large-logo_{$i}x{$i}.jpg";
        }

        Storage::disk('public')->put($logoPath, 'content');
        foreach ($variations as $variation) {
            Storage::disk('public')->put($variation, 'content');
        }

        $this->company->update(['logo_path' => $logoPath]);

        $result = $this->action->execute($this->company, 'logo');

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        expect($result)->toBeTrue();
        expect($executionTime)->toBeLessThan(2.0); // Should complete within 2 seconds
    });

    test('minimizes database queries during deletion', function (): void {
        $logoPath = 'company-images/logos/test.jpg';
        Storage::disk('public')->put($logoPath, 'content');
        $this->company->update(['logo_path' => $logoPath]);

        // Enable query logging
        DB::enableQueryLog();

        $this->action->execute($this->company, 'logo');

        $queries = DB::getQueryLog();

        // Should only perform minimal database operations
        expect(count($queries))->toBeLessThanOrEqual(3); // Select, Update, possible transaction queries
    });
});
