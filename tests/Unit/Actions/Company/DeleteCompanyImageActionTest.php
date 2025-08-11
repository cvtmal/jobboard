<?php

declare(strict_types=1);

use App\Actions\Company\DeleteCompanyImageAction;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->action = new DeleteCompanyImageAction();
    Storage::fake('public');
});

it('deletes logo image successfully', function () {
    $logoPath = 'company-images/logos/test-logo.png';
    Storage::disk('public')->put($logoPath, 'fake-image-content');

    $company = Company::factory()->create([
        'logo_path' => $logoPath,
        'logo_original_name' => 'original-logo.png',
        'logo_file_size' => 12345,
        'logo_mime_type' => 'image/png',
        'logo_dimensions' => ['width' => 400, 'height' => 400],
        'logo_uploaded_at' => now(),
    ]);

    $result = $this->action->execute($company, 'logo');

    expect($result)->toBeTrue();

    // Verify file was deleted from storage
    Storage::disk('public')->assertMissing($logoPath);

    // Verify database fields were cleared
    $company->refresh();
    expect($company->logo_path)->toBeNull();
    expect($company->logo_original_name)->toBeNull();
    expect($company->logo_file_size)->toBeNull();
    expect($company->logo_mime_type)->toBeNull();
    expect($company->logo_dimensions)->toBeNull();
    expect($company->logo_uploaded_at)->toBeNull();
});

it('deletes banner image successfully', function () {
    $bannerPath = 'company-images/banners/test-banner.jpg';
    Storage::disk('public')->put($bannerPath, 'fake-image-content');

    $company = Company::factory()->create([
        'banner_path' => $bannerPath,
        'banner_original_name' => 'original-banner.jpg',
        'banner_file_size' => 54321,
        'banner_mime_type' => 'image/jpeg',
        'banner_dimensions' => ['width' => 1200, 'height' => 400],
        'banner_uploaded_at' => now(),
    ]);

    $result = $this->action->execute($company, 'banner');

    expect($result)->toBeTrue();

    // Verify file was deleted from storage
    Storage::disk('public')->assertMissing($bannerPath);

    // Verify database fields were cleared
    $company->refresh();
    expect($company->banner_path)->toBeNull();
    expect($company->banner_original_name)->toBeNull();
    expect($company->banner_file_size)->toBeNull();
    expect($company->banner_mime_type)->toBeNull();
    expect($company->banner_dimensions)->toBeNull();
    expect($company->banner_uploaded_at)->toBeNull();
});

it('throws exception for invalid image type', function () {
    $company = Company::factory()->create();

    expect(fn () => $this->action->execute($company, 'invalid'))
        ->toThrow(InvalidArgumentException::class, 'Invalid image type: invalid');
});

it('returns true when no image exists to delete', function () {
    $company = Company::factory()->create([
        'logo_path' => null,
    ]);

    $result = $this->action->execute($company, 'logo');

    expect($result)->toBeTrue();
});

it('clears database fields even when file does not exist', function () {
    $company = Company::factory()->create([
        'logo_path' => 'non-existent/logo.png',
        'logo_original_name' => 'original-logo.png',
        'logo_file_size' => 12345,
        'logo_mime_type' => 'image/png',
        'logo_dimensions' => ['width' => 400, 'height' => 400],
        'logo_uploaded_at' => now(),
    ]);

    $result = $this->action->execute($company, 'logo');

    expect($result)->toBeTrue();

    // Verify database fields were cleared despite missing file
    $company->refresh();
    expect($company->logo_path)->toBeNull();
    expect($company->logo_original_name)->toBeNull();
    expect($company->logo_file_size)->toBeNull();
    expect($company->logo_mime_type)->toBeNull();
    expect($company->logo_dimensions)->toBeNull();
    expect($company->logo_uploaded_at)->toBeNull();
});

it('deletes image variations successfully', function () {
    $logoPath = 'company-images/logos/test-logo.png';
    $variationPaths = [
        'company-images/logos/test-logo_100x100.png',
        'company-images/logos/test-logo_200x200.png',
        'company-images/logos/test-logo_400x400.png',
    ];

    // Create main image and variations
    Storage::disk('public')->put($logoPath, 'main-image-content');
    foreach ($variationPaths as $variationPath) {
        Storage::disk('public')->put($variationPath, 'variation-content');
    }

    $company = Company::factory()->create([
        'logo_path' => $logoPath,
    ]);

    $result = $this->action->execute($company, 'logo');

    expect($result)->toBeTrue();

    // Verify main file and all variations were deleted
    Storage::disk('public')->assertMissing($logoPath);
    foreach ($variationPaths as $variationPath) {
        Storage::disk('public')->assertMissing($variationPath);
    }
});

it('handles variations with no extension', function () {
    $logoPath = 'company-images/logos/test-logo';
    $variationPath = 'company-images/logos/test-logo_100x100';

    Storage::disk('public')->put($logoPath, 'main-image-content');
    Storage::disk('public')->put($variationPath, 'variation-content');

    $company = Company::factory()->create([
        'logo_path' => $logoPath,
    ]);

    $result = $this->action->execute($company, 'logo');

    expect($result)->toBeTrue();

    // Verify both files were deleted
    Storage::disk('public')->assertMissing($logoPath);
    Storage::disk('public')->assertMissing($variationPath);
});

it('does not delete unrelated files in same directory', function () {
    $logoPath = 'company-images/logos/company-1-logo.png';
    $unrelatedFile = 'company-images/logos/company-2-logo.png';
    $notVariationFile = 'company-images/logos/company-1-logo-backup.png';

    Storage::disk('public')->put($logoPath, 'main-image-content');
    Storage::disk('public')->put($unrelatedFile, 'unrelated-content');
    Storage::disk('public')->put($notVariationFile, 'backup-content');

    $company = Company::factory()->create([
        'logo_path' => $logoPath,
    ]);

    $result = $this->action->execute($company, 'logo');

    expect($result)->toBeTrue();

    // Verify only the main file was deleted, not unrelated files
    Storage::disk('public')->assertMissing($logoPath);
    Storage::disk('public')->assertExists($unrelatedFile);
    Storage::disk('public')->assertExists($notVariationFile);
});

it('handles missing directory gracefully', function () {
    $company = Company::factory()->create([
        'logo_path' => 'non-existent-directory/logo.png',
    ]);

    $result = $this->action->execute($company, 'logo');

    expect($result)->toBeTrue();

    // Verify database fields were still cleared
    $company->refresh();
    expect($company->logo_path)->toBeNull();
});

it('returns false when storage deletion fails but database update succeeds', function () {
    $logoPath = 'company-images/logos/test-logo.png';

    $company = Company::factory()->create([
        'logo_path' => $logoPath,
        'logo_original_name' => 'original.png',
    ]);

    // Mock Storage to simulate deletion failure
    Storage::shouldReceive('disk')->with('public')->andReturnSelf();
    Storage::shouldReceive('exists')->with($logoPath)->andReturn(true);
    Storage::shouldReceive('delete')->with($logoPath)->andReturn(false);
    Storage::shouldReceive('exists')->with('company-images/logos')->andReturn(false);

    $result = $this->action->execute($company, 'logo');

    expect($result)->toBeFalse();

    // Verify database fields were still cleared
    $company->refresh();
    expect($company->logo_path)->toBeNull();
    expect($company->logo_original_name)->toBeNull();
});
