<?php

declare(strict_types=1);

use App\Http\Requests\JobListing\DeleteJobListingRequest;
use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

uses(RefreshDatabase::class);

it('authorizes company to delete their own job listing', function () {
    // Arrange
    $company = Company::factory()->create();
    $jobListing = JobListing::factory()->create([
        'company_id' => $company->id,
    ]);

    // Create a mock request
    $request = new DeleteJobListingRequest();
    $request->setUserResolver(fn () => $company);

    // Set up a route with controller and action
    $route = new Route('DELETE', 'job-listings/{jobListing}', ['controller' => 'JobListingController', 'action' => 'destroy']);
    $route->bind(Request::create('/job-listings/'.$jobListing->id, 'DELETE'));
    $route->setParameter('jobListing', $jobListing);

    $request->setRouteResolver(fn () => $route);

    // Assert
    expect($request->authorize())->toBeTrue();
});
