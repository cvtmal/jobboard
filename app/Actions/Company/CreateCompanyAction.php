<?php

declare(strict_types=1);

namespace App\Actions\Company;

use App\Models\Company;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

final class CreateCompanyAction
{
    /**
     * Execute the action to create a new company.
     *
     * @param  array<string, mixed>  $data  Validated data for the company
     *
     * @throws Throwable
     */
    public function execute(array $data): Company
    {
        $company = DB::transaction(fn () => Company::create([
            'name' => $data['name'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone_number' => $data['phone_number'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'address' => $data['address'] ?? null,
            'postcode' => $data['postcode'] ?? null,
            'city' => $data['city'] ?? null,
            'url' => $data['url'] ?? null,
            'active' => true,
            'blocked' => false,
        ]));

        event(new Registered($company));

        return $company;
    }
}
