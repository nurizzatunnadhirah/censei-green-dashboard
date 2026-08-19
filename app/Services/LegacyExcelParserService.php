<?php

namespace App\Services;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LegacyExcelParserService
{
    public function parseWorksheet(Worksheet $sheet): array
    {
        $rows = $sheet->toArray();

        $records = [];

        foreach ($rows as $rowNumber => $row) {
            $heading = trim((string) ($row[0] ?? ''));

            if ($heading === '') {
                continue;
            }

            /*
             * The row immediately after a waste-type heading
             * should contain the month columns.
             */
            $monthRow = $rows[$rowNumber + 1] ?? [];

            $monthColumns = [];
            $detectedMonths = [];

            foreach ($monthRow as $column => $header) {
                if ($column === 0) {
                    continue;
                }

                $header = trim((string) $header);

                /*
                 * Accept month formats such as:
                 *
                 * Nov-24
                 * Jan-25
                 * Sept-25
                 */
                if (!preg_match(
                    '/^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Sept|Oct|Nov|Dec)-\d{2}$/i',
                    $header
                )) {
                    continue;
                }

                /*
                 * Carbon accepts Sep rather than Sept.
                 */
                $normalisedMonth = preg_replace(
                    '/^Sept-/i',
                    'Sep-',
                    $header
                );

                $normalisedMonth = ucfirst(
                    strtolower($normalisedMonth)
                );

                /*
                 * Ignore duplicate month headings.
                 *
                 * This is needed because the WCO table has
                 * another Oct-25 heading in its total column.
                 */
                if (in_array($normalisedMonth, $detectedMonths, true)) {
                    continue;
                }

                $detectedMonths[] = $normalisedMonth;
                $monthColumns[$column] = $normalisedMonth;
            }

            /*
             * A valid waste-type section must be followed by
             * several month headings.
             */
            if (count($monthColumns) < 3) {
                continue;
            }

            /*
             * Remove "(kg)" from headings such as Paper (kg).
             *
             * Headings without "(kg)", such as Fabric and eWaste,
             * remain unchanged.
             */
            $wasteType = trim(
                preg_replace('/\s*\(kg\)\s*$/i', '', $heading)
            );

            /*
             * Location data begins two rows below the heading.
             */
            for ($i = $rowNumber + 2; $i < count($rows); $i++) {
                $location = trim(
                    (string) ($rows[$i][0] ?? '')
                );

                /*
                 * The Total row marks the end of the section.
                 */
                if (strcasecmp($location, 'Total') === 0) {
                    break;
                }

                if ($location === '') {
                    continue;
                }

                foreach ($monthColumns as $column => $month) {
                    $rawWeight = $rows[$i][$column] ?? null;

                    if ($rawWeight === null || $rawWeight === '') {
                        continue;
                    }

                    $cleanWeight = str_replace(
                        [',', ' '],
                        '',
                        (string) $rawWeight
                    );

                    if (!is_numeric($cleanWeight)) {
                        continue;
                    }

                    $weight = (float) $cleanWeight;

                    /*
                     * Ignore zero or negative values.
                     */
                    if ($weight <= 0) {
                        continue;
                    }

                    $collectionDate = Carbon::createFromFormat(
                        'M-y',
                        $month
                    )
                        ->startOfMonth()
                        ->format('Y-m-d');

                    $records[] = [
                        'location' => $location,
                        'waste_type' => $wasteType,
                        'collection_date' => $collectionDate,
                        'weight' => $weight,
                    ];
                }
            }
        }

        return $records;
    }
}