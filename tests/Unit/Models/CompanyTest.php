<?php

declare(strict_types=1);

use App\Models\Company;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

test('company is authenticatable', function (): void {
    $company = new Company();

    expect($company)->toBeInstanceOf(Authenticatable::class);
});

test('company factory creates valid instance', function (): void {
    $company = Company::factory()->create();

    expect($company)
        ->toBeInstanceOf(Company::class)
        ->name->not->toBeEmpty()
        ->email->toContain('@')
        ->password->not->toBeEmpty()
        ->email_verified_at->not->toBeNull();
});

test('company factory unverified state works', function (): void {
    $company = Company::factory()->unverified()->create();

    expect($company->email_verified_at)->toBeNull();
});

test('company password is hashed', function (): void {
    $password = 'password123';
    $company = Company::factory()->create([
        'password' => Hash::make($password),
    ]);

    expect(Hash::check($password, $company->password))->toBeTrue();
});

test('company factory generates all fields correctly', function (): void {
    $company = Company::factory()->create();

    // Test required fields
    expect($company->name)->toBeString()->not->toBeEmpty();
    expect($company->email)->toBeString()->toContain('@');
    expect($company->description_english)->toBeString();
    expect($company->active)->toBeBool();
    expect($company->blocked)->toBeBool();

    // Test nullable fields if they are present
    if ($company->address !== null) {
        expect($company->address)->toBeString();
    }

    if ($company->postcode !== null) {
        expect($company->postcode)->toBeString();
    }

    if ($company->city !== null) {
        expect($company->city)->toBeString();
    }

    if ($company->latitude !== null) {
        expect($company->latitude)->toBeFloat();
    }

    if ($company->longitude !== null) {
        expect($company->longitude)->toBeFloat();
    }

    if ($company->url !== null) {
        expect($company->url)->toBeString();
    }

    if ($company->size !== null) {
        expect($company->size)->toBeString();
    }

    if ($company->type !== null) {
        expect($company->type)->toBeString();
    }

    if ($company->description_german !== null) {
        expect($company->description_german)->toBeString();
    }

    if ($company->description_french !== null) {
        expect($company->description_french)->toBeString();
    }

    if ($company->description_italian !== null) {
        expect($company->description_italian)->toBeString();
    }

    if ($company->logo !== null) {
        expect($company->logo)->toBeString();
    }

    if ($company->cover !== null) {
        expect($company->cover)->toBeString();
    }

    if ($company->video !== null) {
        expect($company->video)->toBeString();
    }

    if ($company->newsletter !== null) {
        expect($company->newsletter)->toBeBool();
    }

    if ($company->internal_notes !== null) {
        expect($company->internal_notes)->toBeString();
    }
});

test('company boolean fields are properly cast', function (): void {
    $company = Company::factory()->create([
        'newsletter' => true,
        'active' => true,
        'blocked' => false,
    ]);

    expect($company->newsletter)->toBeBool()->toBeTrue()
        ->and($company->active)->toBeBool()->toBeTrue()
        ->and($company->blocked)->toBeBool()->toBeFalse();
});

test('company coordinates are properly cast', function (): void {
    $company = Company::factory()->create([
        'latitude' => '12.34567890',
        'longitude' => '98.76543210',
    ]);

    expect($company->latitude)->toBeFloat()->toEqual(12.34567890)
        ->and($company->longitude)->toBeFloat()->toEqual(98.76543210);
});

describe('Image Management', function (): void {
    beforeEach(function (): void {
        Storage::fake('public');
        $this->company = Company::factory()->create([
            'logo_path' => null,
            'banner_path' => null,
        ]);
    });

    test('hasLogo returns false when no logo is set', function (): void {
        expect($this->company->hasLogo())->toBeFalse();
    });

    test('hasLogo returns false when logo file does not exist in storage', function (): void {
        $this->company->update(['logo_path' => 'company-images/logos/missing.png']);

        expect($this->company->hasLogo())->toBeFalse();
    });

    test('hasLogo returns true when logo exists in storage', function (): void {
        $logoPath = 'company-images/logos/test-logo.png';
        Storage::disk('public')->put($logoPath, 'fake-logo-content');
        $this->company->update(['logo_path' => $logoPath]);

        expect($this->company->hasLogo())->toBeTrue();
    });

    test('hasBanner returns false when no banner is set', function (): void {
        expect($this->company->hasBanner())->toBeFalse();
    });

    test('hasBanner returns false when banner file does not exist in storage', function (): void {
        $this->company->update(['banner_path' => 'company-images/banners/missing.jpg']);

        expect($this->company->hasBanner())->toBeFalse();
    });

    test('hasBanner returns true when banner exists in storage', function (): void {
        $bannerPath = 'company-images/banners/test-banner.jpg';
        Storage::disk('public')->put($bannerPath, 'fake-banner-content');
        $this->company->update(['banner_path' => $bannerPath]);

        expect($this->company->hasBanner())->toBeTrue();
    });

    test('getLogoUrlAttribute returns null when no logo is set', function (): void {
        expect($this->company->logo_url)->toBeNull();
    });

    test('getLogoUrlAttribute returns correct URL when logo is set', function (): void {
        $logoPath = 'company-images/logos/test-logo.png';
        $this->company->update(['logo_path' => $logoPath]);

        $expectedUrl = Storage::disk('public')->url($logoPath);
        expect($this->company->logo_url)->toBe($expectedUrl);
    });

    test('getBannerUrlAttribute returns null when no banner is set', function (): void {
        expect($this->company->banner_url)->toBeNull();
    });

    test('getBannerUrlAttribute returns correct URL when banner is set', function (): void {
        $bannerPath = 'company-images/banners/test-banner.jpg';
        $this->company->update(['banner_path' => $bannerPath]);

        $expectedUrl = Storage::disk('public')->url($bannerPath);
        expect($this->company->banner_url)->toBe($expectedUrl);
    });

    test('deleteLogo removes file and clears database fields', function (): void {
        $logoPath = 'company-images/logos/test-logo.png';
        Storage::disk('public')->put($logoPath, 'fake-logo-content');
        $this->company->update([
            'logo_path' => $logoPath,
            'logo_original_name' => 'original-logo.png',
            'logo_file_size' => 12345,
            'logo_mime_type' => 'image/png',
            'logo_dimensions' => ['width' => 400, 'height' => 400],
            'logo_uploaded_at' => now(),
        ]);

        $result = $this->company->deleteLogo();

        expect($result)->toBeTrue()
            ->and(Storage::disk('public')->exists($logoPath))->toBeFalse();

        $this->company->refresh();
        expect($this->company->logo_path)->toBeNull()
            ->and($this->company->logo_original_name)->toBeNull()
            ->and($this->company->logo_file_size)->toBeNull()
            ->and($this->company->logo_mime_type)->toBeNull()
            ->and($this->company->logo_dimensions)->toBeNull()
            ->and($this->company->logo_uploaded_at)->toBeNull();
    });

    test('deleteLogo returns true when no logo exists', function (): void {
        $result = $this->company->deleteLogo();

        expect($result)->toBeTrue();
    });

    test('deleteBanner removes file and clears database fields', function (): void {
        $bannerPath = 'company-images/banners/test-banner.jpg';
        Storage::disk('public')->put($bannerPath, 'fake-banner-content');
        $this->company->update([
            'banner_path' => $bannerPath,
            'banner_original_name' => 'original-banner.jpg',
            'banner_file_size' => 54321,
            'banner_mime_type' => 'image/jpeg',
            'banner_dimensions' => ['width' => 1200, 'height' => 400],
            'banner_uploaded_at' => now(),
        ]);

        $result = $this->company->deleteBanner();

        expect($result)->toBeTrue()
            ->and(Storage::disk('public')->exists($bannerPath))->toBeFalse();

        $this->company->refresh();
        expect($this->company->banner_path)->toBeNull()
            ->and($this->company->banner_original_name)->toBeNull()
            ->and($this->company->banner_file_size)->toBeNull()
            ->and($this->company->banner_mime_type)->toBeNull()
            ->and($this->company->banner_dimensions)->toBeNull()
            ->and($this->company->banner_uploaded_at)->toBeNull();
    });

    test('deleteBanner returns true when no banner exists', function (): void {
        $result = $this->company->deleteBanner();

        expect($result)->toBeTrue();
    });

    test('getLogoFileSizeFormattedAttribute returns null when no file size', function (): void {
        expect($this->company->logo_file_size_formatted)->toBeNull();
    });

    test('getLogoFileSizeFormattedAttribute formats file size correctly', function (): void {
        $this->company->update(['logo_file_size' => 1024]); // 1KB
        expect($this->company->logo_file_size_formatted)->toBe('1 KB');

        $this->company->update(['logo_file_size' => 1024 * 1024]); // 1MB
        expect($this->company->logo_file_size_formatted)->toBe('1 MB');

        $this->company->update(['logo_file_size' => 1024 * 1024 * 1024]); // 1GB
        expect($this->company->logo_file_size_formatted)->toBe('1 GB');

        $this->company->update(['logo_file_size' => 1536]); // 1.5KB
        expect($this->company->logo_file_size_formatted)->toBe('1.5 KB');
    });

    test('getBannerFileSizeFormattedAttribute returns null when no file size', function (): void {
        expect($this->company->banner_file_size_formatted)->toBeNull();
    });

    test('getBannerFileSizeFormattedAttribute formats file size correctly', function (): void {
        $this->company->update(['banner_file_size' => 2048]); // 2KB
        expect($this->company->banner_file_size_formatted)->toBe('2 KB');

        $this->company->update(['banner_file_size' => 2 * 1024 * 1024]); // 2MB
        expect($this->company->banner_file_size_formatted)->toBe('2 MB');

        $this->company->update(['banner_file_size' => 768]); // 768B
        expect($this->company->banner_file_size_formatted)->toBe('768 B');
    });

    test('image metadata fields are properly cast', function (): void {
        $uploadTime = now();
        $dimensions = ['width' => 400, 'height' => 400];

        $this->company->update([
            'logo_dimensions' => $dimensions,
            'logo_uploaded_at' => $uploadTime,
            'banner_dimensions' => ['width' => 1200, 'height' => 400],
            'banner_uploaded_at' => $uploadTime,
        ]);

        $this->company->refresh();

        expect($this->company->logo_dimensions)->toBeArray()
            ->toEqual($dimensions)
            ->and($this->company->logo_uploaded_at)->toBeInstanceOf(CarbonImmutable::class)
            ->and($this->company->banner_dimensions)->toBeArray()
            ->and($this->company->banner_uploaded_at)->toBeInstanceOf(CarbonImmutable::class);
    });

    test('handles storage disk failure in deleteLogo gracefully', function (): void {
        $logoPath = 'company-images/logos/test-logo.png';
        $this->company->update(['logo_path' => $logoPath]);

        // Mock storage failure
        Storage::shouldReceive('disk->delete')
            ->once()
            ->with($logoPath)
            ->andReturn(false);

        $result = $this->company->deleteLogo();

        expect($result)->toBeFalse();

        // Database should not be updated when file deletion fails
        $this->company->refresh();
        expect($this->company->logo_path)->toBe($logoPath);
    });

    test('handles storage disk failure in deleteBanner gracefully', function (): void {
        $bannerPath = 'company-images/banners/test-banner.jpg';
        $this->company->update(['banner_path' => $bannerPath]);

        // Mock storage failure
        Storage::shouldReceive('disk->delete')
            ->once()
            ->with($bannerPath)
            ->andReturn(false);

        $result = $this->company->deleteBanner();

        expect($result)->toBeFalse();

        // Database should not be updated when file deletion fails
        $this->company->refresh();
        expect($this->company->banner_path)->toBe($bannerPath);
    });

    test('formatFileSize method handles edge cases', function (): void {
        // Test null when size is 0
        $this->company->update(['logo_file_size' => 0]);
        expect($this->company->logo_file_size_formatted)->toBeNull();

        // Test very small files
        $this->company->update(['logo_file_size' => 1]);
        expect($this->company->logo_file_size_formatted)->toBe('1 B');

        $this->company->update(['logo_file_size' => 1023]);
        expect($this->company->logo_file_size_formatted)->toBe('1023 B');

        // Test exactly 1KB
        $this->company->update(['logo_file_size' => 1024]);
        expect($this->company->logo_file_size_formatted)->toBe('1 KB');

        // Test very large file
        $this->company->update(['logo_file_size' => (int) (1024 * 1024 * 1024 * 2.5)]); // 2.5GB
        expect($this->company->logo_file_size_formatted)->toBe('2.5 GB');
    });
});
