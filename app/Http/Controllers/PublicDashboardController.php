<?php

namespace App\Http\Controllers;

use App\Models\WasteRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicDashboardController extends Controller
{
    public function index(): View
    {
        /*
        |--------------------------------------------------------------------------
        | Selected Filters
        |--------------------------------------------------------------------------
        */

        $selectedYear = request('year');

        $selectedStream = request(
            'waste_stream',
            'Overall'
        );

        if (!in_array(
            $selectedStream,
            ['Overall', 'Recycling', 'Landfill']
        )) {
            $selectedStream = 'Overall';
        }


        /*
        |--------------------------------------------------------------------------
        | Available Years
        |--------------------------------------------------------------------------
        */

        $yearQuery = WasteRecord::query()
            ->whereNotNull('collection_date');

        if ($selectedStream !== 'Overall') {
            $yearQuery->where(
                'waste_stream',
                $selectedStream
            );
        }

        $availableYears = $yearQuery
            ->selectRaw(
                'YEAR(collection_date) as year'
            )
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');


        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */

        $recordsQuery = WasteRecord::query();

        if ($selectedStream !== 'Overall') {
            $recordsQuery->where(
                'waste_stream',
                $selectedStream
            );
        }

        if ($selectedYear) {
            $recordsQuery->whereYear(
                'collection_date',
                $selectedYear
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Shared KPI Data
        |--------------------------------------------------------------------------
        */

        $totalWaste = (clone $recordsQuery)
            ->sum('actual_weight');

        $totalRecords = (clone $recordsQuery)
            ->count();

        $averageWeight = (clone $recordsQuery)
            ->avg('actual_weight') ?? 0;


        /*
        |--------------------------------------------------------------------------
        | Overall Stream Totals
        |--------------------------------------------------------------------------
        */

        $recyclingTotalQuery = WasteRecord::query()
            ->where(
                'waste_stream',
                'Recycling'
            );

        $landfillTotalQuery = WasteRecord::query()
            ->where(
                'waste_stream',
                'Landfill'
            );

        if ($selectedYear) {

            $recyclingTotalQuery->whereYear(
                'collection_date',
                $selectedYear
            );

            $landfillTotalQuery->whereYear(
                'collection_date',
                $selectedYear
            );
        }

        $recyclingTotal =
            $recyclingTotalQuery->sum('actual_weight');

        $landfillTotal =
            $landfillTotalQuery->sum('actual_weight');


        /*
        |--------------------------------------------------------------------------
        | Default Collections
        |--------------------------------------------------------------------------
        */

        $wasteByType = collect();
        $wasteByLocation = collect();

        $monthlyCollections = collect();
        $monthlyAverageWeight = collect();

        $totalLocations = 0;
        $totalWasteTypes = 0;
        $totalVehicles = 0;


        /*
        |--------------------------------------------------------------------------
        | Recycling Analytics
        |--------------------------------------------------------------------------
        */

        if ($selectedStream === 'Recycling') {

            $totalLocations = (clone $recordsQuery)
                ->whereNotNull('location_id')
                ->distinct()
                ->count('location_id');

            $totalWasteTypes = (clone $recordsQuery)
                ->whereNotNull('waste_type_id')
                ->distinct()
                ->count('waste_type_id');


            /*
            | Waste by Type
            */

            $wasteByType = (clone $recordsQuery)
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
            | Waste by Location
            */

            $wasteByLocation = (clone $recordsQuery)
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
                ->get()
                ->map(function ($item) {

                    $item->location_name =
                        Str::limit(
                            $item->location_name,
                            22,
                            '...'
                        );

                    return $item;
                });
        }


        /*
        |--------------------------------------------------------------------------
        | Landfill Analytics
        |--------------------------------------------------------------------------
        */

        if ($selectedStream === 'Landfill') {

            $totalVehicles = (clone $recordsQuery)
                ->whereNotNull('vehicle_no')
                ->distinct()
                ->count('vehicle_no');


            /*
            | Monthly Collection Frequency
            */

            if ($selectedYear) {

                $collectionResults =
                    (clone $recordsQuery)
                        ->select(
                            DB::raw(
                                'MONTH(collection_date) as month_number'
                            ),
                            DB::raw(
                                'COUNT(*) as collection_count'
                            )
                        )
                        ->groupBy(
                            DB::raw(
                                'MONTH(collection_date)'
                            )
                        )
                        ->orderBy(
                            DB::raw(
                                'MONTH(collection_date)'
                            )
                        )
                        ->get()
                        ->keyBy('month_number');

                $monthlyCollections = collect();

                for ($month = 1; $month <= 12; $month++) {

                    $monthlyCollections->push(
                        (object) [
                            'month' =>
                                $this->monthName($month),

                            'collection_count' =>
                                isset(
                                    $collectionResults[$month]
                                )
                                    ? (int)
                                        $collectionResults[$month]
                                            ->collection_count
                                    : 0
                        ]
                    );
                }

            } else {

                $monthlyCollections =
                    (clone $recordsQuery)
                        ->select(
                            DB::raw(
                                "FORMAT(collection_date, 'yyyy-MM') as month"
                            ),
                            DB::raw(
                                'COUNT(*) as collection_count'
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
            }


            /*
            | Average Waste per Collection by Month
            */

            if ($selectedYear) {

                $monthlyAverageResults =
                    (clone $recordsQuery)
                        ->select(
                            DB::raw(
                                'MONTH(collection_date) as month_number'
                            ),
                            DB::raw(
                                'AVG(actual_weight) as average_weight'
                            )
                        )
                        ->groupBy(
                            DB::raw(
                                'MONTH(collection_date)'
                            )
                        )
                        ->orderBy(
                            DB::raw(
                                'MONTH(collection_date)'
                            )
                        )
                        ->get()
                        ->keyBy('month_number');

                $monthlyAverageWeight =
                    collect();

                for ($month = 1; $month <= 12; $month++) {

                    $monthlyAverageWeight->push(
                        (object) [
                            'month' =>
                                $this->monthName($month),

                            'average_weight' =>
                                isset(
                                    $monthlyAverageResults[$month]
                                )
                                    ? (float)
                                        $monthlyAverageResults[$month]
                                            ->average_weight
                                    : 0
                        ]
                    );
                }

            } else {

                $monthlyAverageWeight =
                    (clone $recordsQuery)
                        ->select(
                            DB::raw(
                                "FORMAT(collection_date, 'yyyy-MM') as month"
                            ),
                            DB::raw(
                                'AVG(actual_weight) as average_weight'
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
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Monthly Waste Trend
        |--------------------------------------------------------------------------
        */

        if ($selectedYear) {

            $monthlyResults =
                (clone $recordsQuery)
                    ->select(
                        DB::raw(
                            'MONTH(collection_date) as month_number'
                        ),
                        DB::raw(
                            'SUM(actual_weight) as total_weight'
                        )
                    )
                    ->groupBy(
                        DB::raw(
                            'MONTH(collection_date)'
                        )
                    )
                    ->orderBy(
                        DB::raw(
                            'MONTH(collection_date)'
                        )
                    )
                    ->get()
                    ->keyBy('month_number');


            $monthlyWaste = collect();

            for ($month = 1; $month <= 12; $month++) {

                $monthlyWaste->push(
                    (object) [
                        'month' =>
                            $this->monthName($month),

                        'total_weight' =>
                            isset(
                                $monthlyResults[$month]
                            )
                                ? (float)
                                    $monthlyResults[$month]
                                        ->total_weight
                                : 0
                    ]
                );
            }

        } else {

            $monthlyWaste =
                (clone $recordsQuery)
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
        }


        /*
        |--------------------------------------------------------------------------
        | Overall Monthly Stream Comparison
        |--------------------------------------------------------------------------
        */

        $overallMonthly = collect();

        if ($selectedStream === 'Overall') {

            $overallQuery =
                WasteRecord::query();

            if ($selectedYear) {

                $overallQuery->whereYear(
                    'collection_date',
                    $selectedYear
                );

                $results = $overallQuery
                    ->select(
                        DB::raw(
                            'MONTH(collection_date) as month_number'
                        ),
                        'waste_stream',
                        DB::raw(
                            'SUM(actual_weight) as total_weight'
                        )
                    )
                    ->groupBy(
                        DB::raw(
                            'MONTH(collection_date)'
                        ),
                        'waste_stream'
                    )
                    ->get();

                for ($month = 1; $month <= 12; $month++) {

                    $recycling = $results
                        ->where(
                            'month_number',
                            $month
                        )
                        ->where(
                            'waste_stream',
                            'Recycling'
                        )
                        ->first();

                    $landfill = $results
                        ->where(
                            'month_number',
                            $month
                        )
                        ->where(
                            'waste_stream',
                            'Landfill'
                        )
                        ->first();

                    $overallMonthly->push(
                        (object) [
                            'month' =>
                                $this->monthName($month),

                            'recycling' =>
                                $recycling
                                    ? (float)
                                        $recycling
                                            ->total_weight
                                    : 0,

                            'landfill' =>
                                $landfill
                                    ? (float)
                                        $landfill
                                            ->total_weight
                                    : 0,
                        ]
                    );
                }

            } else {

                $results = $overallQuery
                    ->select(
                        DB::raw(
                            "FORMAT(collection_date, 'yyyy-MM') as month"
                        ),
                        'waste_stream',
                        DB::raw(
                            'SUM(actual_weight) as total_weight'
                        )
                    )
                    ->groupBy(
                        DB::raw(
                            "FORMAT(collection_date, 'yyyy-MM')"
                        ),
                        'waste_stream'
                    )
                    ->orderBy(
                        DB::raw(
                            "FORMAT(collection_date, 'yyyy-MM')"
                        )
                    )
                    ->get();

                $months = $results
                    ->pluck('month')
                    ->unique()
                    ->values();

                foreach ($months as $month) {

                    $recycling = $results
                        ->where('month', $month)
                        ->where(
                            'waste_stream',
                            'Recycling'
                        )
                        ->first();

                    $landfill = $results
                        ->where('month', $month)
                        ->where(
                            'waste_stream',
                            'Landfill'
                        )
                        ->first();

                    $overallMonthly->push(
                        (object) [
                            'month' => $month,

                            'recycling' =>
                                $recycling
                                    ? (float)
                                        $recycling
                                            ->total_weight
                                    : 0,

                            'landfill' =>
                                $landfill
                                    ? (float)
                                        $landfill
                                            ->total_weight
                                    : 0,
                        ]
                    );
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Recycling Rate
        |--------------------------------------------------------------------------
        */

        $recyclingRate =
            $totalWaste > 0
                ? (
                    $recyclingTotal
                    / $totalWaste
                ) * 100
                : 0;


        /*
        |--------------------------------------------------------------------------
        | Return Public Dashboard
        |--------------------------------------------------------------------------
        */

        return view(
            'public.dashboard',
            compact(
                'selectedStream',
                'selectedYear',
                'availableYears',

                'totalWaste',
                'totalRecords',
                'averageWeight',

                'recyclingTotal',
                'landfillTotal',
                'recyclingRate',

                'totalLocations',
                'totalWasteTypes',
                'totalVehicles',

                'wasteByType',
                'wasteByLocation',

                'monthlyWaste',
                'monthlyCollections',
                'monthlyAverageWeight',

                'overallMonthly'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Month Name Helper
    |--------------------------------------------------------------------------
    */

    private function monthName(int $month): string
    {
        $months = [
            1  => 'Jan',
            2  => 'Feb',
            3  => 'Mar',
            4  => 'Apr',
            5  => 'May',
            6  => 'Jun',
            7  => 'Jul',
            8  => 'Aug',
            9  => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];

        return $months[$month];
    }
}