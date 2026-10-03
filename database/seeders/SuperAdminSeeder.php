<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Creates (or updates) the single super-admin account from environment
 * variables. Intended to be re-run safely on every deploy:
 * - If the email doesn't exist yet, the admin is created.
 * - If it does exist, the name/password are kept in sync with the
 *   secrets file, but no duplicate account is ever made.
 *
 * Reads: SUPER_ADMIN_NAME, SUPER_ADMIN_EMAIL, SUPER_ADMIN_PASSWORD
 */
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL');
        $password = env('SUPER_ADMIN_PASSWORD');
        $name = env('SUPER_ADMIN_NAME', 'Site Owner');

        if (empty($email) || empty($password)) {
            $this->command->warn('SUPER_ADMIN_EMAIL or SUPER_ADMIN_PASSWORD missing - skipping.');
            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("Super admin ready: {$email}");
    }
}
