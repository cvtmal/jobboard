<?php

declare(strict_types=1);

use App\Actions\JobListing\UploadJobListingImageAction;
use App\Models\JobListing;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('upload job listing image action exists', function (): void {
    $action = new UploadJobListingImageAction();
    expect($action)->toBeInstanceOf(UploadJobListingImageAction::class);
});

test('upload job listing logo works correctly', function (): void {
    Storage::fake('public');

    $jobListing = JobListing::factory()->create();
    $action = new UploadJobListingImageAction();

    // Create a fake logo file
    $logoFile = UploadedFile::fake()->image('logo.jpg', 400, 400);

    $result = $action->execute($jobListing, $logoFile, 'logo');

    expect($result)->toBeTrue();

    $jobListing->refresh();

    expect($jobListing->logo_path)->not->toBeNull()
        ->and($jobListing->logo_original_name)->toBe('logo.jpg')
        ->and($jobListing->logo_file_size)->toBeGreaterThan(0)
        ->and($jobListing->logo_mime_type)->toBe('image/jpeg')
        ->and($jobListing->logo_dimensions)->toBeArray()
        ->and($jobListing->logo_uploaded_at)->not->toBeNull()
        ->and($jobListing->use_company_logo)->toBeFalse(); // Should be set to use custom logo

    // Verify file was stored
    Storage::disk('public')->assertExists($jobListing->logo_path);
});

test('upload job listing banner works correctly', function (): void {
    Storage::fake('public');

    $jobListing = JobListing::factory()->create();
    $action = new UploadJobListingImageAction();

    // Create a fake banner file
    $bannerFile = UploadedFile::fake()->image('banner.jpg', 1200, 400);

    $result = $action->execute($jobListing, $bannerFile, 'banner');

    expect($result)->toBeTrue();

    $jobListing->refresh();

    expect($jobListing->banner_path)->not->toBeNull()
        ->and($jobListing->banner_original_name)->toBe('banner.jpg')
        ->and($jobListing->banner_file_size)->toBeGreaterThan(0)
        ->and($jobListing->banner_mime_type)->toBe('image/jpeg')
        ->and($jobListing->banner_dimensions)->toBeArray()
        ->and($jobListing->banner_uploaded_at)->not->toBeNull()
        ->and($jobListing->use_company_banner)->toBeFalse(); // Should be set to use custom banner

    // Verify file was stored
    Storage::disk('public')->assertExists($jobListing->banner_path);
});

test('upload job listing image replaces existing image', function (): void {
    Storage::fake('public');

    $jobListing = JobListing::factory()->create([
        'logo_path' => 'job-listing-images/logos/old-logo.jpg',
        'use_company_logo' => false,
    ]);

    // Create old file
    Storage::disk('public')->put('job-listing-images/logos/old-logo.jpg', 'old logo content');

    $action = new UploadJobListingImageAction();

    // Upload new logo
    $newLogoFile = UploadedFile::fake()->image('new-logo.jpg', 400, 400);
    $result = $action->execute($jobListing, $newLogoFile, 'logo');

    expect($result)->toBeTrue();

    $jobListing->refresh();

    // Old file should be deleted
    Storage::disk('public')->assertMissing('job-listing-images/logos/old-logo.jpg');

    // New file should exist
    Storage::disk('public')->assertExists($jobListing->logo_path);

    expect($jobListing->logo_original_name)->toBe('new-logo.jpg');
});

test('upload job listing image throws exception for invalid type', function (): void {
    $jobListing = JobListing::factory()->create();
    $action = new UploadJobListingImageAction();
    $file = UploadedFile::fake()->image('test.jpg', 400, 400);

    expect(fn () => $action->execute($jobListing, $file, 'invalid'))
        ->toThrow(InvalidArgumentException::class, 'Invalid image type. Must be "logo" or "banner".');
});

test('upload job listing image generates unique filename', function (): void {
    Storage::fake('public');

    $jobListing1 = JobListing::factory()->create();
    $jobListing2 = JobListing::factory()->create(['id' => $jobListing1->id + 1]);

    $action = new UploadJobListingImageAction();

    $logoFile1 = UploadedFile::fake()->image('logo.jpg', 400, 400);
    $logoFile2 = UploadedFile::fake()->image('logo.jpg', 400, 400);

    $action->execute($jobListing1, $logoFile1, 'logo');
    $action->execute($jobListing2, $logoFile2, 'logo');

    $jobListing1->refresh();
    $jobListing2->refresh();

    // Filenames should be different despite same original name
    expect($jobListing1->logo_path)->not->toBe($jobListing2->logo_path);

    // Both files should include job listing ID
    expect($jobListing1->logo_path)->toContain("job-listing-{$jobListing1->id}")
        ->and($jobListing2->logo_path)->toContain("job-listing-{$jobListing2->id}");
});

test('upload job listing image processes and resizes images', function (): void {
    Storage::fake('public');

    $jobListing = JobListing::factory()->create();
    $action = new UploadJobListingImageAction();

    // Create a large logo that should be resized to 400x400
    $logoFile = UploadedFile::fake()->image('large-logo.jpg', 800, 800);

    $result = $action->execute($jobListing, $logoFile, 'logo');

    expect($result)->toBeTrue();

    $jobListing->refresh();

    // Dimensions should be set to processed size (400x400 for logos)
    expect($jobListing->logo_dimensions)->toBe(['width' => 400, 'height' => 400]);
});
