<?php

namespace App\Http\Controllers;

use App\Models\WasteRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Smalot\PdfParser\Parser;

class LandfillImportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return view('landfill.index');
    }


    /*
    |--------------------------------------------------------------------------
    | Upload
    |--------------------------------------------------------------------------
    |
    | Normal text PDF:
    |     Parse directly.
    |
    | Scanned PDF:
    |     Convert manually to Excel, then upload XLSX/XLS.
    |
    */

    public function upload(Request $request)
    {
        $request->validate([
            'landfill_file' =>
                'required|file|mimes:pdf,xlsx,xls|max:20480',
        ]);

        $file = $request->file('landfill_file');

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        if ($extension === 'pdf') {
            return $this->processPdf(
                $file->getRealPath()
            );
        }

        if (
            $extension === 'xlsx'
            ||
            $extension === 'xls'
        ) {
            return $this->processExcel(
                $file->getRealPath()
            );
        }

        return back()->withErrors([
            'landfill_file' =>
                'Unsupported file format.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PDF PROCESSOR
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | This intentionally uses LINE-BY-LINE parsing.
    |
    | Your original landfill PDFs already expose records cleanly as:
    |
    | 9/2/2025 MDJ2156 17:41 18:23 2.03
    |
    | Normalizing the whole page caused inaccurate extraction.
    |
    */

    private function processPdf(string $filePath)
{
    try {

        $parser = new Parser();

        $pdf = $parser->parseFile(
            $filePath
        );

    } catch (\Throwable $e) {

        return back()->withErrors([
            'landfill_file' =>
                'This PDF could not be read. '
                . 'If it is a scanned PDF, convert it to Excel first.',
        ]);
    }


    $records = [];
    $pageSummaries = [];


    foreach (
        $pdf->getPages()
        as $pageIndex => $page
    ) {

        $pageNumber = $pageIndex + 1;


        /*
        |--------------------------------------------------------------------------
        | Extract Page Text
        |--------------------------------------------------------------------------
        */

        try {

            $text = trim(
                $page->getText()
            );

        } catch (\Throwable $e) {

            continue;
        }


        if ($text === '') {
            continue;
        }


        /*
        |--------------------------------------------------------------------------
        | Detect Expected Month + Year
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | Data Waste Collection for April 2026
        |
        | This lets us support BOTH:
        |
        | 1/4/2026  = 1 April 2026
        | 4/1/2026  = 1 April 2026
        |
        */

        $pagePeriod =
            $this->detectPdfPeriod(
                $text
            );

        $expectedMonth =
            $pagePeriod['month'];

        $expectedYear =
            $pagePeriod['year'];


        /*
        |--------------------------------------------------------------------------
        | Split Original Text Into Lines
        |--------------------------------------------------------------------------
        */

        $lines = preg_split(
            '/\r\n|\r|\n/',
            $text
        );


        $pageRecords = [];


        /*
        |--------------------------------------------------------------------------
        | Parse Collection Rows
        |--------------------------------------------------------------------------
        */

        foreach ($lines as $line) {

            $line = trim($line);


            if ($line === '') {
                continue;
            }


            /*
             * Normalize whitespace ONLY within this line.
             */
            $line = preg_replace(
                '/\s+/',
                ' ',
                $line
            );


            /*
             * Expected:
             *
             * 1/4/2026 MDJ2156 17:06 17:39 0.60
             *
             * OR:
             *
             * 4/1/2026 MDJ2156 17:06 17:39 0.60
             */
            if (!preg_match(
                '/^
                    (\d{1,2}\/\d{1,2}\/\d{4})
                    \s+
                    ([A-Z0-9]+)
                    \s+
                    (\d{1,2}:\d{2})
                    \s+
                    (\d{1,2}:\d{2})
                    \s+
                    (\d+(?:[.,]\d+)?)
                $/ix',
                $line,
                $match
            )) {

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Parse Date Intelligently
            |--------------------------------------------------------------------------
            */

            $collectionDate =
                $this->parsePdfDate(
                    $match[1],
                    $expectedMonth,
                    $expectedYear
                );


            if (!$collectionDate) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Vehicle
            |--------------------------------------------------------------------------
            */

            $vehicleNo =
                strtoupper(
                    trim($match[2])
                );


            /*
            |--------------------------------------------------------------------------
            | Time
            |--------------------------------------------------------------------------
            */

            $timeIn =
                $this->normalizeTimeString(
                    $match[3]
                );

            $timeOut =
                $this->normalizeTimeString(
                    $match[4]
                );


            if (
                !$timeIn
                ||
                !$timeOut
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Weight
            |--------------------------------------------------------------------------
            */

            $weightMt =
                $this->normalizeWeight(
                    $match[5]
                );


            if (
                $weightMt === null
                ||
                $weightMt <= 0
            ) {
                continue;
            }


            $record = [

                'page_number' =>
                    $pageNumber,

                'collection_date' =>
                    $collectionDate,

                'vehicle_no' =>
                    $vehicleNo,

                'time_in' =>
                    $timeIn,

                'time_out' =>
                    $timeOut,

                'weight_mt' =>
                    $weightMt,

                'weight_kg' =>
                    round(
                        $weightMt * 1000,
                        2
                    ),
            ];


            $records[] =
                $record;

            $pageRecords[] =
                $record;
        }


        /*
        |--------------------------------------------------------------------------
        | Skip Non-Data / Blank Pages
        |--------------------------------------------------------------------------
        */

        if (empty($pageRecords)) {
            continue;
        }


        /*
        |--------------------------------------------------------------------------
        | Calculated Total
        |--------------------------------------------------------------------------
        */

        $calculatedTotalMt =
            round(
                collect($pageRecords)
                    ->sum('weight_mt'),
                2
            );


        /*
        |--------------------------------------------------------------------------
        | Reported Total
        |--------------------------------------------------------------------------
        */

        $reportedTotalMt =
            $this->findPdfReportedTotal(
                $lines,
                $calculatedTotalMt
            );


        /*
        |--------------------------------------------------------------------------
        | Table Summary
        |--------------------------------------------------------------------------
        */

        $pageSummaries[] = [

            'page_number' =>
                $pageNumber,

            'record_count' =>
                count($pageRecords),

            'reported_total_mt' =>
                $reportedTotalMt,

            'calculated_total_mt' =>
                $calculatedTotalMt,

            'total_matches' =>
                $reportedTotalMt !== null
                    ? abs(
                        $reportedTotalMt
                        - $calculatedTotalMt
                    ) < 0.01
                    : null,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Nothing Detected
    |--------------------------------------------------------------------------
    */

    if (empty($records)) {

        return back()->withErrors([
            'landfill_file' =>
                'No landfill collection records were detected. '
                . 'If this is a scanned PDF, convert it to Excel first.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Preview
    |--------------------------------------------------------------------------
    */

    return view(
        'landfill.preview',
        [
            'records' =>
                $records,

            'pageSummaries' =>
                $pageSummaries,

            'totalRecords' =>
                count($records),

            'sourceType' =>
                'PDF',
        ]
    );
}


    /*
    |--------------------------------------------------------------------------
    | PDF DATE
    |--------------------------------------------------------------------------
    |
    | Original text PDFs use:
    |
    | M/D/Y
    |
    */

    private function detectPdfPeriod(
    string $text
): array {

    $monthMap = [

        /*
         * English
         */
        'january' =>
            1,

        'february' =>
            2,

        'march' =>
            3,

        'april' =>
            4,

        'may' =>
            5,

        'june' =>
            6,

        'july' =>
            7,

        'august' =>
            8,

        'september' =>
            9,

        'october' =>
            10,

        'november' =>
            11,

        'december' =>
            12,


        /*
         * Malay
         */
        'januari' =>
            1,

        'februari' =>
            2,

        'mac' =>
            3,

        'mei' =>
            5,

        'jun' =>
            6,

        'julai' =>
            7,

        'ogos' =>
            8,

        'oktober' =>
            10,

        'disember' =>
            12,
    ];


    /*
    |--------------------------------------------------------------------------
    | Look for page title
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | Data Waste Collection for April 2026
    | Data Waste Collection for Mac 2026
    |
    */

    foreach (
        $monthMap
        as $monthName => $monthNumber
    ) {

        if (preg_match(
            '/\b'
            . preg_quote(
                $monthName,
                '/'
            )
            . '\s+(\d{4})\b/i',
            $text,
            $match
        )) {

            return [

                'month' =>
                    $monthNumber,

                'year' =>
                    (int) $match[1],
            ];
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Fallback: Start/Last Date
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | Start Date: 01/04/2026
    |
    */

    if (preg_match(
        '/Start\s*Date\s*:\s*(\d{1,2})\/(\d{1,2})\/(\d{4})/i',
        $text,
        $match
    )) {

        /*
         * Header dates in these documents are displayed
         * as D/M/Y.
         */
        return [

            'month' =>
                (int) $match[2],

            'year' =>
                (int) $match[3],
        ];
    }


    return [

        'month' =>
            null,

        'year' =>
            null,
    ];
}
    private function parsePdfDate(
    string $value,
    ?int $expectedMonth = null,
    ?int $expectedYear = null
): ?string {

    $value = trim($value);

    $parts = explode('/', $value);

    if (count($parts) !== 3) {
        return null;
    }

    $first = (int) $parts[0];
    $second = (int) $parts[1];
    $year = (int) $parts[2];


    /*
    |--------------------------------------------------------------------------
    | Validate Year
    |--------------------------------------------------------------------------
    */

    if (
        $expectedYear !== null
        &&
        $year !== $expectedYear
    ) {
        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | D/M/Y
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | April page:
    | 30/4/2026
    |
    | Second number = expected month (4)
    |
    */

    if (
        $expectedMonth !== null
        &&
        $second === $expectedMonth
    ) {

        $day = $first;
        $month = $second;

        if (
            checkdate(
                $month,
                $day,
                $year
            )
        ) {

            return sprintf(
                '%04d-%02d-%02d',
                $year,
                $month,
                $day
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | M/D/Y
    |--------------------------------------------------------------------------
    |
    | Some of your other PDFs extract dates as:
    |
    | 4/30/2026
    |
    | First number = expected month (4)
    |
    */

    if (
        $expectedMonth !== null
        &&
        $first === $expectedMonth
    ) {

        $month = $first;
        $day = $second;

        if (
            checkdate(
                $month,
                $day,
                $year
            )
        ) {

            return sprintf(
                '%04d-%02d-%02d',
                $year,
                $month,
                $day
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Unambiguous D/M/Y Fallback
    |--------------------------------------------------------------------------
    |
    | 30/4/2026
    |
    | 30 cannot be a month.
    |
    */

    if ($first > 12) {

        $day = $first;
        $month = $second;

        if (
            checkdate(
                $month,
                $day,
                $year
            )
        ) {

            return sprintf(
                '%04d-%02d-%02d',
                $year,
                $month,
                $day
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Unambiguous M/D/Y Fallback
    |--------------------------------------------------------------------------
    |
    | 4/30/2026
    |
    | 30 cannot be a month.
    |
    */

    if ($second > 12) {

        $month = $first;
        $day = $second;

        if (
            checkdate(
                $month,
                $day,
                $year
            )
        ) {

            return sprintf(
                '%04d-%02d-%02d',
                $year,
                $month,
                $day
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Final D/M/Y Fallback
    |--------------------------------------------------------------------------
    |
    | Only used if the page title/month could not be detected.
    |
    */

    if (
        checkdate(
            $second,
            $first,
            $year
        )
    ) {

        return sprintf(
            '%04d-%02d-%02d',
            $year,
            $second,
            $first
        );
    }


    return null;
}


    /*
    |--------------------------------------------------------------------------
    | FIND PDF REPORTED TOTAL
    |--------------------------------------------------------------------------
    */

    private function findPdfReportedTotal(
        array $lines,
        float $calculatedTotalMt
    ): ?float {

        /*
         * Search from bottom upwards because TOTAL is near
         * the end of each table.
         */
        foreach (
            array_reverse($lines)
            as $line
        ) {

            $line = trim($line);


            /*
             * Only standalone decimal/integer values.
             *
             * Examples:
             *
             * 32.20
             * 38.19
             * 41.50
             */
            if (!preg_match(
                '/^\d+(?:[.,]\d+)?$/',
                $line
            )) {
                continue;
            }


            $possibleTotal =
                $this->normalizeWeight(
                    $line
                );


            if ($possibleTotal === null) {
                continue;
            }


            /*
             * The reported total must match the row sum.
             *
             * This prevents years like 2025 or 2026
             * from ever being selected.
             */
            if (
                abs(
                    $possibleTotal
                    - $calculatedTotalMt
                ) < 0.01
            ) {

                return $possibleTotal;
            }
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | EXCEL PROCESSOR
    |--------------------------------------------------------------------------
    |
    | Only intended as fallback for scanned PDFs converted to Excel.
    |
    */

    private function processExcel(string $filePath)
{
    try {

        $spreadsheet = IOFactory::load(
            $filePath
        );

    } catch (\Throwable $e) {

        return back()->withErrors([
            'landfill_file' =>
                'The Excel file could not be read.',
        ]);
    }


    $records = [];
    $pageSummaries = [];

    $tableNumber = 0;


    foreach (
        $spreadsheet->getWorksheetIterator()
        as $sheet
    ) {

        $rows = $sheet->toArray(
            null,
            true,
            true,
            false
        );


        /*
        |--------------------------------------------------------------------------
        | Find Header
        |--------------------------------------------------------------------------
        */

        $headerRowIndex =
            $this->findExcelHeaderRow(
                $rows
            );


        if ($headerRowIndex === null) {
            continue;
        }


        /*
        |--------------------------------------------------------------------------
        | Detect Columns Per Sheet
        |--------------------------------------------------------------------------
        */

        $columns =
            $this->detectExcelColumns(
                $rows,
                $headerRowIndex
            );


        if (
            $columns['date'] === null
            ||
            $columns['vehicle'] === null
            ||
            $columns['time_in'] === null
            ||
            $columns['time_out'] === null
            ||
            $columns['weight'] === null
        ) {
            continue;
        }


        $tableNumber++;

        $tableRecords = [];

        $reportedTotalMt = null;


        /*
        |--------------------------------------------------------------------------
        | Detect Expected Month / Year From Sheet Heading
        |--------------------------------------------------------------------------
        */

        $sheetText = '';

        for (
            $r = 0;
            $r < min(4, count($rows));
            $r++
        ) {

            $sheetText .= ' ' . implode(
                ' ',
                array_map(
                    fn ($value) =>
                        trim(
                            (string) $value
                        ),
                    $rows[$r]
                )
            );
        }


        $period =
            $this->detectPdfPeriod(
                $sheetText
            );


        $expectedMonth =
            $period['month'];

        $expectedYear =
            $period['year'];


        /*
        |--------------------------------------------------------------------------
        | Process Every Row
        |--------------------------------------------------------------------------
        */

        for (
            $i = $headerRowIndex + 1;
            $i < count($rows);
            $i++
        ) {

            $row = $rows[$i];


            $textCells = array_map(
                fn ($value) =>
                    trim(
                        (string) $value
                    ),
                $row
            );


            $combinedRow =
                strtolower(
                    implode(
                        ' ',
                        $textCells
                    )
                );


            /*
             * Skip secondary header row.
             */
            if (
                str_contains(
                    $combinedRow,
                    'vehicle'
                )
                ||
                (
                    str_contains(
                        $combinedRow,
                        'time'
                    )
                    &&
                    str_contains(
                        $combinedRow,
                        'weight'
                    )
                )
            ) {

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Total Row
            |--------------------------------------------------------------------------
            */

            if (
                str_contains(
                    $combinedRow,
                    'total'
                )
            ) {

                $reportedTotalMt =
                    $this->findNumericTotal(
                        $row
                    );

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Values
            |--------------------------------------------------------------------------
            */

            $dateValue =
                $row[
                    $columns['date']
                ] ?? null;

            $vehicleValue =
                $row[
                    $columns['vehicle']
                ] ?? null;

            $timeInValue =
                $row[
                    $columns['time_in']
                ] ?? null;

            $timeOutValue =
                $row[
                    $columns['time_out']
                ] ?? null;

            $weightValue =
                $row[
                    $columns['weight']
                ] ?? null;


            /*
             * Must at least look like
             * a landfill record.
             */
            $vehicleNo =
                strtoupper(
                    trim(
                        (string) $vehicleValue
                    )
                );


            if (
                $vehicleNo === ''
                ||
                !preg_match(
                    '/^[A-Z]{2,4}\d{3,6}$/',
                    $vehicleNo
                )
            ) {

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Date
            |--------------------------------------------------------------------------
            */

            $collectionDate =
                $this->parseExcelDate(
                    $dateValue,
                    $expectedMonth,
                    $expectedYear
                );


            if (!$collectionDate) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Time
            |--------------------------------------------------------------------------
            */

            $timeIn =
                $this->parseExcelTime(
                    $timeInValue
                );

            $timeOut =
                $this->parseExcelTime(
                    $timeOutValue
                );


            if (
                !$timeIn
                ||
                !$timeOut
            ) {

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Weight
            |--------------------------------------------------------------------------
            */

            $weightMt =
                $this->normalizeWeight(
                    $weightValue
                );


            if (
                $weightMt === null
                ||
                $weightMt <= 0
            ) {

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Record
            |--------------------------------------------------------------------------
            */

            $record = [

                'page_number' =>
                    $tableNumber,

                'source_sheet' =>
                    $sheet->getTitle(),

                'collection_date' =>
                    $collectionDate,

                'vehicle_no' =>
                    $vehicleNo,

                'time_in' =>
                    $timeIn,

                'time_out' =>
                    $timeOut,

                'weight_mt' =>
                    $weightMt,

                'weight_kg' =>
                    round(
                        $weightMt * 1000,
                        2
                    ),
            ];


            $records[] =
                $record;

            $tableRecords[] =
                $record;
        }


        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        if (empty($tableRecords)) {
            continue;
        }


        $calculatedTotalMt =
            round(
                collect(
                    $tableRecords
                )->sum(
                    'weight_mt'
                ),
                2
            );


        /*
         * Some converted Excel files do not preserve
         * the TOTAL row correctly.
         *
         * If no total was found, leave it null.
         */
        $pageSummaries[] = [

            'page_number' =>
                $tableNumber,

            'source_sheet' =>
                $sheet->getTitle(),

            'record_count' =>
                count(
                    $tableRecords
                ),

            'reported_total_mt' =>
                $reportedTotalMt,

            'calculated_total_mt' =>
                $calculatedTotalMt,

            'total_matches' =>
                $reportedTotalMt !== null
                    ? abs(
                        $reportedTotalMt
                        - $calculatedTotalMt
                    ) < 0.01
                    : null,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Nothing Detected
    |--------------------------------------------------------------------------
    */

    if (empty($records)) {

        return back()->withErrors([
            'landfill_file' =>
                'No landfill collection records were detected in the Excel file.',
        ]);
    }


    return view(
        'landfill.preview',
        [
            'records' =>
                $records,

            'pageSummaries' =>
                $pageSummaries,

            'totalRecords' =>
                count($records),

            'sourceType' =>
                'Excel',
        ]
    );
}


    /*
    |--------------------------------------------------------------------------
    | FIND EXCEL HEADER
    |--------------------------------------------------------------------------
    */

    private function findExcelHeaderRow(
        array $rows
    ): ?int {

        foreach (
            $rows
            as $index => $row
        ) {

            $combined = strtolower(
                implode(
                    ' ',
                    array_map(
                        fn ($value) =>
                            trim(
                                (string) $value
                            ),
                        $row
                    )
                )
            );


            if (
                str_contains(
                    $combined,
                    'date'
                )
                &&
                str_contains(
                    $combined,
                    'vehicle'
                )
            ) {

                return $index;
            }
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | DETECT EXCEL COLUMNS
    |--------------------------------------------------------------------------
    */

    private function detectExcelColumns(
        array $rows,
        int $headerRowIndex
    ): array {

        $result = [

            'date' =>
                null,

            'vehicle' =>
                null,

            'time_in' =>
                null,

            'time_out' =>
                null,

            'weight' =>
                null,
        ];


        $maxColumns = 0;


        /*
         * Inspect header + following two rows.
         */
        for (
            $r = $headerRowIndex;
            $r <= min(
                $headerRowIndex + 2,
                count($rows) - 1
            );
            $r++
        ) {

            $maxColumns = max(
                $maxColumns,
                count($rows[$r])
            );
        }


        for (
            $column = 0;
            $column < $maxColumns;
            $column++
        ) {

            $parts = [];


            for (
                $r = $headerRowIndex;
                $r <= min(
                    $headerRowIndex + 2,
                    count($rows) - 1
                );
                $r++
            ) {

                $value = strtolower(
                    trim(
                        (string)
                        (
                            $rows[$r][$column]
                            ?? ''
                        )
                    )
                );


                if ($value !== '') {
                    $parts[] =
                        $value;
                }
            }


            $header = implode(
                ' ',
                $parts
            );


            if (
                $result['date'] === null
                &&
                str_contains(
                    $header,
                    'date'
                )
            ) {

                $result['date'] =
                    $column;
            }


            if (
                $result['vehicle'] === null
                &&
                str_contains(
                    $header,
                    'vehicle'
                )
            ) {

                $result['vehicle'] =
                    $column;
            }


            if (
                $result['weight'] === null
                &&
                str_contains(
                    $header,
                    'weight'
                )
            ) {

                $result['weight'] =
                    $column;
            }


            if (
                $result['time_in'] === null
                &&
                preg_match(
                    '/\bin\b/i',
                    $header
                )
            ) {

                $result['time_in'] =
                    $column;
            }


            if (
                $result['time_out'] === null
                &&
                preg_match(
                    '/\bout\b/i',
                    $header
                )
            ) {

                $result['time_out'] =
                    $column;
            }
        }


        /*
         * Standard landfill layout fallback:
         *
         * Date | Vehicle | In | Out | Weight
         */
        if (
            $result['date'] !== null
            &&
            $result['vehicle'] !== null
            &&
            $result['weight'] !== null
        ) {

            if (
                $result['time_in'] === null
            ) {

                $result['time_in'] =
                    $result['vehicle'] + 1;
            }


            if (
                $result['time_out'] === null
            ) {

                $result['time_out'] =
                    $result['vehicle'] + 2;
            }
        }


        return $result;
    }


    /*
    |--------------------------------------------------------------------------
    | PARSE EXCEL DATE
    |--------------------------------------------------------------------------
    */

    private function parseExcelDate(
    $value,
    ?int $expectedMonth = null,
    ?int $expectedYear = null
): ?string {

    if (
        $value === null
        ||
        $value === ''
    ) {

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Actual DateTime object
    |--------------------------------------------------------------------------
    */

    if (
        $value instanceof
        \DateTimeInterface
    ) {

        return $value->format(
            'Y-m-d'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Excel Serial Date
    |--------------------------------------------------------------------------
    */

    if (
        is_numeric($value)
        &&
        (float) $value > 1000
    ) {

        try {

            return ExcelDate
                ::excelToDateTimeObject(
                    $value
                )
                ->format(
                    'Y-m-d'
                );

        } catch (\Throwable $e) {

            return null;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Clean OCR Text Date
    |--------------------------------------------------------------------------
    */

    $value =
        trim(
            (string) $value
        );


    /*
     * Remove curly quotes, apostrophes,
     * spaces and accidental punctuation.
     *
     * Example:
     *
     * 8/9/2025’
     */
    $value = str_replace(
        [
            '’',
            '‘',
            "'",
            '"',
            ' ',
        ],
        '',
        $value
    );


    /*
     * OCR occasionally creates:
     *
     * 9/3/2.026
     *
     * Convert year part back to:
     *
     * 2026
     */
    $value = preg_replace(
        '/(\d{1,2})\/(\d{1,2})\/(\d)\.(\d{3})/',
        '$1/$2/$3$4',
        $value
    );


    /*
     * OCR may read:
     *
     * 202G
     *
     * as the year.
     *
     * G at the end is usually 6.
     */
    $value = preg_replace(
        '/\/(\d{3})G$/i',
        '/$16',
        $value
    );


    /*
    |--------------------------------------------------------------------------
    | Try D/M/Y and M/D/Y Based on Expected Month
    |--------------------------------------------------------------------------
    */

    $parts = explode(
        '/',
        $value
    );


    if (count($parts) !== 3) {
        return null;
    }


    $first =
        (int) $parts[0];

    $second =
        (int) $parts[1];

    $year =
        (int) $parts[2];


    if (
        $expectedYear !== null
        &&
        $year !== $expectedYear
    ) {

        return null;
    }


    /*
     * D/M/Y
     */
    if (
        $expectedMonth !== null
        &&
        $second === $expectedMonth
    ) {

        if (
            checkdate(
                $second,
                $first,
                $year
            )
        ) {

            return sprintf(
                '%04d-%02d-%02d',
                $year,
                $second,
                $first
            );
        }
    }


    /*
     * M/D/Y
     */
    if (
        $expectedMonth !== null
        &&
        $first === $expectedMonth
    ) {

        if (
            checkdate(
                $first,
                $second,
                $year
            )
        ) {

            return sprintf(
                '%04d-%02d-%02d',
                $year,
                $first,
                $second
            );
        }
    }


    /*
     * Unambiguous D/M/Y.
     */
    if (
        $first > 12
        &&
        checkdate(
            $second,
            $first,
            $year
        )
    ) {

        return sprintf(
            '%04d-%02d-%02d',
            $year,
            $second,
            $first
        );
    }


    /*
     * Unambiguous M/D/Y.
     */
    if (
        $second > 12
        &&
        checkdate(
            $first,
            $second,
            $year
        )
    ) {

        return sprintf(
            '%04d-%02d-%02d',
            $year,
            $first,
            $second
        );
    }


    return null;
}


    /*
    |--------------------------------------------------------------------------
    | PARSE D/M/Y
    |--------------------------------------------------------------------------
    */

    private function parseDateString(
        string $value
    ): ?string {

        $value = trim($value);


        try {

            $date =
                Carbon::createFromFormat(
                    '!j/n/Y',
                    $value
                );


            $parts = explode(
                '/',
                $value
            );


            if (count($parts) !== 3) {
                return null;
            }


            if (
                (int) $date->day
                    !== (int) $parts[0]
                ||
                (int) $date->month
                    !== (int) $parts[1]
                ||
                (int) $date->year
                    !== (int) $parts[2]
            ) {

                return null;
            }


            return $date->format(
                'Y-m-d'
            );

        } catch (\Throwable $e) {

            return null;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PARSE EXCEL TIME
    |--------------------------------------------------------------------------
    */

    private function parseExcelTime(
    $value
): ?string {

    if (
        $value === null
        ||
        $value === ''
    ) {

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | DateTime / Time Object
    |--------------------------------------------------------------------------
    */

    if (
        $value instanceof
        \DateTimeInterface
    ) {

        return $value->format(
            'H:i'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Normal Excel Fraction
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | 0.73958
    |
    */

    if (
        is_numeric($value)
        &&
        (float) $value >= 0
        &&
        (float) $value < 1
    ) {

        try {

            return ExcelDate
                ::excelToDateTimeObject(
                    $value
                )
                ->format(
                    'H:i'
                );

        } catch (\Throwable $e) {

            return null;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PDF-to-Excel Decimal Time
    |--------------------------------------------------------------------------
    |
    | Some converted cells become:
    |
    | 18.23
    |
    | But this means:
    |
    | 18:23
    |
    | NOT decimal 18.23 hours.
    |
    */

    if (
        is_numeric($value)
        &&
        (float) $value >= 1
        &&
        (float) $value < 24
    ) {

        $string =
            (string) $value;


        if (str_contains(
            $string,
            '.'
        )) {

            [$hour, $minute] =
                array_pad(
                    explode(
                        '.',
                        $string,
                        2
                    ),
                    2,
                    '0'
                );


            /*
             * Ensure:
             *
             * 18.04 → 18:04
             */
            $minute =
                str_pad(
                    $minute,
                    2,
                    '0',
                    STR_PAD_RIGHT
                );


            $hour =
                (int) $hour;

            $minute =
                (int) substr(
                    $minute,
                    0,
                    2
                );


            if (
                $hour <= 23
                &&
                $minute <= 59
            ) {

                return sprintf(
                    '%02d:%02d',
                    $hour,
                    $minute
                );
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Normal Text Time
    |--------------------------------------------------------------------------
    */

    return $this->normalizeTimeString(
        trim(
            (string) $value
        )
    );
}


    /*
    |--------------------------------------------------------------------------
    | NORMALIZE TIME
    |--------------------------------------------------------------------------
    */

    private function normalizeTimeString(
        string $value
    ): ?string {

        $value = trim($value);


        /*
         * 17:32
         * 17:32:00
         */
        if (preg_match(
            '/^(\d{1,2}):(\d{2})(?::\d{2})?$/',
            $value,
            $matches
        )) {

            $hour =
                (int) $matches[1];

            $minute =
                (int) $matches[2];


            if (
                $hour >= 0
                &&
                $hour <= 23
                &&
                $minute >= 0
                &&
                $minute <= 59
            ) {

                return sprintf(
                    '%02d:%02d',
                    $hour,
                    $minute
                );
            }
        }


        /*
         * Excel conversion may produce:
         *
         * 1732
         *
         * Convert to:
         *
         * 17:32
         */
        if (preg_match(
            '/^(\d{2})(\d{2})$/',
            $value,
            $matches
        )) {

            $hour =
                (int) $matches[1];

            $minute =
                (int) $matches[2];


            if (
                $hour >= 0
                &&
                $hour <= 23
                &&
                $minute >= 0
                &&
                $minute <= 59
            ) {

                return sprintf(
                    '%02d:%02d',
                    $hour,
                    $minute
                );
            }
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZE WEIGHT
    |--------------------------------------------------------------------------
    */

    private function normalizeWeight(
        $value
    ): ?float {

        if (
            $value === null
            ||
            $value === ''
        ) {

            return null;
        }


        $value = trim(
            (string) $value
        );


        $value = str_replace(
            ',',
            '.',
            $value
        );


        $value = preg_replace(
            '/[^0-9.\-]/',
            '',
            $value
        );


        if (
            $value === ''
            ||
            !is_numeric($value)
        ) {

            return null;
        }


        return (float) $value;
    }


    /*
    |--------------------------------------------------------------------------
    | FIND EXCEL TOTAL
    |--------------------------------------------------------------------------
    */

    private function findNumericTotal(
        array $row
    ): ?float {

        foreach (
            array_reverse($row)
            as $value
        ) {

            $number =
                $this->normalizeWeight(
                    $value
                );


            if (
                $number !== null
                &&
                $number > 0
            ) {

                return $number;
            }
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | BUILD SUMMARY
    |--------------------------------------------------------------------------
    */

    private function makeSummary(
        int $number,
        array $records,
        ?float $reportedTotalMt
    ): array {

        $calculatedTotalMt =
            round(
                collect($records)
                    ->sum('weight_mt'),
                2
            );


        return [

            'page_number' =>
                $number,

            'record_count' =>
                count($records),

            'reported_total_mt' =>
                $reportedTotalMt,

            'calculated_total_mt' =>
                $calculatedTotalMt,

            'total_matches' =>
                $reportedTotalMt !== null
                    ? abs(
                        $reportedTotalMt
                        - $calculatedTotalMt
                    ) < 0.01
                    : null,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | IMPORT
    |--------------------------------------------------------------------------
    */

    public function import(Request $request)
    {
        $records = $request->input(
            'records',
            []
        );


        $imported = 0;
        $duplicates = 0;
        $skipped = 0;
        $failed = 0;

        $skipDetails = [];


        DB::beginTransaction();


        try {

            foreach ($records as $record) {

                /*
                |--------------------------------------------------------------------------
                | Validate Required Fields
                |--------------------------------------------------------------------------
                */

                if (
                    empty(
                        $record[
                            'collection_date'
                        ]
                    )
                    ||
                    empty(
                        $record[
                            'vehicle_no'
                        ]
                    )
                    ||
                    empty(
                        $record[
                            'time_in'
                        ]
                    )
                    ||
                    empty(
                        $record[
                            'time_out'
                        ]
                    )
                    ||
                    !isset(
                        $record[
                            'weight_kg'
                        ]
                    )
                ) {

                    $skipped++;


                    $skipDetails[] = [

                        'reason' =>
                            'Invalid landfill record',

                        'collection_date' =>
                            $record[
                                'collection_date'
                            ] ?? null,

                        'vehicle_no' =>
                            $record[
                                'vehicle_no'
                            ] ?? null,

                        'time_in' =>
                            $record[
                                'time_in'
                            ] ?? null,

                        'time_out' =>
                            $record[
                                'time_out'
                            ] ?? null,

                        'weight_kg' =>
                            $record[
                                'weight_kg'
                            ] ?? null,
                    ];


                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | Duplicate Detection
                |--------------------------------------------------------------------------
                |
                | ALL fields must match:
                |
                | Stream
                | Date
                | Vehicle
                | Time In
                | Time Out
                | Weight
                |
                */

                $alreadyExists =
                    WasteRecord::where(
                        'waste_stream',
                        'Landfill'
                    )
                    ->whereDate(
                        'collection_date',
                        $record[
                            'collection_date'
                        ]
                    )
                    ->where(
                        'vehicle_no',
                        $record[
                            'vehicle_no'
                        ]
                    )
                    ->where(
                        'time_in',
                        $record[
                            'time_in'
                        ]
                    )
                    ->where(
                        'time_out',
                        $record[
                            'time_out'
                        ]
                    )
                    ->where(
                        'actual_weight',
                        (float)
                        $record[
                            'weight_kg'
                        ]
                    )
                    ->exists();


                if ($alreadyExists) {

                    $duplicates++;
                    $skipped++;


                    $skipDetails[] = [

                        'reason' =>
                            'Duplicate record',

                        'collection_date' =>
                            $record[
                                'collection_date'
                            ],

                        'vehicle_no' =>
                            $record[
                                'vehicle_no'
                            ],

                        'time_in' =>
                            $record[
                                'time_in'
                            ],

                        'time_out' =>
                            $record[
                                'time_out'
                            ],

                        'weight_kg' =>
                            $record[
                                'weight_kg'
                            ],
                    ];


                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | Create Database Record
                |--------------------------------------------------------------------------
                */

                try {

                    WasteRecord::create([

                        'location_id' =>
                            null,

                        'waste_type_id' =>
                            null,

                        'recorded_by' =>
                            Auth::id(),

                        'actual_weight' =>
                            $record[
                                'weight_kg'
                            ],

                        'collection_date' =>
                            $record[
                                'collection_date'
                            ],

                        'vehicle_no' =>
                            $record[
                                'vehicle_no'
                            ],

                        'time_in' =>
                            $record[
                                'time_in'
                            ],

                        'time_out' =>
                            $record[
                                'time_out'
                            ],

                        'collection_method' =>
                            'Landfill File Import',

                        'waste_stream' =>
                            'Landfill',

                        'recycled' =>
                            false,

                        'remarks' =>
                            'Imported from landfill waste collection data',
                    ]);


                    $imported++;

                } catch (\Throwable $e) {

                    $failed++;


                    $skipDetails[] = [

                        'reason' =>
                            'Database error: '
                            . $e->getMessage(),

                        'collection_date' =>
                            $record[
                                'collection_date'
                            ] ?? null,

                        'vehicle_no' =>
                            $record[
                                'vehicle_no'
                            ] ?? null,

                        'time_in' =>
                            $record[
                                'time_in'
                            ] ?? null,

                        'time_out' =>
                            $record[
                                'time_out'
                            ] ?? null,

                        'weight_kg' =>
                            $record[
                                'weight_kg'
                            ] ?? null,
                    ];
                }
            }


            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();


            return back()->withErrors([
                'import' =>
                    'Landfill import failed: '
                    . $e->getMessage(),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        return view(
            'landfill.summary',
            [
                'total' =>
                    count($records),

                'imported' =>
                    $imported,

                'duplicates' =>
                    $duplicates,

                'skipped' =>
                    $skipped,

                'failed' =>
                    $failed,

                'skipDetails' =>
                    $skipDetails,
            ]
        );
    }
}