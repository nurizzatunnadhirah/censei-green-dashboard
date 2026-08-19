<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\WasteType;
use App\Models\WasteRecord;
use App\Services\LegacyExcelParserService;
use App\Services\LegacyImportValidationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LegacyImportController extends Controller
{
    private LegacyExcelParserService $parser;
private LegacyImportValidationService $validator;

public function __construct(
    LegacyExcelParserService $parser,
    LegacyImportValidationService $validator
) {
    $this->parser = $parser;
    $this->validator = $validator;
}

    public function index()
    {
        return view('legacy.index');
    }

    public function upload(Request $request)
{
    $request->validate([
        'excel_file' => 'required|mimes:xlsx,xls|max:10240',
    ]);

    // Explicitly store on Laravel's local disk
    $path = $request->file('excel_file')
        ->store('legacy-imports', 'local');

    // Get the actual physical path from Laravel
    $fullPath = Storage::disk('local')->path($path);

    if (!Storage::disk('local')->exists($path)) {
        return back()->withErrors([
            'excel_file' => 'The uploaded file could not be stored.',
        ]);
    }

    $spreadsheet = IOFactory::load($fullPath);

    $sheetNames = [];

    foreach ($spreadsheet->getWorksheetIterator() as $sheet) {

        $sheetName = trim($sheet->getTitle());

        if (!str_starts_with(
            strtolower($sheetName),
            'total'
        )) {
            continue;
        }

        $sheetNames[] = $sheetName;
    }

    if (empty($sheetNames)) {

        Storage::disk('local')->delete($path);

        return back()->withErrors([
            'excel_file' =>
                'No supported legacy worksheets were found.',
        ]);
    }

    return view('legacy.select', [
        'sheetNames' => $sheetNames,
        'tempFile' => $path,
    ]);
}

public function preview(Request $request)
{
    $request->validate([
        'temp_file' => 'required|string',
        'sheets' => 'required|array|min:1',
        'sheets.*' => 'required|string',

        'from_month' => 'nullable|date_format:Y-m',
        'to_month' => 'nullable|date_format:Y-m|after_or_equal:from_month',
    ]);

    $path = $request->temp_file;

if (!Storage::disk('local')->exists($path)) {

    return redirect()
        ->route('legacy.index')
        ->withErrors([
            'excel_file' =>
                'The temporary Excel file could not be found. Please upload it again.',
        ]);
}

$fullPath = Storage::disk('local')->path($path);

$spreadsheet = IOFactory::load($fullPath);

    if (!file_exists($fullPath)) {

        return redirect()
            ->route('legacy.index')
            ->withErrors([
                'excel_file' =>
                    'The temporary Excel file could not be found. Please upload it again.',
            ]);
    }

    $spreadsheet = IOFactory::load($fullPath);

    $allRecords = [];
    $processedSheets = [];

    foreach ($request->sheets as $sheetName) {

        $sheet = $spreadsheet->getSheetByName(
            $sheetName
        );

        if (!$sheet) {
            continue;
        }

        $records =
            $this->parser->parseWorksheet($sheet);

        /*
         * Optional date range.
         */
        if ($request->filled('from_month')) {

            $fromDate =
                $request->from_month . '-01';

            $records = array_filter(
                $records,
                fn ($record) =>
                    $record['collection_date'] >= $fromDate
            );
        }

        if ($request->filled('to_month')) {

            $toDate =
                $request->to_month . '-01';

            $records = array_filter(
                $records,
                fn ($record) =>
                    $record['collection_date'] <= $toDate
            );
        }

        $records = array_values($records);

        $records =
            $this->validator->validate($records);

        foreach ($records as &$record) {
            $record['source_sheet'] =
                $sheetName;
        }

        unset($record);

        $allRecords = array_merge(
            $allRecords,
            $records
        );

        $processedSheets[] = $sheetName;
    }

    if (empty($allRecords)) {

        return back()->withErrors([
            'sheets' =>
                'No records were found for the selected worksheets and month range.',
        ]);
    }

    $validCount = collect($allRecords)
        ->where('valid', true)
        ->count();

    $invalidCount = collect($allRecords)
        ->where('valid', false)
        ->count();

    return view('legacy.preview', [
        'records' => $allRecords,
        'processedSheets' => $processedSheets,
        'validCount' => $validCount,
        'invalidCount' => $invalidCount,
        'tempFile' => $path,
    ]);
}
public function import(Request $request)
{
    $records = $request->input('records', []);
    $tempFile = $request->input('temp_file');

    $imported = 0;
    $skipped = 0;
    $duplicates = 0;
    $failed = 0;

    $skipDetails = [];

    DB::beginTransaction();

    try {

        foreach ($records as $record) {

            /*
             * Skip invalid records.
             */
            if (
                empty($record['valid']) ||
                empty($record['location_id']) ||
                empty($record['waste_type_id']) ||
                empty($record['collection_date'])
            ) {
                $skipped++;

                $skipDetails[] = [
                    'reason' => 'Invalid record',
                    'location_id' => $record['location_id'] ?? null,
                    'waste_type_id' => $record['waste_type_id'] ?? null,
                    'collection_date' => $record['collection_date'] ?? null,
                    'weight' => $record['weight'] ?? null,
                ];

                continue;
            }

            /*
             * Duplicate check:
             * location + waste type + collection date + weight
             */
            $alreadyExists = WasteRecord::where(
                'location_id',
                $record['location_id']
            )
                ->where(
                    'waste_type_id',
                    $record['waste_type_id']
                )
                ->whereDate(
                    'collection_date',
                    $record['collection_date']
                )
                ->where(
                    'actual_weight',
                    (float) $record['weight']
                )
                ->exists();

            if ($alreadyExists) {

                $duplicates++;
                $skipped++;

                $skipDetails[] = [
                    'reason' => 'Duplicate record',
                    'location_id' => $record['location_id'],
                    'waste_type_id' => $record['waste_type_id'],
                    'collection_date' => $record['collection_date'],
                    'weight' => $record['weight'],
                ];

                continue;
            }

            try {

                WasteRecord::create([
                    
                    'location_id' => $record['location_id'],
                    'waste_type_id' => $record['waste_type_id'],
                    'recorded_by' => Auth::id(),

                    'actual_weight' => $record['weight'],

                    'collection_date' => $record['collection_date'],

                    'collection_method' => 'Legacy Excel Import',

                    'waste_stream' => 'Recycling',

                    'recycled' => true,

                    'remarks' => 'Imported from CENSEi legacy Excel data',
                ]);

                $imported++;

            } catch (\Throwable $e) {

                $failed++;
            }
        }

        DB::commit();

        /*
         * Delete the temporary Excel workbook
         * only after the database transaction succeeds.
         */
        if (
            $tempFile &&
            Storage::disk('local')->exists($tempFile)
        ) {
            Storage::disk('local')->delete($tempFile);
        }

    } catch (\Throwable $e) {

        DB::rollBack();

        return back()->withErrors([
            'import' => 'Import failed: ' . $e->getMessage(),
        ]);
    }

    return view('legacy.summary', [
        'total' => count($records),
        'imported' => $imported,
        'skipped' => $skipped,
        'duplicates' => $duplicates,
        'failed' => $failed,
        'skipDetails' => $skipDetails,
    ]);
}
}