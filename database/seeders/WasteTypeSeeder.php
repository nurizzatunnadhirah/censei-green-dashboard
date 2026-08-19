<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WasteCategory;
use App\Models\WasteType;

class WasteTypeSeeder extends Seeder
{
    public function run(): void
{
    $organic = WasteCategory::where('category_name', 'Organic')->firstOrFail();
    $inorganic = WasteCategory::where('category_name', 'Inorganic')->firstOrFail();
    $hazardous = WasteCategory::where('category_name', 'Hazardous')->firstOrFail();

    $wasteTypes = [

        [
            'category_id' => $organic->id,
            'type_name' => 'Food Waste',
            'description' => 'Leftover food',
        ],

        [
            'category_id' => $organic->id,
            'type_name' => 'Garden Waste',
            'description' => 'Leaves and branches',
        ],

        [
            'category_id' => $inorganic->id,
            'type_name' => 'Plastic',
            'description' => 'Plastic bottles and containers',
        ],

        [
            'category_id' => $inorganic->id,
            'type_name' => 'Paper',
            'description' => 'Office paper and cardboard',
        ],

        [
            'category_id' => $inorganic->id,
            'type_name' => 'Glass',
            'description' => 'Glass bottles',
        ],

        [
            'category_id' => $inorganic->id,
            'type_name' => 'Metal',
            'description' => 'Aluminium and steel',
        ],

        [
            'category_id' => $hazardous->id,
            'type_name' => 'Battery',
            'description' => 'Used batteries',
        ],

        [
            'category_id' => $hazardous->id,
            'type_name' => 'Chemical Waste',
            'description' => 'Chemical substances',
        ],

        [
            'category_id' => $hazardous->id,
            'type_name' => 'Fluorescent Lamp',
            'description' => 'Used fluorescent lamps',
        ],

        [
            'category_id' => $inorganic->id,
            'type_name' => 'Other class',
            'description' => 'Mixed recyclable materials',
        ],

        [
            'category_id' => $inorganic->id,
            'type_name' => 'Fabric',
            'description' => 'Textile waste',
        ],

        [
            'category_id' => $hazardous->id,
            'type_name' => 'eWaste',
            'description' => 'Electronic waste',
        ],

        [
            'category_id' => $hazardous->id,
            'type_name' => 'Waste Cooking Oil (WCO)',
            'description' => 'Used cooking oil',
        ],

    ];

    foreach ($wasteTypes as $type) {
        WasteType::updateOrCreate(
            [
                'category_id' => $type['category_id'],
                'type_name' => $type['type_name'],
            ],
            [
                'description' => $type['description'],
            ]
        );
    }
}
}