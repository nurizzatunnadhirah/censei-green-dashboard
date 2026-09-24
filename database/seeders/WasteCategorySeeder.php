<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WasteCategory;

class WasteCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [

            [
                'category_name' => 'Organic',
                'description' => 'Biodegradable waste',
            ],

            [
                'category_name' => 'Inorganic',
                'description' => 'Non-biodegradable waste',
            ],

            [
                'category_name' => 'Hazardous',
                'description' => 'Scheduled and hazardous waste',
            ],

        ];

        foreach ($categories as $category) {
            WasteCategory::updateOrCreate(
                ['category_name' => $category['category_name']],
                ['description' => $category['description']]
            );
        }
    }
}