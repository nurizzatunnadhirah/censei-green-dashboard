<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [

            'FTKIP',
            'FKP',
            'Kolej Kediaman Satria Tuah',
            'Stor Pusat CENSEi & Unit Rekod Bendahari',
            'Program-program UTeM',
            'Masjid Sayyidina Abu Bakar UTeM',
            'TKJ',

        ];

        foreach ($locations as $location) {

            Location::updateOrCreate(
            ['location_name' => $location],
            []
       );

        }
    }
}