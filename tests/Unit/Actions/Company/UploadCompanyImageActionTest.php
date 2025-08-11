<?php

declare(strict_types=1);

use App\Actions\Company\UploadCompanyImageAction;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->action = new UploadCompanyImageAction();
    Storage::fake('public');

    $this->company = Company::factory()->create([
        'logo_path' => null,
        'banner_path' => null,
    ]);
});

it('uploads a logo image successfully', function () {
    $file = UploadedFile::fake()->image('logo.png', 400, 400);

    // Mock Image facade
    $mockImage = $this->mock(Intervention\Image\Interfaces\ImageInterface::class);
    $mockImage->shouldReceive('width')->andReturn(400);
    $mockImage->shouldReceive('height')->andReturn(400);
    $mockImage->shouldReceive('cover')->with(400, 400)->andReturnSelf();
    $mockImage->shouldReceive('encode')->andReturn('encoded-image-data');

    Image::shouldReceive('read')->with($file)->andReturn($mockImage);

    $result = $this->action->execute($this->company, $file, 'logo');

    expect($result)->toBeTrue();

    // Refresh company to get updated data
    $this->company->refresh();

    expect($this->company->logo_path)->toMatch('/company-images\/logos\/company-\d+-logo-\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}-\w{8}\.png/');
    expect($this->company->logo_original_name)->toBe('logo.png');
    expect($this->company->logo_file_size)->toBe($file->getSize());
    expect($this->company->logo_mime_type)->toBe('image/png');
    expect($this->company->logo_dimensions)->toBe(['width' => 400, 'height' => 400]);
    expect($this->company->logo_uploaded_at)->not->toBeNull();

    // Verify file was stored
    Storage::disk('public')->assertExists($this->company->logo_path);
});

it('uploads a banner image successfully', function () {
    $file = UploadedFile::fake()->image('banner.jpg', 1200, 400);

    // Mock Image facade
    $mockImage = $this->mock(Intervention\Image\Interfaces\ImageInterface::class);
    $mockImage->shouldReceive('width')->andReturn(1200);
    $mockImage->shouldReceive('height')->andReturn(400);
    $mockImage->shouldReceive('cover')->with(1200, 400)->andReturnSelf();
    $mockImage->shouldReceive('encode')->andReturn('encoded-image-data');

    Image::shouldReceive('read')->with($file)->andReturn($mockImage);

    $result = $this->action->execute($this->company, $file, 'banner');

    expect($result)->toBeTrue();

    // Refresh company to get updated data
    $this->company->refresh();

    expect($this->company->banner_path)->toMatch('/company-images\/banners\/company-\d+-banner-\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}-\w{8}\.jpg/');
    expect($this->company->banner_original_name)->toBe('banner.jpg');
    expect($this->company->banner_file_size)->toBe($file->getSize());
    expect($this->company->banner_mime_type)->toBe('image/jpeg');
    expect($this->company->banner_dimensions)->toBe(['width' => 1200, 'height' => 400]);
    expect($this->company->banner_uploaded_at)->not->toBeNull();
});

it('throws exception for invalid image type', function () {
    $file = UploadedFile::fake()->image('test.png');

    expect(fn () => $this->action->execute($this->company, $file, 'invalid'))
        ->toThrow(InvalidArgumentException::class, 'Invalid image type. Must be "logo" or "banner".');
});

it('deletes existing image before uploading new one', function () {
    // Create existing logo file
    $existingPath = 'company-images/logos/existing-logo.png';
    Storage::disk('public')->put($existingPath, 'fake-image-content');

    $this->company->update(['logo_path' => $existingPath]);

    $file = UploadedFile::fake()->image('new-logo.png', 400, 400);

    // Mock Image facade
    $mockImage = $this->mock(Intervention\Image\Interfaces\ImageInterface::class);
    $mockImage->shouldReceive('width')->andReturn(400);
    $mockImage->shouldReceive('height')->andReturn(400);
    $mockImage->shouldReceive('cover')->with(400, 400)->andReturnSelf();
    $mockImage->shouldReceive('encode')->andReturn('encoded-image-data');

    Image::shouldReceive('read')->with($file)->andReturn($mockImage);

    $result = $this->action->execute($this->company, $file, 'logo');

    expect($result)->toBeTrue();

    // Verify old file was deleted
    Storage::disk('public')->assertMissing($existingPath);

    // Verify new file exists
    $this->company->refresh();
    Storage::disk('public')->assertExists($this->company->logo_path);
});

it('handles missing existing image gracefully', function () {
    // Set a path that doesn't exist
    $this->company->update(['logo_path' => 'non-existent/path.png']);

    $file = UploadedFile::fake()->image('logo.png', 400, 400);

    // Mock Image facade
    $mockImage = $this->mock(Intervention\Image\Interfaces\ImageInterface::class);
    $mockImage->shouldReceive('width')->andReturn(400);
    $mockImage->shouldReceive('height')->andReturn(400);
    $mockImage->shouldReceive('cover')->with(400, 400)->andReturnSelf();
    $mockImage->shouldReceive('encode')->andReturn('encoded-image-data');

    Image::shouldReceive('read')->with($file)->andReturn($mockImage);

    $result = $this->action->execute($this->company, $file, 'logo');

    expect($result)->toBeTrue();
});

it('generates unique filename', function () {
    $file1 = UploadedFile::fake()->image('test.png', 400, 400);
    $file2 = UploadedFile::fake()->image('test.png', 400, 400);

    // Mock Image facade for both uploads
    $mockImage = $this->mock(Intervention\Image\Interfaces\ImageInterface::class);
    $mockImage->shouldReceive('width')->andReturn(400)->times(2);
    $mockImage->shouldReceive('height')->andReturn(400)->times(2);
    $mockImage->shouldReceive('cover')->with(400, 400)->andReturnSelf()->times(2);
    $mockImage->shouldReceive('encode')->andReturn('encoded-image-data')->times(2);

    Image::shouldReceive('read')->andReturn($mockImage)->times(2);

    $company2 = Company::factory()->create();

    $this->action->execute($this->company, $file1, 'logo');
    $this->action->execute($company2, $file2, 'logo');

    $this->company->refresh();
    $company2->refresh();

    expect($this->company->logo_path)->not->toBe($company2->logo_path);
});

it('throws exception when storage fails', function () {
    $file = UploadedFile::fake()->image('logo.png', 400, 400);

    // Mock Image facade
    $mockImage = $this->mock(Intervention\Image\Interfaces\ImageInterface::class);
    $mockImage->shouldReceive('width')->andReturn(400);
    $mockImage->shouldReceive('height')->andReturn(400);
    $mockImage->shouldReceive('cover')->with(400, 400)->andReturnSelf();
    $mockImage->shouldReceive('encode')->andReturn('encoded-image-data');

    Image::shouldReceive('read')->with($file)->andReturn($mockImage);

    // Mock Storage to return false on put
    Storage::shouldReceive('disk')->with('public')->andReturnSelf();
    Storage::shouldReceive('exists')->andReturn(false);
    Storage::shouldReceive('put')->andReturn(false);

    expect(fn () => $this->action->execute($this->company, $file, 'logo'))
        ->toThrow(RuntimeException::class, 'Failed to store logo image.');
});

it('processes logo image with correct dimensions', function () {
    $file = UploadedFile::fake()->image('logo.png', 800, 600);

    // Mock Image facade
    $mockImage = $this->mock(Intervention\Image\Interfaces\ImageInterface::class);
    $mockImage->shouldReceive('width')->andReturn(800);
    $mockImage->shouldReceive('height')->andReturn(600);
    $mockImage->shouldReceive('cover')->with(400, 400)->once()->andReturnSelf();
    $mockImage->shouldReceive('encode')->andReturn('encoded-image-data');

    Image::shouldReceive('read')->with($file)->andReturn($mockImage);

    $result = $this->action->execute($this->company, $file, 'logo');

    expect($result)->toBeTrue();
});

it('processes banner image with correct dimensions', function () {
    $file = UploadedFile::fake()->image('banner.jpg', 1800, 800);

    // Mock Image facade
    $mockImage = $this->mock(Intervention\Image\Interfaces\ImageInterface::class);
    $mockImage->shouldReceive('width')->andReturn(1800);
    $mockImage->shouldReceive('height')->andReturn(800);
    $mockImage->shouldReceive('cover')->with(1200, 400)->once()->andReturnSelf();
    $mockImage->shouldReceive('encode')->andReturn('encoded-image-data');

    Image::shouldReceive('read')->with($file)->andReturn($mockImage);

    $result = $this->action->execute($this->company, $file, 'banner');

    expect($result)->toBeTrue();
});
