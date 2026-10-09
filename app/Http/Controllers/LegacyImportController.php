<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\WasteType;
use App\Models\WasteRecord;
use App\Services\LegacyExcelParserService;
use App\Services\LegacyImportValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LegacyImportController extends Controller
{
    public function __construct(
        private LegacyExcelParserService $parser,
        private LegacyImportValidationService $validator
    ) {}

    public function index() { return view('legacy.index'); }

    private function workbook(string $path)
    {
        if (!preg_match('~^legacy-imports/[A-Za-z0-9._-]+$~', $path) || !Storage::disk('local')->exists($path)) {
            throw ValidationException::withMessages(['excel_file' => 'Temporary workbook not found. Upload it again.']);
        }
        return IOFactory::load(Storage::disk('local')->path($path));
    }

    public function upload(Request $request)
    {
        $request->validate(['excel_file' => 'required|file|mimes:xlsx,xls|max:10240']);
        $path = $request->file('excel_file')->store('legacy-imports', 'local');
        try {
            $spreadsheet = $this->workbook($path);
            $sheetNames = [];
            $detectedTypes = [];
            foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
                $name = trim($sheet->getTitle());
                if (!str_starts_with(strtolower($name), 'total')) continue;
                $sheetNames[] = $name;
                foreach ($this->parser->parseWorksheet($sheet) as $record) {
                    $type = trim((string) ($record['waste_type'] ?? ''));
                    if ($type !== '') $detectedTypes[md5($type)] = $type;
                }
            }
            if (!$sheetNames) {
                Storage::disk('local')->delete($path);
                return back()->withErrors(['excel_file' => 'No supported legacy worksheets were found.']);
            }
            return view('legacy.select', [
                'sheetNames' => $sheetNames,
                'detectedTypes' => $detectedTypes,
                'wasteTypes' => WasteType::orderBy('type_name')->get(),
                'locations' => Location::orderBy('location_name')->get(),
                'tempFile' => $path,
            ]);
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            if ($e instanceof ValidationException) throw $e;
            return back()->withErrors(['excel_file' => 'Unable to read workbook: '.$e->getMessage()]);
        }
    }

    public function preview(Request $request)
    {
        $request->validate([
            'temp_file' => 'required|string',
            'sheets' => 'required|array|min:1',
            'sheets.*' => 'required|string',
            'from_month' => 'nullable|date_format:Y-m',
            'to_month' => 'nullable|date_format:Y-m|after_or_equal:from_month',
            'default_location_id' => 'nullable|integer|exists:locations,id',
            'type_map' => 'nullable|array',
            'type_map.*' => 'nullable|integer|exists:waste_types,id',
        ]);
        try { $spreadsheet = $this->workbook($request->temp_file); }
        catch (ValidationException $e) { return redirect()->route('legacy.index')->withErrors($e->errors()); }

        $allRecords = [];
        $processedSheets = [];
        $wasteTypes = WasteType::all()->keyBy('id');
        $defaultLocation = $request->filled('default_location_id')
            ? Location::find($request->integer('default_location_id')) : null;

        foreach ($request->sheets as $sheetName) {
            if (!str_starts_with(strtolower(trim($sheetName)), 'total')) continue;
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if (!$sheet) continue;
            $records = $this->parser->parseWorksheet($sheet);
            foreach ($records as &$record) {
                if ($defaultLocation && ($record['location'] ?? '') === 'Unassigned (monthly summary)') {
                    $record['location'] = $defaultLocation->location_name;
                }
                $key = md5(trim((string) ($record['waste_type'] ?? '')));
                $mappedId = $request->input('type_map.'.$key);
                if ($mappedId && ($type = $wasteTypes->get((int) $mappedId))) {
                    $record['original_waste_type'] = $record['waste_type'];
                    $record['waste_type'] = $type->type_name;
                }
            }
            unset($record);
            $records = array_values(array_filter($records, function ($record) use ($request) {
                $month = substr((string) ($record['collection_date'] ?? ''), 0, 7);
                return (!$request->filled('from_month') || $month >= $request->from_month)
                    && (!$request->filled('to_month') || $month <= $request->to_month);
            }));
            $records = $this->validator->validate($records);
            foreach ($records as &$record) $record['source_sheet'] = $sheetName;
            unset($record);
            $allRecords = array_merge($allRecords, $records);
            $processedSheets[] = $sheetName;
        }
        if (!$allRecords) return back()->withErrors(['sheets' => 'No records found for the selected worksheets and month range.']);
        return view('legacy.preview', [
            'records' => $allRecords,
            'processedSheets' => $processedSheets,
            'validCount' => collect($allRecords)->where('valid', true)->count(),
            'invalidCount' => collect($allRecords)->where('valid', false)->count(),
            'tempFile' => $request->temp_file,
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'temp_file' => 'required|string',
            'records' => 'required|array',
            'records.*.location_id' => 'nullable|integer',
            'records.*.waste_type_id' => 'nullable|integer',
            'records.*.collection_date' => 'nullable|date',
            'records.*.weight' => 'nullable|numeric',
        ]);
        // Require the uploaded workbook still to exist; reject arbitrary import POSTs.
        try { $this->workbook($request->temp_file); }
        catch (ValidationException $e) { return redirect()->route('legacy.index')->withErrors($e->errors()); }

        $records = $request->input('records', []);
        $imported = $skipped = $duplicates = $failed = 0;
        $skipDetails = [];
        DB::beginTransaction();
        try {
            foreach ($records as $record) {
                $locationId = $record['location_id'] ?? null;
                $typeId = $record['waste_type_id'] ?? null;
                $date = $record['collection_date'] ?? null;
                $weight = $record['weight'] ?? null;
                if (empty($record['valid']) || !$locationId || !$typeId || !$date || !is_numeric($weight) || (float)$weight <= 0
                    || !Location::whereKey($locationId)->exists() || !WasteType::whereKey($typeId)->exists()) {
                    $skipped++;
                    $skipDetails[] = ['reason'=>'Invalid record', 'location_id'=>$locationId, 'waste_type_id'=>$typeId, 'collection_date'=>$date, 'weight'=>$weight];
                    continue;
                }
                $exists = WasteRecord::where('location_id', $locationId)->where('waste_type_id', $typeId)
                    ->whereDate('collection_date', $date)->where('actual_weight', (float)$weight)->exists();
                if ($exists) {
                    $duplicates++; $skipped++;
                    $skipDetails[] = ['reason'=>'Duplicate record', 'location_id'=>$locationId, 'waste_type_id'=>$typeId, 'collection_date'=>$date, 'weight'=>$weight];
                    continue;
                }
                try {
                    WasteRecord::create([
                        'location_id'=>$locationId, 'waste_type_id'=>$typeId, 'recorded_by'=>Auth::id(),
                        'actual_weight'=>$weight, 'collection_date'=>$date,
                        'collection_method'=>'Legacy Excel Import', 'waste_stream'=>'Recycling',
                        'recycled'=>true, 'remarks'=>'Imported from CENSEi legacy Excel data',
                    ]);
                    $imported++;
                } catch (\Throwable $e) { $failed++; }
            }
            DB::commit();
            Storage::disk('local')->delete($request->temp_file);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['import'=>'Import failed: '.$e->getMessage()]);
        }
        return view('legacy.summary', compact('imported','skipped','duplicates','failed','skipDetails') + ['total'=>count($records)]);
    }
}
