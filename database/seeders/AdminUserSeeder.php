<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'admin@gmail.com';

        $existingUser = DB::table('users')->where('email', $email)->first();

        if ($existingUser) {
            DB::table('users')
                ->where('email', $email)
                ->update([
                    'name' => 'Admin',
                    'slug' => 'admin',
                    'phone_number' => '9999999999',
                    'alternative_email' => null,
                    'alternative_phone_number' => null,
                    'whatsapp_number' => '9999999999',
                    'email_verified_at' => now(),
                    'password' => Hash::make('admin@123'),
                    'image' => null,
                    'address' => 'System Administrator',
                    'role' => 'admin',
                    'role_short_form' => 'ADM',
                    'status' => 'active',
                    'last_login_at' => null,
                    'last_login_ip' => null,
                    'remember_token' => Str::random(10),
                    'created_at_ip' => '127.0.0.1',
                    'metadata' => json_encode([
                        'seeded' => true,
                        'type' => 'system admin',
                    ]),
                    'updated_at' => now(),
                ]);

            return;
        }

        DB::table('users')->insert([
            'uuid' => (string) Str::uuid(),
            'name' => 'Admin',
            'slug' => 'admin',
            'email' => $email,
            'phone_number' => '9999999999',
            'alternative_email' => null,
            'alternative_phone_number' => null,
            'whatsapp_number' => '9999999999',
            'email_verified_at' => now(),
            'password' => Hash::make('admin@123'),
            'image' => null,
            'address' => 'System Administrator',
            'role' => 'admin',
            'role_short_form' => 'ADM',
            'status' => 'active',
            'last_login_at' => null,
            'last_login_ip' => null,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
            'created_by' => null,
            'created_at_ip' => '127.0.0.1',
            'deleted_at' => null,
            'metadata' => json_encode([
                'seeded' => true,
                'type' => 'system admin',
            ]),
        ]);
    }
}
