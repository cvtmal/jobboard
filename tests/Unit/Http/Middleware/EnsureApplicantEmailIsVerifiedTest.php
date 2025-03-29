<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureApplicantEmailIsVerified;
use App\Models\Applicant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

it('allows verified applicants to proceed', function () {
    // Create a verified applicant
    $applicant = Applicant::factory()->create([
        'email_verified_at' => now(),
    ]);
    
    // Create a request with the authenticated applicant
    $request = new Request();
    $request->setUserResolver(fn () => $applicant);
    
    // Create the middleware
    $middleware = new EnsureApplicantEmailIsVerified();
    
    // Set up the next closure that should be called if middleware passes
    $next = fn (Request $request) => new Response('Proceeded');
    
    // Execute middleware
    $response = $middleware->handle($request, $next);
    
    // Assert that the middleware allowed the request to proceed
    expect($response->getContent())->toBe('Proceeded');
});

it('redirects unverified applicants to verification notice', function () {
    // Create an unverified applicant
    $applicant = Applicant::factory()->create([
        'email_verified_at' => null,
    ]);
    
    // Create a request with the authenticated applicant
    $request = new Request();
    $request->setUserResolver(fn () => $applicant);
    
    // Create the middleware
    $middleware = new EnsureApplicantEmailIsVerified();
    
    // Set up the next closure (should not be reached)
    $next = fn (Request $request) => new Response('Should not reach this');
    
    // Mock the redirect response
    app()->instance('redirect', $redirect = Mockery::mock(\Illuminate\Routing\Redirector::class));
    $redirect->shouldReceive('route')
        ->with('applicant.verification.notice')
        ->once()
        ->andReturn(new Response('Redirected to verification'));
    
    // Execute middleware
    $response = $middleware->handle($request, $next);
    
    // Assert that we got redirected
    expect($response->getContent())->toBe('Redirected to verification');
});

it('returns 403 for JSON request from unverified applicant', function () {
    // Create an unverified applicant
    $applicant = Applicant::factory()->create([
        'email_verified_at' => null,
    ]);
    
    // Create a request with the authenticated applicant
    $request = new Request();
    $request->headers->set('Accept', 'application/json');
    $request->setUserResolver(fn () => $applicant);
    
    // Create the middleware
    $middleware = new EnsureApplicantEmailIsVerified();
    
    // Set up the next closure (should not be reached)
    $next = fn (Request $request) => new Response('Should not reach this');
    
    // Execute middleware and expect exception
    expect(fn () => $middleware->handle($request, $next))
        ->toThrow(HttpException::class);
    
    // Since we're expecting an exception, we need to reset the mock
    Mockery::close();
});

it('redirects unauthenticated users to verification notice', function () {
    // Create a request with no authenticated user
    $request = new Request();
    $request->setUserResolver(fn () => null);
    
    // Create the middleware
    $middleware = new EnsureApplicantEmailIsVerified();
    
    // Set up the next closure (should not be reached)
    $next = fn (Request $request) => new Response('Should not reach this');
    
    // Mock the redirect response
    app()->instance('redirect', $redirect = Mockery::mock(\Illuminate\Routing\Redirector::class));
    $redirect->shouldReceive('route')
        ->with('applicant.verification.notice')
        ->once()
        ->andReturn(new Response('Redirected to verification'));
    
    // Execute middleware
    $response = $middleware->handle($request, $next);
    
    // Assert that we got redirected
    expect($response->getContent())->toBe('Redirected to verification');
});
