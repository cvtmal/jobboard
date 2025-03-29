<?php

declare(strict_types=1);

namespace App\Actions\Applicant;

use App\Models\Applicant;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

final class CreateApplicantAction
{
    /**
     * Execute the action to create a new applicant.
     *
     * @param  array<string, mixed>  $data  Validated data for the applicant
     *
     * @throws Throwable
     */
    public function execute(array $data): Applicant
    {
        $applicantData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']), // @phpstan-ignore-line
        ];

        // Add optional fields if they exist
        foreach ([
            'address', 'phone', 'profile_photo_path',
            'city', 'postcode', 'country', 'bio',
        ] as $field) {
            if (isset($data[$field])) {
                $applicantData[$field] = $data[$field];
            }
        }

        $applicant = DB::transaction(fn () => Applicant::create($applicantData));

        event(new Registered($applicant));

        return $applicant;
    }
}
