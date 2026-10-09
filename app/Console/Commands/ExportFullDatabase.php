<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExportFullDatabase extends Command
{
    protected $signature = 'db:export-full';

    protected $description = 'Export actual Green Dashboard database records';

    public function handle(): int
    {
        $tables = [
            'faculties',
            'locations',
            'waste_categories',
            'waste_types',
            'users',
            'waste_records',
            'waste_files',
        ];

        $data = [];

        foreach ($tables as $table) {
            $rows = DB::table($table)
                ->orderBy('id')
                ->get()
                ->map(fn ($row) => (array) $row)
                ->all();

            // Never export authentication credentials or reset tokens.
            if ($table === 'users') {
                foreach ($rows as &$row) {
                    unset(
                        $row['password'],
                        $row['remember_token']
                    );
                }
                unset($row);
            }

            $data[$table] = $rows;

            $this->info(
                "{$table}: " . count($rows) . " records"
            );
        }

        $directory = database_path('data');

        File::ensureDirectoryExists($directory);

        $path = $directory . '/full_database.json';

        File::put(
            $path,
            json_encode(
                $data,
                JSON_PRETTY_PRINT |
                JSON_UNESCAPED_UNICODE |
                JSON_THROW_ON_ERROR
            )
        );

        $this->info('Database export completed.');
        $this->info("Saved to: {$path}");

        return self::SUCCESS;
    }
}
