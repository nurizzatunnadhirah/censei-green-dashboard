<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\WasteRecord;
use App\Models\WasteType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | REPORT PAGE
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $this->validateFilters($request);

        $selectedStream = $request->input('waste_stream', '');


        /*
        |--------------------------------------------------------------------------
        | Filter Options
        |--------------------------------------------------------------------------
        */

        $locations = Location::orderBy('location_name')->get();

        $wasteTypes = WasteType::orderBy('type_name')->get();


        /*
        |--------------------------------------------------------------------------
        | Available Years
        |--------------------------------------------------------------------------
        */

        $availableYears = WasteRecord::selectRaw(
                'YEAR(collection_date) as year'
            )
            ->whereNotNull('collection_date')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');


        /*
        |--------------------------------------------------------------------------
        | Build Queries
        |--------------------------------------------------------------------------
        */

        $recyclingQuery = $this->buildRecyclingQuery($request);

        $landfillQuery = $this->buildLandfillQuery($request);


        /*
        |--------------------------------------------------------------------------
        | Recycling Summary
        |--------------------------------------------------------------------------
        */

        $recyclingTotalWaste = (clone $recyclingQuery)
            ->sum('actual_weight');

        $recyclingTotalRecords = (clone $recyclingQuery)
            ->count();

        $recyclingAverageWeight = (clone $recyclingQuery)
            ->avg('actual_weight') ?? 0;


        /*
        |--------------------------------------------------------------------------
        | Landfill Summary
        |--------------------------------------------------------------------------
        */

        $landfillTotalWaste = (clone $landfillQuery)
            ->sum('actual_weight');

        $landfillTotalRecords = (clone $landfillQuery)
            ->count();

        $landfillAverageWeight = (clone $landfillQuery)
            ->avg('actual_weight') ?? 0;


        /*
        |--------------------------------------------------------------------------
        | Overall Summary
        |--------------------------------------------------------------------------
        */

        if ($selectedStream === 'recycling') {

            $totalWaste = $recyclingTotalWaste;

            $totalRecords = $recyclingTotalRecords;

            $averageWeight = $recyclingAverageWeight;

        } elseif ($selectedStream === 'landfill') {

            $totalWaste = $landfillTotalWaste;

            $totalRecords = $landfillTotalRecords;

            $averageWeight = $landfillAverageWeight;

        } else {

            $totalWaste =
                $recyclingTotalWaste +
                $landfillTotalWaste;

            $totalRecords =
                $recyclingTotalRecords +
                $landfillTotalRecords;

            $averageWeight =
                $totalRecords > 0
                    ? $totalWaste / $totalRecords
                    : 0;
        }


        /*
        |--------------------------------------------------------------------------
        | Highest Waste Type
        |--------------------------------------------------------------------------
        */

        $highestWasteType = null;

        if ($selectedStream !== 'landfill') {

            $highestWasteType = (clone $recyclingQuery)
                ->join(
                    'waste_types',
                    'waste_records.waste_type_id',
                    '=',
                    'waste_types.id'
                )
                ->select(
                    'waste_types.type_name',
                    DB::raw(
                        'SUM(waste_records.actual_weight) as total_weight'
                    )
                )
                ->groupBy(
                    'waste_types.type_name'
                )
                ->orderByDesc(
                    'total_weight'
                )
                ->first();
        }


        /*
        |--------------------------------------------------------------------------
        | Highest Location
        |--------------------------------------------------------------------------
        */

        $highestLocation = null;

        if ($selectedStream !== 'landfill') {

            $highestLocation = (clone $recyclingQuery)
                ->join(
                    'locations',
                    'waste_records.location_id',
                    '=',
                    'locations.id'
                )
                ->select(
                    'locations.location_name',
                    DB::raw(
                        'SUM(waste_records.actual_weight) as total_weight'
                    )
                )
                ->groupBy(
                    'locations.location_name'
                )
                ->orderByDesc(
                    'total_weight'
                )
                ->first();
        }

        /*
|--------------------------------------------------------------------------
| Recycling Analytics - Waste Type
|--------------------------------------------------------------------------
*/

$wasteByType = (clone $recyclingQuery)
    ->join(
        'waste_types',
        'waste_records.waste_type_id',
        '=',
        'waste_types.id'
    )
    ->select(
        'waste_types.type_name',
        DB::raw(
            'SUM(waste_records.actual_weight) as total_weight'
        )
    )
    ->groupBy('waste_types.type_name')
    ->orderBy('waste_types.type_name')
    ->get();


/*
|--------------------------------------------------------------------------
| Recycling Analytics - Location
|--------------------------------------------------------------------------
*/

$wasteByLocation = (clone $recyclingQuery)
    ->join(
        'locations',
        'waste_records.location_id',
        '=',
        'locations.id'
    )
    ->select(
        'locations.location_name',
        DB::raw(
            'SUM(waste_records.actual_weight) as total_weight'
        )
    )
    ->groupBy('locations.location_name')
    ->orderBy('locations.location_name')
    ->get();


/*
|--------------------------------------------------------------------------
| Recycling Monthly Trend
|--------------------------------------------------------------------------
*/

$monthlyWaste = (clone $recyclingQuery)
    ->select(
        DB::raw(
            "FORMAT(collection_date, 'yyyy-MM') as month"
        ),
        DB::raw(
            'SUM(actual_weight) as total_weight'
        )
    )
    ->groupBy(
        DB::raw(
            "FORMAT(collection_date, 'yyyy-MM')"
        )
    )
    ->orderBy(
        DB::raw(
            "FORMAT(collection_date, 'yyyy-MM')"
        )
    )
    ->get();


/*
|--------------------------------------------------------------------------
| Landfill Monthly Trend
|--------------------------------------------------------------------------
*/

$monthlyLandfill = (clone $landfillQuery)
    ->select(
        DB::raw(
            "FORMAT(collection_date, 'yyyy-MM') as month"
        ),
        DB::raw(
            'SUM(actual_weight) as total_weight'
        )
    )
    ->groupBy(
        DB::raw(
            "FORMAT(collection_date, 'yyyy-MM')"
        )
    )
    ->orderBy(
        DB::raw(
            "FORMAT(collection_date, 'yyyy-MM')"
        )
    )
    ->get();


        /*
        |--------------------------------------------------------------------------
        | Recycling Records
        |--------------------------------------------------------------------------
        */

        $records = (clone $recyclingQuery)
            ->orderByDesc('collection_date')
            ->paginate(
                15,
                ['*'],
                'page'
            )
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Landfill Records
        |--------------------------------------------------------------------------
        */

        $landfillRecords = (clone $landfillQuery)
            ->orderByDesc('collection_date')
            ->paginate(
                15,
                ['*'],
                'landfill_page'
            )
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view('reports.index', compact(
            'locations',
            'wasteTypes',
            'availableYears',
            'selectedStream',

            'totalWaste',
            'totalRecords',
            'averageWeight',

            'recyclingTotalWaste',
            'recyclingTotalRecords',
            'recyclingAverageWeight',

            'landfillTotalWaste',
            'landfillTotalRecords',
            'landfillAverageWeight',

            'highestWasteType',
            'highestLocation',

            'wasteByType',
            'wasteByLocation',
            'monthlyWaste',
            'monthlyLandfill',

            'records',
            'landfillRecords'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | EXCEL EXPORT
    |--------------------------------------------------------------------------
    */

    public function exportExcel(Request $request)
    {
        $this->validateFilters($request);

        $selectedStream = $request->input('waste_stream', '');


        /*
        |--------------------------------------------------------------------------
        | Queries
        |--------------------------------------------------------------------------
        */

        $recyclingQuery = $this->buildRecyclingQuery($request);

        $landfillQuery = $this->buildLandfillQuery($request);


        /*
        |--------------------------------------------------------------------------
        | Records
        |--------------------------------------------------------------------------
        */

        $recyclingRecords = (clone $recyclingQuery)
            ->orderBy('collection_date')
            ->get();

        $landfillRecords = (clone $landfillQuery)
            ->orderBy('collection_date')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Spreadsheet
        |--------------------------------------------------------------------------
        */

        $spreadsheet = new Spreadsheet();


        /*
        |--------------------------------------------------------------------------
        | Summary Sheet
        |--------------------------------------------------------------------------
        */

        $summarySheet = $spreadsheet->getActiveSheet();

        $summarySheet->setTitle('Summary');

        $summarySheet->setCellValue(
            'A1',
            'CENSEi Waste Management Report'
        );

        $summarySheet->mergeCells('A1:B1');

        $summarySheet
            ->getStyle('A1')
            ->getFont()
            ->setBold(true)
            ->setSize(16);


        /*
        |--------------------------------------------------------------------------
        | Stream Label
        |--------------------------------------------------------------------------
        */

        $streamLabel = match ($selectedStream) {

            'recycling' =>
                'Recycling',

            'landfill' =>
                'Waste to Landfill',

            default =>
                'All Waste Streams',
        };


        /*
        |--------------------------------------------------------------------------
        | Location Label
        |--------------------------------------------------------------------------
        */

        $locationName = 'All Locations';

        if ($request->filled('location_id')) {

            $locationName =
                Location::find(
                    $request->location_id
                )?->location_name ?? '-';
        }


        /*
        |--------------------------------------------------------------------------
        | Waste Type Label
        |--------------------------------------------------------------------------
        */

        $wasteTypeName = 'All Waste Types';

        if ($request->filled('waste_type_id')) {

            $wasteTypeName =
                WasteType::find(
                    $request->waste_type_id
                )?->type_name ?? '-';
        }


        /*
        |--------------------------------------------------------------------------
        | Summary Values
        |--------------------------------------------------------------------------
        */

        $recyclingWeight =
            $recyclingRecords->sum(
                'actual_weight'
            );

        $landfillWeight =
            $landfillRecords->sum(
                'actual_weight'
            );


        if ($selectedStream === 'recycling') {

            $totalWaste =
                $recyclingWeight;

            $totalRecords =
                $recyclingRecords->count();

        } elseif ($selectedStream === 'landfill') {

            $totalWaste =
                $landfillWeight;

            $totalRecords =
                $landfillRecords->count();

        } else {

            $totalWaste =
                $recyclingWeight +
                $landfillWeight;

            $totalRecords =
                $recyclingRecords->count() +
                $landfillRecords->count();
        }


        $averageWeight =
            $totalRecords > 0
                ? $totalWaste / $totalRecords
                : 0;


        /*
        |--------------------------------------------------------------------------
        | Summary Rows
        |--------------------------------------------------------------------------
        */

        $summaryRows = [

            [
                'Report Filters',
                ''
            ],

            [
                'Year',
                $request->year ?: 'All Years'
            ],

            [
                'Waste Stream',
                $streamLabel
            ],

            [
                'Location / Program',
                $selectedStream === 'landfill'
                    ? 'Not Applicable'
                    : $locationName
            ],

            [
                'Waste Type',
                $selectedStream === 'landfill'
                    ? 'Not Applicable'
                    : $wasteTypeName
            ],

            [
                'From Date',
                $request->date_from ?: '-'
            ],

            [
                'To Date',
                $request->date_to ?: '-'
            ],

            [
                '',
                ''
            ],

            [
                'Total Waste',
                number_format(
                    $totalWaste,
                    2
                ) . ' kg'
            ],

            [
                'Total Records',
                $totalRecords
            ],

            [
                'Average Weight',
                number_format(
                    $averageWeight,
                    2
                ) . ' kg'
            ],
        ];


        $row = 3;

        foreach ($summaryRows as $summaryRow) {

            $summarySheet->setCellValue(
                "A{$row}",
                $summaryRow[0]
            );

            $summarySheet->setCellValue(
                "B{$row}",
                $summaryRow[1]
            );

            $row++;
        }


        $summarySheet
            ->getStyle('A3')
            ->getFont()
            ->setBold(true);

        $summarySheet
            ->getColumnDimension('A')
            ->setAutoSize(true);

        $summarySheet
            ->getColumnDimension('B')
            ->setAutoSize(true);


        /*
        |--------------------------------------------------------------------------
        | Recycling Sheet
        |--------------------------------------------------------------------------
        */

        if ($selectedStream !== 'landfill') {

            $recyclingSheet =
                $spreadsheet->createSheet();

            $recyclingSheet->setTitle(
                'Recycling'
            );


            $headers = [
                'Date',
                'Location / Program',
                'Waste Type',
                'Weight (kg)',
                'Collection Method',
                'Recorded By',
                'Remarks',
            ];


            foreach ($headers as $index => $header) {

                $column =
                    chr(65 + $index);

                $recyclingSheet->setCellValue(
                    "{$column}1",
                    $header
                );
            }


            $recyclingSheet
                ->getStyle('A1:G1')
                ->getFont()
                ->setBold(true);


            $row = 2;


            foreach ($recyclingRecords as $record) {

                $recyclingSheet->setCellValue(
                    "A{$row}",
                    $record->collection_date
                        ? \Carbon\Carbon::parse(
                            $record->collection_date
                        )->format('d M Y')
                        : '-'
                );


                $recyclingSheet->setCellValue(
                    "B{$row}",
                    $record->location
                        ->location_name ?? '-'
                );


                $recyclingSheet->setCellValue(
                    "C{$row}",
                    $record->wasteType
                        ->type_name ?? '-'
                );


                $recyclingSheet->setCellValue(
                    "D{$row}",
                    $record->actual_weight
                );


                $recyclingSheet->setCellValue(
                    "E{$row}",
                    $record->collection_method ?? '-'
                );


                $recyclingSheet->setCellValue(
                    "F{$row}",
                    $record->recordedBy
                        ->name ?? '-'
                );


                $recyclingSheet->setCellValue(
                    "G{$row}",
                    $record->remarks ?? '-'
                );


                $row++;
            }


            foreach (
                range('A', 'G')
                as $column
            ) {

                $recyclingSheet
                    ->getColumnDimension(
                        $column
                    )
                    ->setAutoSize(true);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Landfill Sheet
        |--------------------------------------------------------------------------
        */

        if ($selectedStream !== 'recycling') {

            $landfillSheet =
                $spreadsheet->createSheet();

            $landfillSheet->setTitle(
                'Waste to Landfill'
            );


            $headers = [
                'Date',
                'Vehicle Number',
                'Time In',
                'Time Out',
                'Weight (kg)',
            ];


            foreach ($headers as $index => $header) {

                $column =
                    chr(65 + $index);

                $landfillSheet->setCellValue(
                    "{$column}1",
                    $header
                );
            }


            $landfillSheet
                ->getStyle('A1:E1')
                ->getFont()
                ->setBold(true);


            $row = 2;


            foreach ($landfillRecords as $record) {

                $landfillSheet->setCellValue(
                    "A{$row}",
                    $record->collection_date
                        ? \Carbon\Carbon::parse(
                            $record->collection_date
                        )->format('d M Y')
                        : '-'
                );


                $landfillSheet->setCellValue(
                    "B{$row}",
                    $record->vehicle_no ?? '-'
                );


                $landfillSheet->setCellValue(
    "C{$row}",
    $record->time_in
        ? \Carbon\Carbon::parse($record->time_in)->format('H:i')
        : '-'
);

$landfillSheet->setCellValue(
    "D{$row}",
    $record->time_out
        ? \Carbon\Carbon::parse($record->time_out)->format('H:i')
        : '-'
);


                $landfillSheet->setCellValue(
                    "E{$row}",
                    $record->actual_weight
                );


                $row++;
            }


            foreach (
                range('A', 'E')
                as $column
            ) {

                $landfillSheet
                    ->getColumnDimension(
                        $column
                    )
                    ->setAutoSize(true);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Download
        |--------------------------------------------------------------------------
        */

        $spreadsheet->setActiveSheetIndex(0);


        $fileName =
            'CENSEi_Waste_Report_' .
            now()->format(
                'Y-m-d_His'
            ) .
            '.xlsx';


        return response()->streamDownload(

            function () use ($spreadsheet) {

                $writer =
                    new Xlsx($spreadsheet);

                $writer->save(
                    'php://output'
                );
            },

            $fileName,

            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PRINT / PDF
    |--------------------------------------------------------------------------
    */

    public function print(Request $request)
    {
        $this->validateFilters($request);


        $selectedStream =
            $request->input(
                'waste_stream',
                ''
            );


        /*
        |--------------------------------------------------------------------------
        | Queries
        |--------------------------------------------------------------------------
        */

        $recyclingQuery =
            $this->buildRecyclingQuery(
                $request
            );

        $landfillQuery =
            $this->buildLandfillQuery(
                $request
            );


        /*
        |--------------------------------------------------------------------------
        | Records
        |--------------------------------------------------------------------------
        */

        $records =
            (clone $recyclingQuery)
                ->orderBy(
                    'collection_date'
                )
                ->get();


        $landfillRecords =
            (clone $landfillQuery)
                ->orderBy(
                    'collection_date'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | Recycling Analytics - Waste Type
        |--------------------------------------------------------------------------
        */

        $wasteByType =
            (clone $recyclingQuery)
                ->join(
                    'waste_types',
                    'waste_records.waste_type_id',
                    '=',
                    'waste_types.id'
                )
                ->select(
                    'waste_types.type_name',
                    DB::raw(
                        'SUM(waste_records.actual_weight) as total_weight'
                    )
                )
                ->groupBy(
                    'waste_types.type_name'
                )
                ->orderBy(
                    'waste_types.type_name'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | Recycling Analytics - Location
        |--------------------------------------------------------------------------
        */

        $wasteByLocation =
            (clone $recyclingQuery)
                ->join(
                    'locations',
                    'waste_records.location_id',
                    '=',
                    'locations.id'
                )
                ->select(
                    'locations.location_name',
                    DB::raw(
                        'SUM(waste_records.actual_weight) as total_weight'
                    )
                )
                ->groupBy(
                    'locations.location_name'
                )
                ->orderBy(
                    'locations.location_name'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | Recycling Monthly Analytics
        |--------------------------------------------------------------------------
        */

        $monthlyWaste =
            (clone $recyclingQuery)
                ->select(
                    DB::raw(
                        "FORMAT(collection_date, 'yyyy-MM') as month"
                    ),

                    DB::raw(
                        'SUM(actual_weight) as total_weight'
                    )
                )
                ->groupBy(
                    DB::raw(
                        "FORMAT(collection_date, 'yyyy-MM')"
                    )
                )
                ->orderBy(
                    DB::raw(
                        "FORMAT(collection_date, 'yyyy-MM')"
                    )
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | Landfill Monthly Analytics
        |--------------------------------------------------------------------------
        */

        $monthlyLandfill =
            (clone $landfillQuery)
                ->select(
                    DB::raw(
                        "FORMAT(collection_date, 'yyyy-MM') as month"
                    ),

                    DB::raw(
                        'SUM(actual_weight) as total_weight'
                    )
                )
                ->groupBy(
                    DB::raw(
                        "FORMAT(collection_date, 'yyyy-MM')"
                    )
                )
                ->orderBy(
                    DB::raw(
                        "FORMAT(collection_date, 'yyyy-MM')"
                    )
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $recyclingTotalWaste =
            $records->sum(
                'actual_weight'
            );

        $landfillTotalWaste =
            $landfillRecords->sum(
                'actual_weight'
            );


        $recyclingTotalRecords =
            $records->count();

        $landfillTotalRecords =
            $landfillRecords->count();


        if ($selectedStream === 'recycling') {

            $totalWaste =
                $recyclingTotalWaste;

            $totalRecords =
                $recyclingTotalRecords;

        } elseif ($selectedStream === 'landfill') {

            $totalWaste =
                $landfillTotalWaste;

            $totalRecords =
                $landfillTotalRecords;

        } else {

            $totalWaste =
                $recyclingTotalWaste +
                $landfillTotalWaste;

            $totalRecords =
                $recyclingTotalRecords +
                $landfillTotalRecords;
        }


        $averageWeight =
            $totalRecords > 0
                ? $totalWaste / $totalRecords
                : 0;


        /*
        |--------------------------------------------------------------------------
        | Highest Recycling Type / Location
        |--------------------------------------------------------------------------
        */

        $highestWasteType = null;

        $highestLocation = null;


        if ($selectedStream !== 'landfill') {

            $highestWasteType =
                $wasteByType
                    ->sortByDesc(
                        'total_weight'
                    )
                    ->first();


            $highestLocation =
                $wasteByLocation
                    ->sortByDesc(
                        'total_weight'
                    )
                    ->first();
        }


        /*
        |--------------------------------------------------------------------------
        | Filter Labels
        |--------------------------------------------------------------------------
        */

        $locationName =
            'All Locations';


        if ($request->filled(
            'location_id'
        )) {

            $locationName =
                Location::find(
                    $request->location_id
                )?->location_name ?? '-';
        }


        $wasteTypeName =
            'All Waste Types';


        if ($request->filled(
            'waste_type_id'
        )) {

            $wasteTypeName =
                WasteType::find(
                    $request->waste_type_id
                )?->type_name ?? '-';
        }


        $streamLabel = match (
            $selectedStream
        ) {

            'recycling' =>
                'Recycling',

            'landfill' =>
                'Waste to Landfill',

            default =>
                'All Waste Streams',
        };


        /*
        |--------------------------------------------------------------------------
        | Return Print View
        |--------------------------------------------------------------------------
        */

        return view(
            'reports.print',
            compact(
                'records',
                'landfillRecords',

                'selectedStream',
                'streamLabel',

                'totalWaste',
                'totalRecords',
                'averageWeight',

                'recyclingTotalWaste',
                'recyclingTotalRecords',

                'landfillTotalWaste',
                'landfillTotalRecords',

                'highestWasteType',
                'highestLocation',

                'locationName',
                'wasteTypeName',

                'wasteByType',
                'wasteByLocation',

                'monthlyWaste',
                'monthlyLandfill'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATE FILTERS
    |--------------------------------------------------------------------------
    */

    private function validateFilters(
        Request $request
    ): void
    {
        $request->validate([

            'year' =>
                'nullable|integer',

            'waste_stream' =>
                'nullable|in:recycling,landfill',

            'location_id' =>
                'nullable|integer|exists:locations,id',

            'waste_type_id' =>
                'nullable|integer|exists:waste_types,id',

            'date_from' =>
                'nullable|date',

            'date_to' =>
                'nullable|date|after_or_equal:date_from',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | BUILD RECYCLING QUERY
    |--------------------------------------------------------------------------
    */

    private function buildRecyclingQuery(
        Request $request
    )
    {
        $query =
            WasteRecord::query()
                ->with([
                    'location',
                    'wasteType',
                    'recordedBy',
                ])
                ->where(function ($query) {

                    $query
                        ->where(
                            'waste_stream',
                            'Recycling'
                        )
                        ->orWhereNull(
                            'waste_stream'
                        );
                });


        if ($request->filled('year')) {

            $query->whereYear(
                'collection_date',
                $request->year
            );
        }


        if ($request->filled(
            'location_id'
        )) {

            $query->where(
                'location_id',
                $request->location_id
            );
        }


        if ($request->filled(
            'waste_type_id'
        )) {

            $query->where(
                'waste_type_id',
                $request->waste_type_id
            );
        }


        if ($request->filled(
            'date_from'
        )) {

            $query->whereDate(
                'collection_date',
                '>=',
                $request->date_from
            );
        }


        if ($request->filled(
            'date_to'
        )) {

            $query->whereDate(
                'collection_date',
                '<=',
                $request->date_to
            );
        }


        return $query;
    }


    /*
    |--------------------------------------------------------------------------
    | BUILD LANDFILL QUERY
    |--------------------------------------------------------------------------
    */

    private function buildLandfillQuery(
        Request $request
    )
    {
        $query =
            WasteRecord::query()
                ->with([
                    'recordedBy',
                ])
                ->where(
                    'waste_stream',
                    'Landfill'
                );


        if ($request->filled('year')) {

            $query->whereYear(
                'collection_date',
                $request->year
            );
        }


        if ($request->filled(
            'date_from'
        )) {

            $query->whereDate(
                'collection_date',
                '>=',
                $request->date_from
            );
        }


        if ($request->filled(
            'date_to'
        )) {

            $query->whereDate(
                'collection_date',
                '<=',
                $request->date_to
            );
        }


        return $query;
    }
}