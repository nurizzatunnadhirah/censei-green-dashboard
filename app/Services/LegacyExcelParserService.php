<?php

namespace App\Services;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LegacyExcelParserService
{
    public function parseWorksheet(Worksheet $sheet): array
    {
        $rows = $sheet->toArray(null, true, true, false);
        foreach ($rows as $index => $row) {
            if ($this->isSummaryHeader($row)) {
                return $this->parseSummary($rows, $index);
            }
        }
        return $this->parseSectioned($rows);
    }

    private function isSummaryHeader(array $row): bool
    {
        return preg_match('/organik|organic/i', trim((string)($row[0] ?? '')))
            && preg_match('/jenis|type/i', trim((string)($row[1] ?? '')))
            && preg_match('/total/i', trim((string)($row[2] ?? '')));
    }

    private function month(string $value): ?string
    {
        $value = trim($value);
        if (!preg_match('/^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Sept|Oct|Nov|Dec)[-\s](\d{2}|\d{4})$/i', $value, $matches)) {
            return null;
        }
        $name = strtolower($matches[1]) === 'sept' ? 'Sep' : ucfirst(strtolower($matches[1]));
        $year = (int)$matches[2];
        $year = $year < 100 ? 2000 + $year : $year;
        try {
            return Carbon::createFromFormat('!M-Y', "$name-$year")->format('Y-m-01');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function weight($value): ?float
    {
        if ($value === null || trim((string)$value) === '') return null;
        $value = str_replace([',', ' '], '', (string)$value);
        return is_numeric($value) && (float)$value > 0 ? (float)$value : null;
    }

    private function parseSummary(array $rows, int $headerIndex): array
    {
        $columns = [];
        foreach ($rows[$headerIndex] as $column => $value) {
            if ($column < 3) continue;
            $month = $this->month((string)($value ?? ''));
            if ($month && !in_array($month, $columns, true)) $columns[$column] = $month;
        }
        $records = [];
        for ($i = $headerIndex + 1; $i < count($rows); $i++) {
            $category = trim((string)($rows[$i][0] ?? ''));
            $type = trim((string)($rows[$i][1] ?? ''));
            if ($category === '' || $type === '') continue;
            foreach ($columns as $column => $date) {
                $weight = $this->weight($rows[$i][$column] ?? null);
                if ($weight === null) continue;
                $records[] = [
                    'location' => 'Unassigned (monthly summary)',
                    'waste_type' => $type,
                    'collection_date' => $date,
                    'weight' => $weight,
                    'source_category' => $category,
                ];
            }
        }
        return $records;
    }

    private function parseSectioned(array $rows): array
    {
        $records = [];
        foreach ($rows as $rowNumber => $row) {
            $heading = trim((string)($row[0] ?? ''));
            if ($heading === '') continue;
            $monthRow = $rows[$rowNumber + 1] ?? [];
            $monthColumns = [];
            $detected = [];
            foreach ($monthRow as $column => $header) {
                if ($column === 0) continue;
                $month = $this->month((string)($header ?? ''));
                if ($month === null || in_array($month, $detected, true)) continue;
                $detected[] = $month;
                $monthColumns[$column] = $month;
            }
            if (count($monthColumns) < 3) continue;
            $wasteType = trim(preg_replace('/\s*\(kg\)\s*$/i', '', $heading));
            for ($i = $rowNumber + 2; $i < count($rows); $i++) {
                $location = trim((string)($rows[$i][0] ?? ''));
                if (strcasecmp($location, 'Total') === 0) break;
                if ($location === '') continue;
                foreach ($monthColumns as $column => $date) {
                    $weight = $this->weight($rows[$i][$column] ?? null);
                    if ($weight === null) continue;
                    $records[] = [
                        'location' => $location,
                        'waste_type' => $wasteType,
                        'collection_date' => $date,
                        'weight' => $weight,
                    ];
                }
            }
        }
        return $records;
    }
}
