<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WasteCategory;
use App\Models\WasteType;

class WasteTypeSeeder extends Seeder
{
    public function run(): void
    {
        $organic = WasteCategory::where('category_name', 'Organic')->first();
        $inorganic = WasteCategory::where('category_name', 'Inorganic')->first();
        $hazardous = WasteCategory::where('category_name', 'Hazardous')->first();

        WasteType::insert([

            [
                'category_id' => $organic->id,
                'type_name' => 'Food Waste',
                'description' => 'Leftover food',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $organic->id,
                'type_name' => 'Garden Waste',
                'description' => 'Leaves and branches',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $inorganic->id,
                'type_name' => 'Plastic',
                'description' => 'Plastic bottles and containers',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $inorganic->id,
                'type_name' => 'Paper',
                'description' => 'Office paper and cardboard',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $inorganic->id,
                'type_name' => 'Glass',
                'description' => 'Glass bottles',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $inorganic->id,
                'type_name' => 'Metal',
                'description' => 'Aluminium and steel',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $hazardous->id,
                'type_name' => 'Battery',
                'description' => 'Used batteries',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $hazardous->id,
                'type_name' => 'Chemical Waste',
                'description' => 'Chemical substances',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $hazardous->id,
                'type_name' => 'Fluorescent Lamp',
                'description' => 'Used fluorescent lamps',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}