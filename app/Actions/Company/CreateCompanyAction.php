<?php

declare(strict_types=1);

namespace App\Actions\Company;

use App\Models\Company;
use Illuminate\Support\Facades\Hash;

final class CreateCompanyAction
{
    /**
     * Execute the action to create a new company.
     *
     * @param  array<string, mixed>  $data  Validated data for the company
     */
    public function execute(array $data): Company
    {
        return Company::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']), // @phpstan-ignore-line
            'address' => $data['address'] ?? null,
            'postcode' => $data['postcode'] ?? null,
            'city' => $data['city'] ?? null,
            'url' => $data['url'] ?? null,
            'active' => true,
            'blocked' => false,
        ]);
    }
}
