<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;

class FacultySeeder extends Seeder
{
    public function run(): void
    {
        $faculties = [

            ['faculty_code' => 'FTMK', 'faculty_name' => 'Faculty of Information and Communication Technology'],

            ['faculty_code' => 'FKE', 'faculty_name' => 'Faculty of Electrical Engineering'],

            ['faculty_code' => 'FTK', 'faculty_name' => 'Faculty of Mechanical Engineering'],

            ['faculty_code' => 'FKP', 'faculty_name' => 'Faculty of Manufacturing Engineering'],

            ['faculty_code' => 'FPTT', 'faculty_name' => 'Faculty of Engineering Technology'],

            ['faculty_code' => 'FKM', 'faculty_name' => 'Faculty of Technology Management and Technopreneurship'],

            ['faculty_code' => 'PPP', 'faculty_name' => 'Centre for Language Learning'],

            ['faculty_code' => 'CENSEI', 'faculty_name' => 'Centre for Smart Environment'],

        ];

        foreach ($faculties as $faculty) {
            Faculty::create($faculty);
        }
    }
}