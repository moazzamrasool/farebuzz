<?php

namespace Database\Seeders;

use App\Models\Admin\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Default admin credentials:
        //   Email    : admin@farebuzz.com
        //   Password : Admin@123
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@farebuzz.com'],
            [
                'name'     => 'FareBuzzer Admin',
                'password' => Hash::make('Admin@123'),
            ]
        );

        // Force tier/status even if this row pre-dated the tenancy columns.
        $admin->update(['tier' => 'super_admin', 'status' => 'active']);
    }
}
