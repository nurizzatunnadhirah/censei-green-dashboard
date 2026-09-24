<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Faculty;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faculty = Faculty::where('faculty_code', 'FTMK')->firstOrFail();

        User::updateOrCreate(
            ['email' => 'admin.test@censei.local'],
            [
                'name' => 'Test Administrator',
                'password' => 'AdminTest123!',
                'phone_number' => '0123456789',
                'role' => 'Admin',
                'position' => 'System Administrator',
                'faculty_id' => $faculty->id,
                'status' => 'Active',
                'profile_photo' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff.test@censei.local'],
            [
                'name' => 'Test Staff',
                'password' => 'StaffTest123!',
                'phone_number' => '0123456788',
                'role' => 'Staff',
                'position' => 'Staff',
                'faculty_id' => $faculty->id,
                'status' => 'Active',
                'profile_photo' => null,
            ]
        );
    }
}