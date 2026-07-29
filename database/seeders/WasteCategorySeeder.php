<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WasteCategory;

class WasteCategorySeeder extends Seeder
{
    public function run(): void
    {
        WasteCategory::insert([

            [
                'category_name' => 'Organic',
                'description' => 'Biodegradable waste',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_name' => 'Inorganic',
                'description' => 'Non-biodegradable waste',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_name' => 'Hazardous',
                'description' => 'Scheduled and hazardous waste',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}