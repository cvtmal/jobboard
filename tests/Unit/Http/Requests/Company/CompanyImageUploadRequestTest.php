<?php

declare(strict_types=1);

use App\Http\Requests\Company\CompanyBannerUploadRequest;
use App\Http\Requests\Company\CompanyLogoUploadRequest;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\Helpers\ImageTestHelper;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->company = Company::factory()->create();
});

describe('CompanyLogoUploadRequest', function (): void {
    test('authorizes company user', function (): void {
        $request = new CompanyLogoUploadRequest();
        $request->setUserResolver(fn () => $this->company);

        expect($request->authorize())->toBeTrue();
    });

    test('does not authorize non-company user', function (): void {
        $request = new CompanyLogoUploadRequest();
        $request->setUserResolver(fn () => null);

        expect($request->authorize())->toBeFalse();
    });

    test('validates valid PNG logo image', function (): void {
        $request = new CompanyLogoUploadRequest();
        $data = [
            'logo' => ImageTestHelper::createTestImage('logo.png', 320, 320),
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->passes())->toBeTrue();
    });

    test('validates valid JPG logo image', function (): void {
        $request = new CompanyLogoUploadRequest();
        $data = [
            'logo' => ImageTestHelper::createTestImage('logo.jpg', 400, 400, 'jpg'),
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->passes())->toBeTrue();
    });

    test('validates valid JPEG logo image', function (): void {
        $request = new CompanyLogoUploadRequest();
        $data = [
            'logo' => ImageTestHelper::createTestImage('logo.jpeg', 500, 500, 'jpeg'),
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->passes())->toBeTrue();
    });

    test('rejects logo with missing file', function (): void {
        $request = new CompanyLogoUploadRequest();
        $data = []; // No logo field

        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('logo'))->toBeTrue();
    });

    test('rejects logo with dimensions too small', function (): void {
        $request = new CompanyLogoUploadRequest();
        $data = [
            'logo' => ImageTestHelper::createTestImage('logo.png', 300, 300), // Below 320x320 minimum
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('logo'))->toBeTrue();
    });

    test('rejects logo with non-square aspect ratio', function (): void {
        $request = new CompanyLogoUploadRequest();
        $data = [
            'logo' => ImageTestHelper::createTestImage('logo.png', 400, 300), // Not square
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('logo'))->toBeTrue();
    });

    test('rejects logo with file too large', function (): void {
        $request = new CompanyLogoUploadRequest();
        $data = [
            'logo' => ImageTestHelper::createLargeTestFile('logo.png', 9 * 1024), // 9MB, over 8MB limit
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('logo'))->toBeTrue();
    });

    test('rejects logo with invalid file type', function (): void {
        $request = new CompanyLogoUploadRequest();
        $data = [
            'logo' => UploadedFile::fake()->create('logo.gif', 1024, 'image/gif'), // GIF not allowed
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('logo'))->toBeTrue();
    });

    test('rejects logo with non-image file', function (): void {
        $request = new CompanyLogoUploadRequest();
        $data = [
            'logo' => ImageTestHelper::createNonImageFile('document.pdf', 'application/pdf'),
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('logo'))->toBeTrue();
    });

    test('rejects logo with file too small', function (): void {
        $request = new CompanyLogoUploadRequest();
        $data = [
            'logo' => ImageTestHelper::createSmallTestImage('logo.png'), // Under 1KB minimum
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('logo'))->toBeTrue();
    });

    test('provides German error messages', function (): void {
        app()->setLocale('de');

        $request = new CompanyLogoUploadRequest();
        $messages = $request->messages();

        expect($messages['logo.required'])->toContain('Logo ist erforderlich')
            ->and($messages['logo.image'])->toContain('gültige Bilddatei')
            ->and($messages['logo.max'])->toContain('8MB')
            ->and($messages['logo.dimensions'])->toContain('320x320 Pixel')
            ->and($messages['logo.mimes'])->toContain('PNG- oder JPG-Datei');
    });

    test('provides custom attributes', function (): void {
        $request = new CompanyLogoUploadRequest();
        $attributes = $request->attributes();

        expect($attributes['logo'])->toBe(__('Logo'));
    });
});

describe('CompanyBannerUploadRequest', function (): void {
    test('authorizes company user', function (): void {
        $request = new CompanyBannerUploadRequest();
        $request->setUserResolver(fn () => $this->company);

        expect($request->authorize())->toBeTrue();
    });

    test('does not authorize non-company user', function (): void {
        $request = new CompanyBannerUploadRequest();
        $request->setUserResolver(fn () => null);

        expect($request->authorize())->toBeFalse();
    });

    test('validates valid PNG banner image', function (): void {
        $request = new CompanyBannerUploadRequest();
        $data = [
            'banner' => ImageTestHelper::createTestImage('banner.png', 1200, 400),
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->passes())->toBeTrue();
    });

    test('validates valid JPG banner image', function (): void {
        $request = new CompanyBannerUploadRequest();
        $data = [
            'banner' => ImageTestHelper::createTestImage('banner.jpg', 1500, 500, 'jpg'),
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->passes())->toBeTrue();
    });

    test('validates banner with correct aspect ratio', function (): void {
        $request = new CompanyBannerUploadRequest();
        $data = [
            'banner' => ImageTestHelper::createTestImage('banner.jpg', 2400, 800, 'jpg'), // 3:1 ratio
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->passes())->toBeTrue();
    });

    test('rejects banner with missing file', function (): void {
        $request = new CompanyBannerUploadRequest();
        $data = []; // No banner field

        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('banner'))->toBeTrue();
    });

    test('rejects banner with dimensions too small', function (): void {
        $request = new CompanyBannerUploadRequest();
        $data = [
            'banner' => ImageTestHelper::createTestImage('banner.png', 1000, 300), // Below 1200x400 minimum
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('banner'))->toBeTrue();
    });

    test('rejects banner with file too large', function (): void {
        $request = new CompanyBannerUploadRequest();
        $data = [
            'banner' => ImageTestHelper::createLargeTestFile('banner.png', 17 * 1024), // 17MB, over 16MB limit
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('banner'))->toBeTrue();
    });

    test('rejects banner with invalid file type', function (): void {
        $request = new CompanyBannerUploadRequest();
        $data = [
            'banner' => UploadedFile::fake()->create('banner.webp', 1024, 'image/webp'), // WEBP not allowed
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('banner'))->toBeTrue();
    });

    test('rejects banner with non-image file', function (): void {
        $request = new CompanyBannerUploadRequest();
        $data = [
            'banner' => ImageTestHelper::createNonImageFile('video.mp4', 'video/mp4'),
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('banner'))->toBeTrue();
    });

    test('accepts banner at exact minimum dimensions', function (): void {
        $request = new CompanyBannerUploadRequest();
        $data = [
            'banner' => ImageTestHelper::createTestImage('banner.jpg', 1200, 400, 'jpg'), // Exact minimum
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->passes())->toBeTrue();
    });

    test('accepts banner at maximum file size', function (): void {
        $request = new CompanyBannerUploadRequest();
        $data = [
            'banner' => ImageTestHelper::createTestImage('banner.jpg', 1200, 400, 'jpg'), // Valid image under limit
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->passes())->toBeTrue();
    });

    test('provides German error messages', function (): void {
        app()->setLocale('de');

        $request = new CompanyBannerUploadRequest();
        $messages = $request->messages();

        expect($messages['banner.required'])->toContain('Banner ist erforderlich')
            ->and($messages['banner.image'])->toContain('gültige Bilddatei')
            ->and($messages['banner.max'])->toContain('16MB')
            ->and($messages['banner.dimensions'])->toContain('1200x400 Pixel')
            ->and($messages['banner.mimes'])->toContain('PNG- oder JPG-Datei');
    });

    test('provides custom attributes', function (): void {
        $request = new CompanyBannerUploadRequest();
        $attributes = $request->attributes();

        expect($attributes['banner'])->toBe(__('Banner'));
    });

    test('validates banner with large dimensions', function (): void {
        $request = new CompanyBannerUploadRequest();
        $data = [
            'banner' => ImageTestHelper::createTestImage('banner.jpg', 3600, 1200, 'jpg'), // Large but valid 3:1
        ];

        $validator = Validator::make($data, $request->rules());

        expect($validator->passes())->toBeTrue();
    });
});

describe('Cross-Request Validation Consistency', function (): void {
    test('both requests accept PNG and JPG formats', function (): void {
        $logoRequest = new CompanyLogoUploadRequest();
        $bannerRequest = new CompanyBannerUploadRequest();

        $logoData = ['logo' => ImageTestHelper::createTestImage('test.png', 400, 400)];
        $bannerData = ['banner' => ImageTestHelper::createTestImage('test.png', 1200, 400)];

        $logoValidator = Validator::make($logoData, $logoRequest->rules());
        $bannerValidator = Validator::make($bannerData, $bannerRequest->rules());

        expect($logoValidator->passes())->toBeTrue()
            ->and($bannerValidator->passes())->toBeTrue();
    });

    test('both requests reject GIF format consistently', function (): void {
        $logoRequest = new CompanyLogoUploadRequest();
        $bannerRequest = new CompanyBannerUploadRequest();

        $logoData = ['logo' => UploadedFile::fake()->create('test.gif', 1024, 'image/gif')];
        $bannerData = ['banner' => UploadedFile::fake()->create('test.gif', 1024, 'image/gif')];

        $logoValidator = Validator::make($logoData, $logoRequest->rules());
        $bannerValidator = Validator::make($bannerData, $bannerRequest->rules());

        expect($logoValidator->fails())->toBeTrue()
            ->and($bannerValidator->fails())->toBeTrue();
    });

    test('both requests have minimum file size requirement', function (): void {
        $logoRequest = new CompanyLogoUploadRequest();
        $bannerRequest = new CompanyBannerUploadRequest();

        $logoData = ['logo' => ImageTestHelper::createSmallTestImage('test.png')]; // Under 1KB
        $bannerData = ['banner' => ImageTestHelper::createSmallTestImage('test.png')]; // Under 1KB

        $logoValidator = Validator::make($logoData, $logoRequest->rules());
        $bannerValidator = Validator::make($bannerData, $bannerRequest->rules());

        expect($logoValidator->fails())->toBeTrue()
            ->and($bannerValidator->fails())->toBeTrue();
    });

    test('different maximum file size limits are properly enforced', function (): void {
        $logoRequest = new CompanyLogoUploadRequest();
        $bannerRequest = new CompanyBannerUploadRequest();

        // Test logo at 10MB (should fail - over 8MB limit)
        $logoData = ['logo' => ImageTestHelper::createLargeTestFile('test.png', 10 * 1024)];
        $logoValidator = Validator::make($logoData, $logoRequest->rules());
        expect($logoValidator->fails())->toBeTrue();

        // Test banner at moderate size (should pass - under 16MB limit)
        $bannerData = ['banner' => ImageTestHelper::createTestImage('test.png', 1200, 400)];
        $bannerValidator = Validator::make($bannerData, $bannerRequest->rules());
        expect($bannerValidator->passes())->toBeTrue();
    });
});
