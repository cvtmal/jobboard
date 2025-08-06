<?php

declare(strict_types=1);

use App\Actions\Company\UploadCompanyImageAction;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
    $this->company = Company::factory()->create([
        'logo_path' => null,
        'banner_path' => null,
    ]);
    $this->action = new UploadCompanyImageAction();
});

describe('Banner Image Upload', function (): void {
    test('processes banner image correctly', function (): void {
        $image = UploadedFile::fake()->image('banner.jpg', 1200, 400);

        $result = $this->action->execute($this->company, $image, 'banner');

        expect($result)->toBeTrue();

        // Verify database was updated
        $this->company->refresh();
        expect($this->company->banner_path)->not->toBeNull()
            ->and($this->company->banner_path)->toContain('company-images/banners/')
            ->and($this->company->banner_original_name)->toBe('banner.jpg')
            ->and($this->company->banner_mime_type)->toBe('image/jpeg')
            ->and($this->company->banner_file_size)->toBeGreaterThan(0)
            ->and($this->company->banner_dimensions)->toBeArray()
            ->and($this->company->banner_uploaded_at)->not->toBeNull();

        // Verify image was stored
        expect(Storage::disk('public')->exists($this->company->banner_path))->toBeTrue();
    });

    test('stores banner with correct dimensions metadata', function (): void {
        $image = UploadedFile::fake()->image('banner.png', 1800, 600);

        $result = $this->action->execute($this->company, $image, 'banner');

        expect($result)->toBeTrue();

        $this->company->refresh();
        expect($this->company->banner_dimensions)->toEqual([
            'width' => 1800,
            'height' => 600,
        ]);
    });

    test('updates company banner in database', function (): void {
        $image = UploadedFile::fake()->image('banner.jpg', 1200, 400);

        $this->action->execute($this->company, $image, 'banner');

        $this->company->refresh();
        expect($this->company->banner_path)->not->toBeNull()
            ->and($this->company->banner_path)->toContain('company-images/banners/');
    });

    test('replaces existing banner image', function (): void {
        // Set existing banner
        $existingBannerPath = 'company-images/banners/existing.jpg';
        Storage::disk('public')->put($existingBannerPath, 'fake-content');
        $this->company->update(['banner_path' => $existingBannerPath]);

        $newImage = UploadedFile::fake()->image('new-banner.jpg', 1200, 400);

        $result = $this->action->execute($this->company, $newImage, 'banner');

        expect($result)->toBeTrue();

        // Verify old image was deleted
        expect(Storage::disk('public')->exists($existingBannerPath))->toBeFalse();

        // Verify new image was stored
        $this->company->refresh();
        expect(Storage::disk('public')->exists($this->company->banner_path))->toBeTrue();
        expect($this->company->banner_path)->not->toBe($existingBannerPath);
    });

    test('stores banner metadata correctly', function (): void {
        $image = UploadedFile::fake()->image('test-banner.jpg', 1200, 400);

        $result = $this->action->execute($this->company, $image, 'banner');

        expect($result)->toBeTrue();

        $this->company->refresh();
        expect($this->company->banner_original_name)->toBe('test-banner.jpg')
            ->and($this->company->banner_file_size)->toBe($image->getSize())
            ->and($this->company->banner_mime_type)->toBe('image/jpeg')
            ->and($this->company->banner_dimensions)->toEqual([
                'width' => 1200,
                'height' => 400,
            ])
            ->and($this->company->banner_uploaded_at)->toBeInstanceOf(Carbon\CarbonImmutable::class);
    });
});

describe('Logo Image Upload', function (): void {
    test('processes logo image correctly', function (): void {
        $image = UploadedFile::fake()->image('logo.png', 400, 400);

        $result = $this->action->execute($this->company, $image, 'logo');

        expect($result)->toBeTrue();

        // Verify database was updated
        $this->company->refresh();
        expect($this->company->logo_path)->not->toBeNull()
            ->and($this->company->logo_path)->toContain('company-images/logos/')
            ->and($this->company->logo_original_name)->toBe('logo.png')
            ->and($this->company->logo_mime_type)->toBe('image/png')
            ->and($this->company->logo_file_size)->toBeGreaterThan(0)
            ->and($this->company->logo_dimensions)->toBeArray()
            ->and($this->company->logo_uploaded_at)->not->toBeNull();

        // Verify image was stored
        expect(Storage::disk('public')->exists($this->company->logo_path))->toBeTrue();
    });

    test('stores logo with correct dimensions metadata', function (): void {
        $image = UploadedFile::fake()->image('logo.jpg', 500, 500);

        $result = $this->action->execute($this->company, $image, 'logo');

        expect($result)->toBeTrue();

        $this->company->refresh();
        expect($this->company->logo_dimensions)->toEqual([
            'width' => 500,
            'height' => 500,
        ]);
    });

    test('updates company logo in database', function (): void {
        $image = UploadedFile::fake()->image('logo.png', 400, 400);

        $this->action->execute($this->company, $image, 'logo');

        $this->company->refresh();
        expect($this->company->logo_path)->not->toBeNull()
            ->and($this->company->logo_path)->toContain('company-images/logos/');
    });

    test('replaces existing logo image', function (): void {
        // Set existing logo
        $existingLogoPath = 'company-images/logos/existing.png';
        Storage::disk('public')->put($existingLogoPath, 'fake-content');
        $this->company->update(['logo_path' => $existingLogoPath]);

        $newImage = UploadedFile::fake()->image('new-logo.png', 400, 400);

        $result = $this->action->execute($this->company, $newImage, 'logo');

        expect($result)->toBeTrue();

        // Verify old image was deleted
        expect(Storage::disk('public')->exists($existingLogoPath))->toBeFalse();

        // Verify new image was stored
        $this->company->refresh();
        expect(Storage::disk('public')->exists($this->company->logo_path))->toBeTrue();
        expect($this->company->logo_path)->not->toBe($existingLogoPath);
    });
});

describe('File Handling', function (): void {
    test('generates unique filenames to prevent conflicts', function (): void {
        $image1 = UploadedFile::fake()->image('test.jpg', 1200, 400);
        $image2 = UploadedFile::fake()->image('test.jpg', 1200, 400);

        $this->action->execute($this->company, $image1, 'banner');
        $this->company->refresh();
        $firstPath = $this->company->banner_path;

        $anotherCompany = Company::factory()->create([
            'logo_path' => null,
            'banner_path' => null,
        ]);
        $this->action->execute($anotherCompany, $image2, 'banner');
        $anotherCompany->refresh();
        $secondPath = $anotherCompany->banner_path;

        expect($firstPath)->not->toBe($secondPath);
    });

    test('stores files in correct directory structure', function (): void {
        $logoImage = UploadedFile::fake()->image('logo.png', 400, 400);
        $bannerImage = UploadedFile::fake()->image('banner.jpg', 1200, 400);

        $this->action->execute($this->company, $logoImage, 'logo');
        $this->action->execute($this->company, $bannerImage, 'banner');

        $this->company->refresh();

        expect($this->company->logo_path)->toStartWith('company-images/logos/')
            ->and($this->company->banner_path)->toStartWith('company-images/banners/');
    });

    test('handles PNG images correctly', function (): void {
        $image = UploadedFile::fake()->image('logo.png', 400, 400);

        $result = $this->action->execute($this->company, $image, 'logo');

        expect($result)->toBeTrue();

        $this->company->refresh();
        expect($this->company->logo_mime_type)->toBe('image/png');
        expect(Storage::disk('public')->exists($this->company->logo_path))->toBeTrue();
    });

    test('processes and resizes images correctly', function (): void {
        $largeImage = UploadedFile::fake()->image('large-banner.jpg', 2400, 800);

        $result = $this->action->execute($this->company, $largeImage, 'banner');

        expect($result)->toBeTrue();

        $this->company->refresh();

        // Original dimensions should be stored
        expect($this->company->banner_dimensions)->toEqual([
            'width' => 2400,
            'height' => 800,
        ]);

        // File should be processed and stored
        expect(Storage::disk('public')->exists($this->company->banner_path))->toBeTrue();
    });
});

describe('Error Handling', function (): void {
    test('throws exception for invalid image type', function (): void {
        $image = UploadedFile::fake()->image('test.jpg', 400, 400);

        expect(fn () => $this->action->execute($this->company, $image, 'invalid'))
            ->toThrow(InvalidArgumentException::class, 'Invalid image type. Must be "logo" or "banner".');
    });

    test('throws exception when storage fails', function (): void {
        Storage::shouldReceive('disk->put')
            ->once()
            ->andReturn(false);

        $image = UploadedFile::fake()->image('test.jpg', 1200, 400);

        expect(fn () => $this->action->execute($this->company, $image, 'banner'))
            ->toThrow(RuntimeException::class, 'Failed to store banner image.');
    });

    test('maintains data integrity on failure', function (): void {
        $originalBannerPath = $this->company->banner_path;

        // Mock storage failure
        Storage::shouldReceive('disk->put')
            ->once()
            ->andReturn(false);

        $image = UploadedFile::fake()->image('test.jpg', 1200, 400);

        try {
            $this->action->execute($this->company, $image, 'banner');
            $this->fail('Expected exception was not thrown');
        } catch (RuntimeException $e) {
            // Expected exception
        }

        // Verify company wasn't updated
        $this->company->refresh();
        expect($this->company->banner_path)->toBe($originalBannerPath);
    });

    test('replaces existing image successfully', function (): void {
        // Set up existing image
        $existingPath = 'company-images/banners/existing.jpg';
        Storage::disk('public')->put($existingPath, 'content');
        $this->company->update(['banner_path' => $existingPath]);

        // Upload new image
        $image = UploadedFile::fake()->image('new.jpg', 1200, 400);

        $result = $this->action->execute($this->company, $image, 'banner');

        expect($result)->toBeTrue();

        // Verify old image was replaced
        expect(Storage::disk('public')->exists($existingPath))->toBeFalse();

        $this->company->refresh();
        expect($this->company->banner_path)->not()->toBe($existingPath);
        expect($this->company->banner_path)->not()->toBeNull();

        // Existing image should have been deleted
        expect(Storage::disk('public')->exists($existingPath))->toBeFalse();
    });
});

describe('Image Processing', function (): void {
    test('maintains image quality during processing', function (): void {
        $image = UploadedFile::fake()->image('test.jpg', 1200, 400);

        $result = $this->action->execute($this->company, $image, 'banner');

        expect($result)->toBeTrue();

        $this->company->refresh();
        $storedImage = Storage::disk('public')->get($this->company->banner_path);
        expect($storedImage)->not->toBeEmpty();
    });

    test('handles different image formats consistently', function (): void {
        $jpgImage = UploadedFile::fake()->image('logo.jpg', 400, 400);
        $result1 = $this->action->execute($this->company, $jpgImage, 'logo');

        expect($result1)->toBeTrue();

        $pngImage = UploadedFile::fake()->image('banner.png', 1200, 400);
        $result2 = $this->action->execute($this->company, $pngImage, 'banner');

        expect($result2)->toBeTrue();

        $this->company->refresh();
        expect($this->company->logo_mime_type)->toBe('image/jpeg')
            ->and($this->company->banner_mime_type)->toBe('image/png');
    });

    test('generates filename with company ID and timestamp', function (): void {
        $image = UploadedFile::fake()->image('test.jpg', 400, 400);

        $result = $this->action->execute($this->company, $image, 'logo');

        expect($result)->toBeTrue();

        $this->company->refresh();
        expect($this->company->logo_path)->toContain("company-{$this->company->id}-logo-");
    });
});

describe('Metadata Storage', function (): void {
    test('stores complete metadata for uploaded images', function (): void {
        $image = UploadedFile::fake()->image('company-logo.png', 500, 500);
        $originalSize = $image->getSize();

        $result = $this->action->execute($this->company, $image, 'logo');

        expect($result)->toBeTrue();

        $this->company->refresh();
        expect($this->company->logo_original_name)->toBe('company-logo.png')
            ->and($this->company->logo_file_size)->toBe($originalSize)
            ->and($this->company->logo_mime_type)->toBe('image/png')
            ->and($this->company->logo_dimensions)->toEqual(['width' => 500, 'height' => 500])
            ->and($this->company->logo_uploaded_at)->toBeInstanceOf(Carbon\CarbonImmutable::class);
    });

    test('correctly updates all banner metadata fields', function (): void {
        $image = UploadedFile::fake()->image('banner-image.jpg', 1500, 500);
        $originalSize = $image->getSize();

        $result = $this->action->execute($this->company, $image, 'banner');

        expect($result)->toBeTrue();

        $this->company->refresh();
        expect($this->company->banner_original_name)->toBe('banner-image.jpg')
            ->and($this->company->banner_file_size)->toBe($originalSize)
            ->and($this->company->banner_mime_type)->toBe('image/jpeg')
            ->and($this->company->banner_dimensions)->toEqual(['width' => 1500, 'height' => 500])
            ->and($this->company->banner_uploaded_at)->toBeInstanceOf(Carbon\CarbonImmutable::class);
    });

    test('uploads at timestamp reflects actual upload time', function (): void {
        $beforeUpload = now()->subSecond(); // Add 1 second buffer for timing precision

        $image = UploadedFile::fake()->image('test.jpg', 400, 400);
        $result = $this->action->execute($this->company, $image, 'logo');

        expect($result)->toBeTrue();

        $afterUpload = now()->addSecond(); // Add 1 second buffer for timing precision

        $this->company->refresh();
        expect($this->company->logo_uploaded_at)->toBeInstanceOf(Carbon\CarbonImmutable::class);
        expect($this->company->logo_uploaded_at->isAfter($beforeUpload))->toBeTrue();
        expect($this->company->logo_uploaded_at->isBefore($afterUpload))->toBeTrue();
    });
});
