<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class TestCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::firstOrCreate(
            ['email' => 'damian.ermanni@myitjob.ch'],
            [
                'name' => 'myitjob',
                'first_name' => 'Damian',
                'last_name' => 'Ermanni',
                'email' => 'damian.ermanni@myitjob.ch',
                'phone_number' => '0797166222',
                'password' => Hash::make('super888'),
                'email_verified_at' => '2025-08-11 00:00:00',
                'active' => true,
                'blocked' => false,
            ]
        );
    }
}
