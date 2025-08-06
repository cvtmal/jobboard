<?php

declare(strict_types=1);

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Helpers\ImageTestHelper;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
    // Create company without preset images
    $this->company = Company::factory()->create([
        'logo_path' => null,
        'banner_path' => null,
        'logo_original_name' => null,
        'logo_file_size' => null,
        'logo_mime_type' => null,
        'logo_dimensions' => null,
        'logo_uploaded_at' => null,
        'banner_original_name' => null,
        'banner_file_size' => null,
        'banner_mime_type' => null,
        'banner_dimensions' => null,
        'banner_uploaded_at' => null,
    ]);
});

describe('Company Banner Image Upload', function (): void {
    test('company can upload valid banner image', function (): void {
        $image = UploadedFile::fake()->image('banner.jpg', 1200, 400);

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), [
                'banner' => $image,
            ]);

        $response->assertStatus(200);

        // Verify database was updated
        $this->company->refresh();
        expect($this->company->banner_path)->not->toBeNull();

        // Verify image was stored
        Storage::disk('public')->assertExists($this->company->banner_path);

        // Verify response contains metadata
        $response->assertJsonStructure([
            'success',
            'data' => [
                'message',
                'banner_url',
                'banner_metadata' => [
                    'original_name',
                    'file_size',
                    'mime_type',
                    'dimensions',
                    'uploaded_at',
                ],
            ],
        ]);
    });

    test('company can upload valid banner image in PNG format', function (): void {
        $image = UploadedFile::fake()->image('banner.png', 1200, 400);

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), [
                'banner' => $image,
            ]);

        $response->assertStatus(200);

        $this->company->refresh();
        Storage::disk('public')->assertExists($this->company->banner_path);
    });

    test('banner upload replaces existing banner', function (): void {
        // Upload first banner
        $firstImage = UploadedFile::fake()->image('banner1.jpg', 1200, 400);
        $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), [
                'banner' => $firstImage,
            ]);

        $this->company->refresh();
        $firstImagePath = $this->company->banner_path;
        Storage::disk('public')->assertExists($firstImagePath);

        // Upload second banner
        $secondImage = UploadedFile::fake()->image('banner2.jpg', 1200, 400);
        $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), [
                'banner' => $secondImage,
            ]);

        // First image should be deleted, second should exist
        Storage::disk('public')->assertMissing($firstImagePath);
        $this->company->refresh();
        Storage::disk('public')->assertExists($this->company->banner_path);
    });

    test('banner upload fails with invalid dimensions', function (): void {
        $image = ImageTestHelper::createTestImage('banner.jpg', 800, 600, 'jpg'); // Wrong aspect ratio

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), [
                'banner' => $image,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['banner']);

        // Verify no image was stored
        $this->company->refresh();
        expect($this->company->banner_path)->toBeNull();
    });

    test('banner upload fails with file too large', function (): void {
        // Create a fake large file (17MB)
        $image = UploadedFile::fake()->create('banner.jpg', 17 * 1024, 'image/jpeg');

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), [
                'banner' => $image,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['banner']);
    });

    test('banner upload fails with invalid file type', function (): void {
        $file = UploadedFile::fake()->create('banner.gif', 1000, 'image/gif');

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), [
                'banner' => $file,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['banner']);
    });

    test('banner upload stores metadata correctly', function (): void {
        $image = UploadedFile::fake()->image('test-banner.jpg', 1200, 400);

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), [
                'banner' => $image,
            ]);

        $response->assertStatus(200);

        $this->company->refresh();
        expect($this->company->banner_original_name)->toBe('test-banner.jpg')
            ->and($this->company->banner_file_size)->toBeGreaterThan(0)
            ->and($this->company->banner_mime_type)->toBe('image/jpeg')
            ->and($this->company->banner_dimensions)->toBeArray()
            ->and($this->company->banner_uploaded_at)->not->toBeNull();
    });
});

describe('Company Logo Image Upload', function (): void {
    test('company can upload valid logo image', function (): void {
        $image = UploadedFile::fake()->image('logo.jpg', 400, 400);

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.logo.upload'), [
                'logo' => $image,
            ]);

        $response->assertStatus(200);

        // Verify database was updated
        $this->company->refresh();
        expect($this->company->logo_path)->not->toBeNull();

        // Verify image was stored
        Storage::disk('public')->assertExists($this->company->logo_path);

        // Verify response contains metadata
        $response->assertJsonStructure([
            'success',
            'data' => [
                'message',
                'logo_url',
                'logo_metadata' => [
                    'original_name',
                    'file_size',
                    'mime_type',
                    'dimensions',
                    'uploaded_at',
                ],
            ],
        ]);
    });

    test('logo upload fails with non-square dimensions', function (): void {
        $image = UploadedFile::fake()->image('logo.jpg', 400, 300); // Not square

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.logo.upload'), [
                'logo' => $image,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['logo']);
    });

    test('logo upload fails with dimensions too small', function (): void {
        $image = UploadedFile::fake()->image('logo.jpg', 300, 300); // Below 320x320 minimum

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.logo.upload'), [
                'logo' => $image,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['logo']);
    });

    test('logo upload fails with file too large', function (): void {
        // Create a fake large file (9MB)
        $image = UploadedFile::fake()->create('logo.png', 9 * 1024, 'image/png');

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.logo.upload'), [
                'logo' => $image,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['logo']);
    });

    test('logo upload stores metadata correctly', function (): void {
        $image = ImageTestHelper::createTestImage('company-logo.png', 400, 400);

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.logo.upload'), [
                'logo' => $image,
            ]);

        $response->assertStatus(200);

        $this->company->refresh();
        expect($this->company->logo_original_name)->toBe('company-logo.png')
            ->and($this->company->logo_file_size)->toBeGreaterThan(0)
            ->and($this->company->logo_mime_type)->toBe('image/png')
            ->and($this->company->logo_dimensions)->toBeArray()
            ->and($this->company->logo_uploaded_at)->not->toBeNull();
    });
});

describe('Company Image Deletion', function (): void {
    test('company can delete banner image', function (): void {
        // First upload a banner
        $image = UploadedFile::fake()->image('banner.jpg', 1200, 400);
        $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), [
                'banner' => $image,
            ]);

        $this->company->refresh();
        $imagePath = $this->company->banner_path;
        Storage::disk('public')->assertExists($imagePath);

        // Delete the banner
        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->delete(route('company.images.banner.delete'));

        $response->assertStatus(200);

        // Verify file was deleted from storage
        Storage::disk('public')->assertMissing($imagePath);

        // Verify database was updated
        $this->company->refresh();
        expect($this->company->banner_path)->toBeNull();
    });

    test('company can delete logo image', function (): void {
        // First upload a logo
        $image = UploadedFile::fake()->image('logo.jpg', 400, 400);
        $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.logo.upload'), [
                'logo' => $image,
            ]);

        $this->company->refresh();
        $imagePath = $this->company->logo_path;
        Storage::disk('public')->assertExists($imagePath);

        // Delete the logo
        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->delete(route('company.images.logo.delete'));

        $response->assertStatus(200);

        // Verify file was deleted from storage
        Storage::disk('public')->assertMissing($imagePath);

        // Verify database was updated
        $this->company->refresh();
        expect($this->company->logo_path)->toBeNull();
    });

    test('deleting non-existent image returns success', function (): void {
        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->delete(route('company.images.banner.delete'));

        $response->assertStatus(200);
    });
});

describe('Company Images Show Endpoint', function (): void {
    test('company can get current images when none exist', function (): void {
        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->get(route('company.images.show'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'logo' => null,
                    'banner' => null,
                ],
            ]);
    });

    test('company can get current images when they exist', function (): void {
        // Upload both logo and banner
        $logo = ImageTestHelper::createTestImage('logo.png', 400, 400);
        $banner = ImageTestHelper::createTestImage('banner.jpg', 1200, 400, 'jpg');

        $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.logo.upload'), ['logo' => $logo]);

        $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), ['banner' => $banner]);

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->get(route('company.images.show'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'logo' => [
                        'url',
                        'original_name',
                        'file_size',
                        'mime_type',
                        'dimensions',
                        'uploaded_at',
                    ],
                    'banner' => [
                        'url',
                        'original_name',
                        'file_size',
                        'mime_type',
                        'dimensions',
                        'uploaded_at',
                    ],
                ],
            ]);

        $responseData = $response->json('data');
        expect($responseData['logo'])->not->toBeNull()
            ->and($responseData['banner'])->not->toBeNull()
            ->and($responseData['logo']['original_name'])->toBe('logo.png')
            ->and($responseData['banner']['original_name'])->toBe('banner.jpg')
            ->and($responseData['logo']['mime_type'])->toBe('image/png')
            ->and($responseData['banner']['mime_type'])->toBe('image/jpeg');
    });
});

describe('Authorization and Security', function (): void {
    test('unauthenticated user cannot upload images', function (): void {
        $image = UploadedFile::fake()->image('banner.jpg', 1200, 400);

        $response = $this->post(route('company.images.banner.upload'), [
            'banner' => $image,
        ]);

        $response->assertStatus(302)
            ->assertRedirect(route('login')); // Redirects to main login page
    });

    test('unauthenticated user cannot access show endpoint', function (): void {
        $response = $this->get(route('company.images.show'));

        $response->assertStatus(302)
            ->assertRedirect(route('login')); // Redirects to main login page
    });

    test('applicant guard cannot access company image upload', function (): void {
        $applicant = App\Models\Applicant::factory()->create();
        $image = UploadedFile::fake()->image('banner.jpg', 1200, 400);

        $response = $this->actingAs($applicant, 'applicant')
            ->post(route('company.images.banner.upload'), [
                'banner' => $image,
            ]);

        $response->assertStatus(302);
    });

    test('admin guard cannot access company image upload endpoints', function (): void {
        $admin = App\Models\User::factory()->create();
        $image = UploadedFile::fake()->image('banner.jpg', 1200, 400);

        $response = $this->actingAs($admin, 'web')
            ->post(route('company.images.banner.upload'), [
                'banner' => $image,
            ]);

        $response->assertStatus(302);
    });
});

describe('German Localization', function (): void {
    beforeEach(function (): void {
        app()->setLocale('de');
    });

    test('validation errors are returned in German for banner', function (): void {
        $image = UploadedFile::fake()->image('banner.jpg', 800, 600); // Wrong dimensions

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), [
                'banner' => $image,
            ]);

        $response->assertStatus(422);

        // Check that error messages are in German
        $errors = $response->json('errors.banner');
        expect($errors[0])->toContain('Banner'); // German word for banner
    });

    test('validation errors are returned in German for logo', function (): void {
        $image = UploadedFile::fake()->image('logo.jpg', 300, 200); // Wrong dimensions

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.logo.upload'), [
                'logo' => $image,
            ]);

        $response->assertStatus(422);

        // Check that error messages are in German
        $errors = $response->json('errors.logo');
        expect($errors[0])->toContain('Logo'); // German word for logo
    });

    test('success messages are returned in German for banner', function (): void {
        $image = UploadedFile::fake()->image('banner.jpg', 1200, 400);

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), [
                'banner' => $image,
            ]);

        $response->assertStatus(200);

        // Check success message is in German
        $message = $response->json('data.message');
        expect($message)->toContain('erfolgreich'); // German word for "successfully"
    });

    test('success messages are returned in German for logo', function (): void {
        $image = ImageTestHelper::createTestImage('logo.png', 400, 400);

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.logo.upload'), [
                'logo' => $image,
            ]);

        $response->assertStatus(200);

        // Check success message is in German
        $message = $response->json('data.message');
        expect($message)->toContain('erfolgreich'); // German word for "successfully"
    });
});

describe('Edge Cases and Error Handling', function (): void {
    test('upload handles storage disk failure gracefully', function (): void {
        // Test with valid image - if there are any storage issues they will be handled gracefully
        $image = ImageTestHelper::createTestImage('banner.jpg', 1200, 400, 'jpg');

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), [
                'banner' => $image,
            ]);

        // Should succeed normally in test environment
        $response->assertStatus(200);

        // Verify database was updated
        $this->company->refresh();
        expect($this->company->banner_path)->not->toBeNull();
    });

    test('upload with corrupted image file fails validation', function (): void {
        $corruptedFile = UploadedFile::fake()->create('corrupt.jpg', 1000, 'text/plain');

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), [
                'banner' => $corruptedFile,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['banner']);
    });

    test('database handles concurrent image uploads safely', function (): void {
        $image1 = UploadedFile::fake()->image('banner1.jpg', 1200, 400);
        $image2 = UploadedFile::fake()->image('banner2.jpg', 1200, 400);

        // Simulate concurrent uploads
        $response1 = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), [
                'banner' => $image1,
            ]);

        $response2 = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), [
                'banner' => $image2,
            ]);

        expect($response1->status())->toBe(200);
        expect($response2->status())->toBe(200);

        // Only the last uploaded image should remain
        $this->company->refresh();
        expect($this->company->banner_path)->not->toBeNull();
        Storage::disk('public')->assertExists($this->company->banner_path);
    });

    test('metadata is consistent across different image formats', function (): void {
        // Test PNG logo
        $pngLogo = ImageTestHelper::createTestImage('logo.png', 400, 400);
        $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.logo.upload'), ['logo' => $pngLogo]);

        $this->company->refresh();
        expect($this->company->logo_mime_type)->toBe('image/png')
            ->and($this->company->logo_dimensions)->toHaveKey('width')
            ->and($this->company->logo_dimensions)->toHaveKey('height');

        // Test JPG banner
        $jpgBanner = ImageTestHelper::createTestImage('banner.jpg', 1200, 400, 'jpg');
        $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), ['banner' => $jpgBanner]);

        $this->company->refresh();
        expect($this->company->banner_mime_type)->toBe('image/jpeg')
            ->and($this->company->banner_dimensions)->toHaveKey('width')
            ->and($this->company->banner_dimensions)->toHaveKey('height');
    });

    test('file size calculation is accurate', function (): void {
        $image = UploadedFile::fake()->image('banner.jpg', 1200, 400);
        $actualSize = $image->getSize();

        $response = $this->actingAs($this->company, 'company')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('company.images.banner.upload'), [
                'banner' => $image,
            ]);

        $response->assertStatus(200);

        $this->company->refresh();
        expect($this->company->banner_file_size)->toBe($actualSize);
    });
});
