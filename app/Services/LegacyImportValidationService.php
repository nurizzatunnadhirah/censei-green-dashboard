<?php

namespace App\Services;

use App\Models\Location;
use App\Models\WasteType;

class LegacyImportValidationService
{
    public function validate(array $records): array
    {
        $locations = Location::query()
            ->get()
            ->keyBy(
                fn ($location) =>
                    strtolower(trim($location->location_name))
            );

        $wasteTypes = WasteType::query()
            ->get()
            ->keyBy(
                fn ($wasteType) =>
                    strtolower(trim($wasteType->type_name))
            );

        $locationAliases = [
            'satria' => 'Kolej Kediaman Satria Tuah',
            'program' => 'Program-program UTeM',
            'masjid' => 'Masjid Sayyidina Abu Bakar UTeM',
            'bendahari' => 'Stor Pusat CENSEi & Unit Rekod Bendahari',
        ];

        $validatedRecords = [];

        foreach ($records as $record) {
            $originalLocationName = trim($record['location']);

            $locationKey = strtolower($originalLocationName);

            if (isset($locationAliases[$locationKey])) {
                $locationName = $locationAliases[$locationKey];
            } else {
                $locationName = $originalLocationName;
            }

            $locationKey = strtolower(trim($locationName));

            $wasteTypeKey = strtolower(
                trim($record['waste_type'])
            );

            $locationModel = $locations->get($locationKey);
            $wasteTypeModel = $wasteTypes->get($wasteTypeKey);

            $errors = [];

            if (!$locationModel) {
                $errors[] = 'Location / Program not found';
            }

            if (!$wasteTypeModel) {
                $errors[] = 'Waste type not found';
            }

            if (!is_numeric($record['weight'])) {
                $errors[] = 'Invalid weight';
            }

            if (($record['weight'] ?? 0) <= 0) {
                $errors[] = 'Weight must be greater than zero';
            }

            $validatedRecords[] = [
                ...$record,

                // Show the official database name in the preview
                'location' => $locationModel?->location_name
                    ?? $originalLocationName,

                'location_id' => $locationModel?->id,
                'waste_type_id' => $wasteTypeModel?->id,

                'valid' => empty($errors),
                'errors' => $errors,
            ];
        }

        return $validatedRecords;
    }
}