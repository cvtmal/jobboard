<?php

declare(strict_types=1);

use App\Models\Company;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('public');
});

describe('Company Profile Completion', function () {
    test('can calculate profile completion percentage', function (): void {
        $company = Company::factory()->create([
            'name' => 'Test Company',
            'email' => 'test@example.com',
            'address' => '123 Test St',
            'city' => 'Test City',
            'postcode' => '12345',
            'size' => '10-50',
            'type' => 'Private',
            'industry' => 'Technology',
            'description_english' => 'Test description',
        ]);

        // Upload logo and banner to complete all steps
        Storage::disk('public')->put('company-logos/test-logo.jpg', 'fake logo content');
        Storage::disk('public')->put('company-banners/test-banner.jpg', 'fake banner content');

        $company->update([
            'logo_path' => 'company-logos/test-logo.jpg',
            'banner_path' => 'company-banners/test-banner.jpg',
        ]);

        $percentage = $company->getProfileCompletionPercentage();
        expect($percentage)->toBe(100);
    });

    test('can get profile completion steps with correct status', function (): void {
        $company = Company::factory()->create([
            'name' => 'Test Company',
            'email' => 'test@example.com',
            'address' => null, // Missing contact info
            'city' => null,
            'postcode' => null,
            'size' => null,    // Missing company details
            'type' => null,
            'industry' => null,
            'description_english' => null, // Missing description
            'description_german' => null,
            'description_french' => null,
            'description_italian' => null,
        ]);

        $steps = $company->getProfileCompletionSteps();

        expect($steps)->toHaveKey('basic_info')
            ->and($steps['basic_info'])->toBeTrue()
            ->and($steps['contact_info'])->toBeFalse()
            ->and($steps['company_details'])->toBeFalse()
            ->and($steps['description'])->toBeFalse()
            ->and($steps['logo'])->toBeFalse()
            ->and($steps['banner'])->toBeFalse();
    });

    test('can update profile completion status', function (): void {
        $company = Company::factory()->create([
            'name' => 'Test Company',
            'email' => 'test-profile-completion@example.com',
            'address' => '123 Test St',
            'city' => 'Test City',
            'postcode' => '12345',
            'size' => '10-50',
            'type' => 'Private',
            'industry' => 'Technology',
            'description_english' => 'Test description',
            'profile_completed' => false,
        ]);

        // Add logo to get to 5/6 steps (83% > 70%)
        Storage::disk('public')->put('company-logos/test-logo-completion.jpg', 'fake logo content');
        $company->update(['logo_path' => 'company-logos/test-logo-completion.jpg']);

        $company->updateProfileCompletion();
        $company->refresh();

        // Should be completed as it has at least 70% completion (5/6 = 83%)
        expect($company->profile_completed)->toBeTrue()
            ->and($company->profile_completed_at)->not->toBeNull()
            ->and($company->profile_completion_steps)->toBeArray();
    });

    test('should show onboarding for new incomplete profiles', function (): void {
        $company = Company::factory()->create([
            'profile_completed' => false,
            'created_at' => now()->subDays(5), // Recently created
        ]);

        expect($company->shouldShowOnboarding())->toBeTrue();
    });

    test('should not show onboarding for completed profiles', function (): void {
        $company = Company::factory()->create([
            'profile_completed' => true,
            'created_at' => now()->subDays(5),
        ]);

        expect($company->shouldShowOnboarding())->toBeFalse();
    });

    test('should not show onboarding for old incomplete profiles', function (): void {
        $company = Company::factory()->create([
            'profile_completed' => false,
            'created_at' => now()->subDays(35), // Too old
        ]);

        expect($company->shouldShowOnboarding())->toBeFalse();
    });

    test('can get missing profile steps', function (): void {
        $company = Company::factory()->create([
            'name' => 'Test Company',
            'email' => 'test-missing-steps@example.com',
            'address' => null,
            'city' => null,
            'postcode' => null,
            'size' => null,
            'type' => null,
            'industry' => null,
            'description_english' => null,
            'description_german' => null,
            'description_french' => null,
            'description_italian' => null,
            'logo_path' => null,
            'banner_path' => null,
        ]);

        $missingSteps = $company->getMissingProfileSteps();

        expect($missingSteps)->toContain('contact_info')
            ->and($missingSteps)->toContain('company_details')
            ->and($missingSteps)->toContain('description')
            ->and($missingSteps)->toContain('logo')
            ->and($missingSteps)->toContain('banner')
            ->and($missingSteps)->not->toContain('basic_info');
    });
});

describe('Company Profile Routes', function () {
    test('can access company profile page', function (): void {
        $company = Company::factory()->create();

        $response = $this->actingAs($company, 'company')
            ->get(route('company.profile'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('company/profile')
                ->has('company')
                ->has('profileCompletion')
                ->has('shouldShowOnboarding')
            );
    });

    test('can update company profile', function (): void {
        $company = Company::factory()->create();

        $updateData = [
            'name' => 'Updated Company',
            'email' => 'updated@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone_number' => '+41 12 345 67 89',
            'address' => '123 Updated St',
            'postcode' => '8000',
            'city' => 'Zurich',
            'url' => 'https://updated.com',
            'size' => '50-100',
            'type' => 'Public',
            'industry' => 'Finance',
            'founded_year' => '2020',
            'description_english' => 'Updated description',
            'mission_statement' => 'Our mission is to help companies succeed',
            'benefits' => ['Health insurance', 'Remote work'],
            'company_culture' => ['Innovation', 'Collaboration'],
        ];

        $response = $this->actingAs($company, 'company')
            ->patch(route('company.profile.update'), $updateData);

        $response->assertRedirect();

        $company->refresh();

        expect($company->name)->toBe('Updated Company')
            ->and($company->email)->toBe('updated@example.com')
            ->and($company->first_name)->toBe('John')
            ->and($company->last_name)->toBe('Doe')
            ->and($company->phone_number)->toBe('+41 12 345 67 89')
            ->and($company->address)->toBe('123 Updated St')
            ->and($company->postcode)->toBe('8000')
            ->and($company->city)->toBe('Zurich')
            ->and($company->url)->toBe('https://updated.com')
            ->and($company->size)->toBe('50-100')
            ->and($company->type)->toBe('Public')
            ->and($company->industry)->toBe('Finance')
            ->and($company->founded_year)->toBe(2020)
            ->and($company->description_english)->toBe('Updated description')
            ->and($company->mission_statement)->toBe('Our mission is to help companies succeed')
            ->and($company->benefits)->toBe(['Health insurance', 'Remote work'])
            ->and($company->company_culture)->toBe(['Innovation', 'Collaboration']);
    });

    test('can skip onboarding', function (): void {
        $company = Company::factory()->create([
            'profile_completed' => false,
        ]);

        $response = $this->actingAs($company, 'company')
            ->post(route('company.profile.skip'));

        $response->assertRedirect(route('company.dashboard'))
            ->assertSessionHas('status', 'onboarding-skipped');

        $company->refresh();

        expect($company->profile_completed)->toBeTrue()
            ->and($company->profile_completed_at)->not->toBeNull();
    });

    test('dashboard shows onboarding guidance for incomplete profiles', function (): void {
        $company = Company::factory()->create([
            'profile_completed' => false,
            'created_at' => now()->subDays(5),
        ]);

        $response = $this->actingAs($company, 'company')
            ->get(route('company.dashboard'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('company/dashboard')
                ->has('company')
                ->has('profileCompletion')
                ->where('shouldShowOnboarding', true)
            );
    });

    test('dashboard does not show onboarding for completed profiles', function (): void {
        // Create a company with complete profile data
        $company = Company::factory()->create([
            'name' => 'Complete Company',
            'email' => 'complete@example.com',
            'address' => '123 Complete St',
            'city' => 'Complete City',
            'postcode' => '12345',
            'size' => '10-50',
            'type' => 'Private',
            'industry' => 'Technology',
            'description_english' => 'Complete description',
            'profile_completed' => true,
            'profile_completed_at' => now()->subDays(1),
            'created_at' => now()->subDays(5),
        ]);

        // Add logo and banner to ensure full completion
        Storage::disk('public')->put('complete-logo.jpg', 'fake logo content');
        Storage::disk('public')->put('complete-banner.jpg', 'fake banner content');
        $company->update([
            'logo_path' => 'complete-logo.jpg',
            'banner_path' => 'complete-banner.jpg',
        ]);

        $response = $this->actingAs($company, 'company')
            ->get(route('company.dashboard'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('company/dashboard')
                ->has('company')
                ->has('profileCompletion')
                ->where('shouldShowOnboarding', false)
            );
    });
});

describe('Profile Validation', function () {
    test('validates required fields', function (): void {
        $company = Company::factory()->create();

        $response = $this->actingAs($company, 'company')
            ->patch(route('company.profile.update'), [
                'name' => '', // Required field
                'email' => 'invalid-email', // Invalid email
            ]);

        $response->assertSessionHasErrors(['name', 'email']);
    });

    test('validates url format', function (): void {
        $company = Company::factory()->create();

        $response = $this->actingAs($company, 'company')
            ->patch(route('company.profile.update'), [
                'name' => 'Test Company',
                'email' => 'test@example.com',
                'url' => 'not-a-valid-url',
            ]);

        $response->assertSessionHasErrors(['url']);
    });

    test('validates founded year range', function (): void {
        $company = Company::factory()->create();

        $response = $this->actingAs($company, 'company')
            ->patch(route('company.profile.update'), [
                'name' => 'Test Company',
                'email' => 'test@example.com',
                'founded_year' => '1750', // Too early
            ]);

        $response->assertSessionHasErrors(['founded_year']);

        $response = $this->actingAs($company, 'company')
            ->patch(route('company.profile.update'), [
                'name' => 'Test Company',
                'email' => 'test@example.com',
                'founded_year' => (string) (date('Y') + 1), // Future year
            ]);

        $response->assertSessionHasErrors(['founded_year']);
    });

    test('validates benefits and company culture arrays', function (): void {
        $company = Company::factory()->create();

        $response = $this->actingAs($company, 'company')
            ->patch(route('company.profile.update'), [
                'name' => 'Test Company',
                'email' => 'test@example.com',
                'benefits' => ['Valid benefit', str_repeat('x', 201)], // Second item too long
                'company_culture' => ['Valid culture', str_repeat('x', 201)], // Second item too long
            ]);

        $response->assertSessionHasErrors(['benefits.1', 'company_culture.1']);
    });
});

describe('Authentication', function () {
    test('requires company authentication for profile routes', function (): void {
        $response = $this->get(route('company.profile'));
        $response->assertRedirect(route('login'));

        $response = $this->patch(route('company.profile.update'), []);
        $response->assertRedirect(route('login'));

        $response = $this->post(route('company.profile.skip'));
        $response->assertRedirect(route('login'));
    });

    test('rejects non-company users', function (): void {
        $user = App\Models\User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('company.profile'));

        $response->assertRedirect(route('login'));
    });
});
