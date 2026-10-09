<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class FullDataSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/full_database.json');

        if (!File::exists($path)) {
            throw new RuntimeException(
                'Full database export not found: ' . $path
            );
        }

        // Never run this seeder on the original database.
        $database = DB::connection()->getDatabaseName();

        if (strcasecmp($database, 'CENSEI_GreenDashboard') === 0) {
            throw new RuntimeException(
                'Cannot seed the original production/development database.'
            );
        }

        $data = json_decode(
            File::get($path),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        // Verify the expected number of exported records.
$expectedCounts = [
    'faculties' => 8,
    'locations' => 8,
    'waste_categories' => 4,
    'waste_types' => 16,
    'users' => 5,
    'waste_records' => 610,
    'waste_files' => 0,
];

foreach ($expectedCounts as $table => $expected) {
    $actual = count($data[$table] ?? []);

    if ($actual !== $expected) {
        throw new RuntimeException(
            "Data validation failed for {$table}. " .
            "Expected {$expected}, found {$actual}."
        );
    }
}

// Ensure every waste record references an exported user.
$userIds = array_column($data['users'], 'id');

foreach ($data['waste_records'] as $record) {
    if (!in_array($record['recorded_by'], $userIds)) {
        throw new RuntimeException(
            "Waste record {$record['id']} references " .
            "a missing user."
        );
    }
}

        $tables = [
            'faculties',
            'locations',
            'waste_categories',
            'waste_types',
            'users',
            'waste_records',
        ];

        // Import only into empty tables.
        foreach ($tables as $table) {
            if (DB::table($table)->exists()) {
                throw new RuntimeException(
                    "Table {$table} is not empty. Aborting import."
                );
            }
        }

        DB::transaction(function () use ($data, $tables) {

            foreach ($tables as $table) {

                $rows = $data[$table] ?? [];

                if (empty($rows)) {
                    continue;
                }

                if ($table === 'users') {
                    foreach ($rows as &$row) {
                        // Historical users cannot log in.
                        $row['password'] = Hash::make(Str::random(64));
                        $row['remember_token'] = null;
                        $row['status'] = 'Inactive';
                    }
                    unset($row);
                }

                $tableName = '[' . $table . ']';

                DB::unprepared(
                    "SET IDENTITY_INSERT {$tableName} ON"
                );

                try {
                    foreach (array_chunk($rows, 100) as $chunk) {
                        DB::table($table)->insert($chunk);
                    }
                } finally {
                    DB::unprepared(
                        "SET IDENTITY_INSERT {$tableName} OFF"
                    );
                }

                $this->command?->info(
                    "{$table}: " . count($rows) . " imported"
                );
            }

        });

        $this->command?->info(
            'Full database import completed successfully.'
        );
    }
}
