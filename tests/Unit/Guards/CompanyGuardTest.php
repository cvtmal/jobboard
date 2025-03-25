<?php

declare(strict_types=1);

use App\Guards\CompanyGuard;
use App\Models\Company;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Session\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

it('extends session guard', function () {
    $provider = mock(UserProvider::class);
    $session = mock(Session::class);
    $request = mock(Request::class);
    $guard = new CompanyGuard('company', $provider, $session, $request);

    expect($guard)->toBeInstanceOf(SessionGuard::class);
});

it('returns session object with type hint', function () {
    $provider = mock(UserProvider::class);
    $session = mock(Session::class);
    $request = mock(Request::class);
    $guard = new CompanyGuard('company', $provider, $session, $request);

    expect($guard->getSession())->toBe($session);
});

it('correctly attempts authentication with valid credentials', function () {
    $company = Company::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $authenticated = $this->actingAs($company, 'company')
        ->post('/login', [
            'email' => $company->email,
            'password' => 'password',
        ]);

    expect(auth('company')->check())->toBeTrue()
        ->and(auth('company')->user()->id)->toBe($company->id);
});
