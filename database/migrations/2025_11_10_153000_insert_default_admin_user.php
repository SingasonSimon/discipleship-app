<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $defaults = [
            [
                'email' => env('DEFAULT_ADMIN_EMAIL', 'admin@discipleship.local'),
                'name' => env('DEFAULT_ADMIN_NAME', 'Admin User'),
                'role' => 'admin',
                'phone' => env('DEFAULT_ADMIN_PHONE', null),
            ],
            [
                'email' => env('DEFAULT_PASTOR_EMAIL', 'pastor@discipleship.local'),
                'name' => env('DEFAULT_PASTOR_NAME', 'Pastor John Smith'),
                'role' => 'pastor',
                'phone' => env('DEFAULT_PASTOR_PHONE', null),
            ],
            [
                'email' => env('DEFAULT_MENTOR_EMAIL', 'mentor@discipleship.local'),
                'name' => env('DEFAULT_MENTOR_NAME', 'Mentor Grace'),
                'role' => 'mentor',
                'phone' => env('DEFAULT_MENTOR_PHONE', null),
            ],
            [
                'email' => env('DEFAULT_MEMBER_EMAIL', 'member@discipleship.local'),
                'name' => env('DEFAULT_MEMBER_NAME', 'Member Joseph'),
                'role' => 'member',
                'phone' => env('DEFAULT_MEMBER_PHONE', null),
            ],
        ];

        $sharedPassword = env('DEFAULT_SHARED_PASSWORD', env('DEFAULT_ADMIN_PASSWORD', 'password'));

        foreach ($defaults as $user) {
            $existing = DB::table('users')->where('email', $user['email'])->first();

            if ($existing) {
                DB::table('users')
                    ->where('email', $user['email'])
                    ->update([
                        'name' => $user['name'],
                        'role' => $user['role'],
                        'phone' => $user['phone'],
                        'password' => Hash::make($sharedPassword),
                        'email_verified_at' => $existing->email_verified_at ?? now(),
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('users')->insert([
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'phone' => $user['phone'],
                    'password' => Hash::make($sharedPassword),
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Ensure coordinator legacy roles become mentors
        DB::table('users')
            ->where('role', 'coordinator')
            ->update([
                'role' => 'mentor',
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $emails = [
            env('DEFAULT_ADMIN_EMAIL', 'admin@discipleship.local'),
            env('DEFAULT_PASTOR_EMAIL', 'pastor@discipleship.local'),
            env('DEFAULT_MENTOR_EMAIL', 'mentor@discipleship.local'),
            env('DEFAULT_MEMBER_EMAIL', 'member@discipleship.local'),
        ];

        DB::table('users')
            ->whereIn('email', $emails)
            ->delete();
    }
};

