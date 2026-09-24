<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WasteRecord;
use App\Models\Location;
use App\Models\WasteType;
use App\Models\User;

class WasteRecordSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin.test@censei.local')->firstOrFail();
        $staff = User::where('email', 'staff.test@censei.local')->firstOrFail();

        $ftkip = Location::where('location_name', 'FTKIP')->firstOrFail();
        $fkp = Location::where('location_name', 'FKP')->firstOrFail();
        $kolej = Location::where(
            'location_name',
            'Kolej Kediaman Satria Tuah'
        )->firstOrFail();

        $stor = Location::where(
            'location_name',
            'Stor Pusat CENSEi & Unit Rekod Bendahari'
        )->firstOrFail();

        $program = Location::where(
            'location_name',
            'Program-program UTeM'
        )->firstOrFail();

        $foodWaste = WasteType::where(
            'type_name',
            'Food Waste'
        )->firstOrFail();

        $gardenWaste = WasteType::where(
            'type_name',
            'Garden Waste'
        )->firstOrFail();

        $plastic = WasteType::where(
            'type_name',
            'Plastic'
        )->firstOrFail();

        $paper = WasteType::where(
            'type_name',
            'Paper'
        )->firstOrFail();

        $glass = WasteType::where(
            'type_name',
            'Glass'
        )->firstOrFail();

        $metal = WasteType::where(
            'type_name',
            'Metal'
        )->firstOrFail();

        $battery = WasteType::where(
            'type_name',
            'Battery'
        )->firstOrFail();

        $ewaste = WasteType::where(
            'type_name',
            'eWaste'
        )->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Recycling / General Waste Records
        |--------------------------------------------------------------------------
        */

        $records = [
            [
                'location_id' => $ftkip->id,
                'waste_type_id' => $foodWaste->id,
                'recorded_by' => $staff->id,
                'actual_weight' => 25.50,
                'collection_method' => 'Manual Collection',
                'collection_date' => '2026-09-01',
                'recycled' => false,
                'remarks' => 'Sample organic waste record for testing.',
                'waste_stream' => 'Recycling',
                'vehicle_no' => null,
                'time_in' => null,
                'time_out' => null,
            ],

            [
                'location_id' => $fkp->id,
                'waste_type_id' => $plastic->id,
                'recorded_by' => $staff->id,
                'actual_weight' => 15.00,
                'collection_method' => 'Recycling Collection',
                'collection_date' => '2026-09-03',
                'recycled' => true,
                'remarks' => 'Sample recyclable plastic waste.',
                'waste_stream' => 'Recycling',
                'vehicle_no' => null,
                'time_in' => null,
                'time_out' => null,
            ],

            [
                'location_id' => $kolej->id,
                'waste_type_id' => $paper->id,
                'recorded_by' => $staff->id,
                'actual_weight' => 30.00,
                'collection_method' => 'Recycling Collection',
                'collection_date' => '2026-09-05',
                'recycled' => true,
                'remarks' => 'Sample paper and cardboard waste.',
                'waste_stream' => 'Recycling',
                'vehicle_no' => null,
                'time_in' => null,
                'time_out' => null,
            ],

            [
                'location_id' => $stor->id,
                'waste_type_id' => $battery->id,
                'recorded_by' => $admin->id,
                'actual_weight' => 5.00,
                'collection_method' => 'Scheduled Waste Collection',
                'collection_date' => '2026-09-08',
                'recycled' => false,
                'remarks' => 'Sample hazardous waste record.',
                'waste_stream' => 'Recycling',
                'vehicle_no' => null,
                'time_in' => null,
                'time_out' => null,
            ],

            [
                'location_id' => $program->id,
                'waste_type_id' => $glass->id,
                'recorded_by' => $staff->id,
                'actual_weight' => 12.00,
                'collection_method' => 'Recycling Collection',
                'collection_date' => '2026-09-10',
                'recycled' => true,
                'remarks' => 'Sample glass waste.',
                'waste_stream' => 'Recycling',
                'vehicle_no' => null,
                'time_in' => null,
                'time_out' => null,
            ],

            [
                'location_id' => $ftkip->id,
                'waste_type_id' => $gardenWaste->id,
                'recorded_by' => $staff->id,
                'actual_weight' => 18.00,
                'collection_method' => 'Manual Collection',
                'collection_date' => '2026-09-12',
                'recycled' => false,
                'remarks' => 'Sample garden waste record.',
                'waste_stream' => 'Recycling',
                'vehicle_no' => null,
                'time_in' => null,
                'time_out' => null,
            ],

            [
                'location_id' => $fkp->id,
                'waste_type_id' => $metal->id,
                'recorded_by' => $admin->id,
                'actual_weight' => 10.50,
                'collection_method' => 'Recycling Collection',
                'collection_date' => '2026-09-15',
                'recycled' => true,
                'remarks' => 'Sample metal waste.',
                'waste_stream' => 'Recycling',
                'vehicle_no' => null,
                'time_in' => null,
                'time_out' => null,
            ],

            [
                'location_id' => $stor->id,
                'waste_type_id' => $ewaste->id,
                'recorded_by' => $admin->id,
                'actual_weight' => 8.00,
                'collection_method' => 'E-Waste Collection',
                'collection_date' => '2026-09-18',
                'recycled' => false,
                'remarks' => 'Sample electronic waste record.',
                'waste_stream' => 'Recycling',
                'vehicle_no' => null,
                'time_in' => null,
                'time_out' => null,
            ],
        ];

        foreach ($records as $record) {
            WasteRecord::updateOrCreate(
                [
                    'location_id' => $record['location_id'],
                    'waste_type_id' => $record['waste_type_id'],
                    'collection_date' => $record['collection_date'],
                    'actual_weight' => $record['actual_weight'],
                ],
                $record
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Waste-to-Landfill Records
        |--------------------------------------------------------------------------
        */

        $landfillRecords = [
            [
                'location_id' => null,
                'waste_type_id' => null,
                'recorded_by' => $admin->id,
                'actual_weight' => 120.00,
                'collection_method' => 'Waste to Landfill',
                'collection_date' => '2026-09-07',
                'recycled' => false,
                'remarks' => 'Sample waste-to-landfill record.',
                'waste_stream' => 'Landfill',
                'vehicle_no' => 'TEST-WLF-001',
                'time_in' => '08:30:00',
                'time_out' => '09:15:00',
            ],

            [
                'location_id' => null,
                'waste_type_id' => null,
                'recorded_by' => $admin->id,
                'actual_weight' => 95.50,
                'collection_method' => 'Waste to Landfill',
                'collection_date' => '2026-09-17',
                'recycled' => false,
                'remarks' => 'Sample waste-to-landfill record.',
                'waste_stream' => 'Landfill',
                'vehicle_no' => 'TEST-WLF-002',
                'time_in' => '10:00:00',
                'time_out' => '10:45:00',
            ],
        ];

        foreach ($landfillRecords as $record) {
            WasteRecord::updateOrCreate(
                [
                    'collection_date' => $record['collection_date'],
                    'actual_weight' => $record['actual_weight'],
                    'waste_stream' => $record['waste_stream'],
                ],
                $record
            );
        }
    }
}