<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Company\Auth;

use App\Models\Company;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

final class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('company.register'));

        $response->assertStatus(200);
    }

    public function test_new_companies_can_register(): void
    {
        Event::fake();

        $response = $this->post(route('company.register'), [
            'name' => 'Test Company',
            'email' => 'test@company.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        Event::assertDispatched(Registered::class);

        $this->assertAuthenticated('company');
        $response->assertRedirect(route('company.dashboard'));
    }

    public function test_company_cannot_register_with_same_email(): void
    {
        // Create a company with the email
        Company::factory()->create([
            'email' => 'already@exists.com',
        ]);

        $response = $this->post(route('company.register'), [
            'name' => 'Test Company',
            'email' => 'already@exists.com', // Same email
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('company');
    }
}
