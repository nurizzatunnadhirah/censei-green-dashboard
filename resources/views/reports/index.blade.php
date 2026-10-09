@extends('layouts.app')



@section('title', 'Reports')



@section('content')



<div class="container-fluid px-4 py-4">



    {{-- Page Header --}}

    <div class="mb-4">

        <h2 class="fw-bold mb-1">

            Reports

        </h2>



        <div class="text-body-secondary">

            Generate and review waste management reports.

        </div>

    </div>





    {{-- Filter Card --}}

    <div class="card mb-4">



        <div class="card-header">

            <strong>Report Filters</strong>

        </div>



        <div class="card-body">



            {{-- Validation Errors --}}

            @if($errors->any())



                <div class="alert alert-danger">



                    <ul class="mb-0">



                        @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach



                    </ul>



                </div>



            @endif





            <form method="GET" action="{{ route('reports.index') }}">



                <div class="row g-3">



                    {{-- Year --}}

                    <div class="col-md-3">



                        <label

                            for="year"

                            class="form-label"

                        >

                            Year

                        </label>



                        <select

                            name="year"

                            id="year"

                            class="form-select"

                        >



                            <option value="">

                                All Years

                            </option>



                            @foreach($availableYears as $availableYear)



                                <option

                                    value="{{ $availableYear }}"

                                    {{ request('year') == $availableYear ? 'selected' : '' }}

                                >

                                    {{ $availableYear }}

                                </option>



                            @endforeach



                        </select>



                    </div>





                    {{-- Waste Stream --}}

                    <div class="col-md-3">



                        <label

                            for="waste_stream"

                            class="form-label"

                        >

                            Waste Stream

                        </label>



                        <select

                            name="waste_stream"

                            id="waste_stream"

                            class="form-select"

                        >



                            <option

                                value=""

                                {{ request('waste_stream') == '' ? 'selected' : '' }}

                            >

                                All Waste Streams

                            </option>



                            <option

                                value="recycling"

                                {{ request('waste_stream') == 'recycling' ? 'selected' : '' }}

                            >

                                Recycling

                            </option>



                            <option

                                value="landfill"

                                {{ request('waste_stream') == 'landfill' ? 'selected' : '' }}

                            >

                                Waste to Landfill

                            </option>



                        </select>



                    </div>





                    {{-- Location --}}

                    <div class="col-md-3">



                        <label

                            for="location_id"

                            class="form-label"

                        >

                            Location / Program

                        </label>



                        <select

                            name="location_id"

                            id="location_id"

                            class="form-select"

                        >



                            <option value="">

                                All Locations

                            </option>



                            @foreach($locations as $location)



                                <option

                                    value="{{ $location->id }}"

                                    {{ request('location_id') == $location->id ? 'selected' : '' }}

                                >

                                    {{ $location->location_name }}

                                </option>



                            @endforeach



                        </select>



                    </div>





                    {{-- Waste Type --}}

                    <div class="col-md-3">



                        <label

                            for="waste_type_id"

                            class="form-label"

                        >

                            Waste Type

                        </label>



                        <select

                            name="waste_type_id"

                            id="waste_type_id"

                            class="form-select"

                        >



                            <option value="">

                                All Waste Types

                            </option>



                            @foreach($wasteTypes as $wasteType)



                                <option

                                    value="{{ $wasteType->id }}"

                                    {{ request('waste_type_id') == $wasteType->id ? 'selected' : '' }}

                                >

                                    {{ $wasteType->type_name }}

                                </option>



                            @endforeach



                        </select>



                    </div>





                    {{-- From Date --}}

                    <div class="col-md-3">



                        <label

                            for="date_from"

                            class="form-label"

                        >

                            From Date

                        </label>



                        <input

                            type="date"

                            name="date_from"

                            id="date_from"

                            class="form-control"

                            value="{{ request('date_from') }}"

                        >



                    </div>





                    {{-- To Date --}}

                    <div class="col-md-3">



                        <label

                            for="date_to"

                            class="form-label"

                        >

                            To Date

                        </label>



                        <input

                            type="date"

                            name="date_to"

                            id="date_to"

                            class="form-control"

                            value="{{ request('date_to') }}"

                        >



                    </div>



                </div>





                {{-- Buttons --}}

                <div class="d-flex justify-content-end gap-2 mt-4 flex-wrap">



                    <a

                        href="{{ route('reports.index') }}"

                        class="btn btn-secondary"

                    >

                        Reset

                    </a>



                    <button

                        type="submit"

                        class="btn btn-primary"

                    >

                        Generate Report

                    </button>



                    <a

                        href="{{ route('reports.export.excel', request()->query()) }}"

                        class="btn btn-success"

                    >

                        Export Excel

                    </a>



                    <a

                        href="{{ route('reports.print', request()->query()) }}"

                        class="btn btn-info"

                        target="_blank"

                    >

                        Print / Save PDF

                    </a>



                </div>



            </form>



        </div>



    </div>





    {{-- Summary Cards --}}

    <div class="row g-3 mb-4">



        {{-- Total Waste --}}

        <div class="col-md-4 col-xl">



            <div class="card h-100">



                <div class="card-body">



                    <div class="text-body-secondary mb-1">

                        Total Waste

                    </div>



                    <div class="fs-4 fw-semibold">

                        {{ number_format($totalWaste, 2) }} kg

                    </div>



                </div>



            </div>



        </div>





        {{-- Total Records --}}

        <div class="col-md-4 col-xl">



            <div class="card h-100">



                <div class="card-body">



                    <div class="text-body-secondary mb-1">

                        Total Records

                    </div>



                    <div class="fs-4 fw-semibold">

                        {{ number_format($totalRecords) }}

                    </div>



                </div>



            </div>



        </div>





        {{-- Average Weight --}}

        <div class="col-md-4 col-xl">



            <div class="card h-100">



                <div class="card-body">



                    <div class="text-body-secondary mb-1">

                        Average Weight

                    </div>



                    <div class="fs-4 fw-semibold">

                        {{ number_format($averageWeight ?? 0, 2) }} kg

                    </div>



                </div>



            </div>



        </div>





        {{-- Highest Waste Type --}}

        <div class="col-md-6 col-xl">



            <div class="card h-100">



                <div class="card-body">



                    <div class="text-body-secondary mb-1">

                        Highest Waste Type

                    </div>



                    @if($selectedStream === 'landfill')



                        <div class="fs-5 fw-semibold text-body-secondary">

                            Not Applicable

                        </div>



                    @else



                        <div class="fs-5 fw-semibold">

                            {{ $highestWasteType->type_name ?? 'No Data' }}

                        </div>



                        @if($highestWasteType)



                            <small class="text-body-secondary">



                                {{ number_format(

                                    $highestWasteType->total_weight,

                                    2

                                ) }} kg



                            </small>



                        @endif



                    @endif



                </div>



            </div>



        </div>





        {{-- Highest Location --}}

        <div class="col-md-6 col-xl">



            <div class="card h-100">



                <div class="card-body">



                    <div class="text-body-secondary mb-1">

                        Highest Location

                    </div>



                    @if($selectedStream === 'landfill')



                        <div class="fs-5 fw-semibold text-body-secondary">

                            Not Applicable

                        </div>



                    @else



                        <div class="fs-5 fw-semibold">

                            {{ $highestLocation->location_name ?? 'No Data' }}

                        </div>



                        @if($highestLocation)



                            <small class="text-body-secondary">



                                {{ number_format(

                                    $highestLocation->total_weight,

                                    2

                                ) }} kg



                            </small>



                        @endif



                    @endif



                </div>



            </div>



        </div>



    </div>



    {{-- Analytics --}}

<div class="mb-4">



    <div class="mb-3">

        <h4 class="fw-semibold mb-1">

            Report Analytics

        </h4>



        <div class="text-body-secondary">

            Analytics based on the selected report filters.

        </div>

    </div>





    {{-- Recycling Analytics --}}

    @if($selectedStream !== 'landfill')



        <div class="row g-4 mb-4">



            {{-- Waste by Type --}}

            <div class="col-lg-6">



                <div class="card h-100">



                    <div class="card-header">

                        <strong>Recycling Waste by Type</strong>

                    </div>



                    <div class="card-body">



                        @if($wasteByType->count())



                            <div style="height: 340px;">

                                <canvas id="wasteTypeChart"></canvas>

                            </div>



                        @else



                            <div class="text-center text-body-secondary py-5">

                                No recycling waste type data available for the selected filters.

                            </div>



                        @endif



                    </div>



                </div>



            </div>





            {{-- Waste by Location --}}

            <div class="col-lg-6">



                <div class="card h-100">



                    <div class="card-header">

                        <strong>Recycling Waste by Location / Program</strong>

                    </div>



                    <div class="card-body">



                        @if($wasteByLocation->count())



                            <div style="height: 340px;">

                                <canvas id="wasteLocationChart"></canvas>

                            </div>



                        @else



                            <div class="text-center text-body-secondary py-5">

                                No recycling location data available for the selected filters.

                            </div>



                        @endif



                    </div>



                </div>



            </div>



        </div>



    
        {{-- Recycling Waste Category Breakdown --}}
        <div class="card mb-4">
            <div class="card-header">
                <strong>Recycling Waste Category Breakdown</strong>
            </div>
            <div class="card-body">
                <p class="text-body-secondary mb-3">
                    Distribution of classified recycling waste by category.
                    Landfill waste is not included.
                </p>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Waste Category</th>
                                <th class="text-end">Total Weight (kg)</th>
                                <th class="text-end">Percentage (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wasteByCategory as $category)
                                <tr>
                                    <td>{{ $category->category_name }}</td>
                                    <td class="text-end">{{ number_format($category->total_weight, 2) }}</td>
                                    <td class="text-end">{{ number_format($category->percentage, 2) }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-body-secondary">
                                        No recycling waste category data available for the selected filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($wasteByCategory->count())
                            <tfoot>
                                <tr class="fw-semibold">
                                    <td>Total Classified Recycling Waste</td>
                                    <td class="text-end">{{ number_format($wasteByCategory->sum('total_weight'), 2) }}</td>
                                    <td class="text-end">100.00%</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

    @endif





    {{-- Monthly Trend --}}

    <div class="card">



        <div class="card-header">

            <strong>

                @if($selectedStream === 'recycling')

                    Recycling Waste Trend

                @elseif($selectedStream === 'landfill')

                    Waste to Landfill Trend

                @else

                    Monthly Waste Trend

                @endif

            </strong>

        </div>



        <div class="card-body">



            @if($monthlyWaste->count() || $monthlyLandfill->count())



                <div style="height: 380px;">

                    <canvas id="monthlyTrendChart"></canvas>

                </div>



            @else



                <div class="text-center text-body-secondary py-5">

                    No monthly analytics available for the selected filters.

                </div>



            @endif



        </div>



    </div>



</div>



    {{-- Recycling Report --}}

    @if($selectedStream !== 'landfill')



        <div class="card mb-4">



            <div class="card-header d-flex justify-content-between align-items-center">



                <strong>

                    Recycling Waste Records

                </strong>



                <span class="text-body-secondary">

                    {{ $recyclingTotalRecords }} record(s)

                </span>



            </div>





            <div class="card-body">



                <div class="table-responsive">



                    <table class="table table-hover align-middle mb-0">



                        <thead>



                            <tr>



                                <th style="white-space: nowrap;">

                                    Date

                                </th>



                                <th style="min-width: 220px;">

                                    Location / Program

                                </th>



                                <th style="white-space: nowrap;">

                                    Waste Type

                                </th>



                                <th style="white-space: nowrap;">

                                    Weight

                                </th>



                                <th style="min-width: 160px;">

                                    Collection Method

                                </th>



                                <th style="white-space: nowrap;">

                                    Recorded By

                                </th>



                                <th style="min-width: 250px; max-width: 320px;">

                                    Remarks

                                </th>



                            </tr>



                        </thead>





                        <tbody>



                            @forelse($records as $record)



                                <tr>



                                    <td style="white-space: nowrap;">



                                        {{ \Carbon\Carbon::parse(

                                            $record->collection_date

                                        )->format('d M Y') }}



                                    </td>





                                    <td style="white-space: normal;">



                                        {{ $record->location->location_name ?? '-' }}



                                    </td>





                                    <td>



                                        {{ $record->wasteType->type_name ?? '-' }}



                                    </td>





                                    <td style="white-space: nowrap;">



                                        {{ number_format(

                                            $record->actual_weight,

                                            2

                                        ) }} kg



                                    </td>





                                    <td>



                                        {{ $record->collection_method ?? '-' }}



                                    </td>





                                    <td>



                                        {{ $record->recordedBy->name ?? '-' }}



                                    </td>





                                    <td

                                        style="

                                            max-width: 320px;

                                            white-space: normal;

                                            word-wrap: break-word;

                                        "

                                    >



                                        {{ $record->remarks ?? '-' }}



                                    </td>



                                </tr>



                            @empty



                                <tr>



                                    <td

                                        colspan="7"

                                        class="text-center py-5 text-body-secondary"

                                    >

                                        No recycling records found for the selected filters.

                                    </td>



                                </tr>



                            @endforelse



                        </tbody>



                    </table>



                </div>



            </div>





            @if($records->hasPages())



                <div class="card-footer">



                    {{ $records->links() }}



                </div>



            @endif



        </div>



    @endif





    {{-- Landfill Report --}}

    @if($selectedStream !== 'recycling')



        <div class="card mb-4">



            <div class="card-header d-flex justify-content-between align-items-center">



                <strong>

                    Waste to Landfill Records

                </strong>



                <span class="text-body-secondary">

                    {{ $landfillTotalRecords }} record(s)

                </span>



            </div>





            <div class="card-body">



                <div class="table-responsive">



                    <table class="table table-hover align-middle mb-0">



                        <thead>



                            <tr>



                                <th style="white-space: nowrap;">

                                    Date

                                </th>



                                <th>

                                    Vehicle Number

                                </th>



                                <th style="white-space: nowrap;">

                                    Time In

                                </th>



                                <th style="white-space: nowrap;">

                                    Time Out

                                </th>



                                <th style="white-space: nowrap;">

                                    Weight

                                </th>



                            </tr>



                        </thead>





                        <tbody>



                            @forelse($landfillRecords as $landfillRecord)



                                <tr>



                                    <td style="white-space: nowrap;">



                                        {{ \Carbon\Carbon::parse(

                                            $landfillRecord->collection_date

                                        )->format('d M Y') }}



                                    </td>





                                    <td>



                                        {{ $landfillRecord->vehicle_no ?? '-' }}



                                    </td>





                                    <td>



                                        {{ $landfillRecord->time_in

        ? \Carbon\Carbon::parse($landfillRecord->time_in)->format('H:i')

        : '-' }}



                                    </td>





                                    <td>



                                        {{ $landfillRecord->time_out

        ? \Carbon\Carbon::parse($landfillRecord->time_out)->format('H:i')

        : '-' }}



                                    </td>





                                    <td style="white-space: nowrap;">



                                        {{ number_format(

                                            $landfillRecord->actual_weight,

                                            2

                                        ) }} kg



                                    </td>



                                </tr>



                            @empty



                                <tr>



                                    <td

                                        colspan="5"

                                        class="text-center py-5 text-body-secondary"

                                    >

                                        No landfill records found for the selected filters.

                                    </td>



                                </tr>



                            @endforelse



                        </tbody>



                    </table>



                </div>



            </div>





            @if($landfillRecords->hasPages())



                <div class="card-footer">



                    {{ $landfillRecords->links() }}



                </div>



            @endif



        </div>



    @endif



</div>





{{-- Disable Recycling-only Filters When Landfill Is Selected --}}

<script>



document.addEventListener('DOMContentLoaded', function () {



    const wasteStream =

        document.getElementById('waste_stream');



    const locationSelect =

        document.getElementById('location_id');



    const wasteTypeSelect =

        document.getElementById('waste_type_id');





    function updateFilters() {



        const isLandfill =

            wasteStream.value === 'landfill';





        locationSelect.disabled = isLandfill;



        wasteTypeSelect.disabled = isLandfill;





        if (isLandfill) {



            locationSelect.value = '';



            wasteTypeSelect.value = '';



        }



    }





    wasteStream.addEventListener(

        'change',

        updateFilters

    );





    updateFilters();



});



</script>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



<script>

document.addEventListener('DOMContentLoaded', function () {



    /*

    |--------------------------------------------------------------------------

    | Recycling Waste by Type

    |--------------------------------------------------------------------------

    */



    const wasteTypeCanvas =

        document.getElementById('wasteTypeChart');



    if (wasteTypeCanvas) {



        new Chart(wasteTypeCanvas, {



            type: 'doughnut',



            data: {



                labels: @json(

                    $wasteByType->pluck('type_name')->values()

                ),



                datasets: [{

                    data: @json(

                        $wasteByType->pluck('total_weight')

                            ->map(fn ($value) => (float) $value)

                            ->values()

                    ),



                    backgroundColor: [

                        '#4CAF50',

                        '#2196F3',

                        '#FFC107',

                        '#FF5722',

                        '#9C27B0',

                        '#795548',

                        '#00BCD4'

                    ],



                    borderWidth: 1

                }]



            },



            options: {



                responsive: true,



                maintainAspectRatio: false,



                plugins: {



                    legend: {

                        position: 'bottom'

                    },



                    tooltip: {



                        callbacks: {



                            label: function(context) {



                                const value =

                                    Number(context.raw || 0);



                                return context.label +

                                    ': ' +

                                    value.toLocaleString(

                                        undefined,

                                        {

                                            minimumFractionDigits: 2,

                                            maximumFractionDigits: 2

                                        }

                                    ) +

                                    ' kg';

                            }



                        }



                    }



                }



            }



        });



    }





    /*

    |--------------------------------------------------------------------------

    | Recycling Waste by Location

    |--------------------------------------------------------------------------

    */



    const locationCanvas =

        document.getElementById('wasteLocationChart');



    if (locationCanvas) {



        new Chart(locationCanvas, {



            type: 'bar',



            data: {



                labels: @json(

                    $wasteByLocation

                        ->pluck('location_name')

                        ->values()

                ),



                datasets: [{



                    label: 'Weight (kg)',



                    data: @json(

                        $wasteByLocation

                            ->pluck('total_weight')

                            ->map(fn ($value) => (float) $value)

                            ->values()

                    ),



                    backgroundColor: '#4CAF50',



                    borderRadius: 5



                }]



            },



            options: {



                responsive: true,



                maintainAspectRatio: false,



                plugins: {



                    legend: {

                        display: false

                    },



                    tooltip: {



                        callbacks: {



                            label: function(context) {



                                return Number(

                                    context.raw || 0

                                ).toLocaleString(

                                    undefined,

                                    {

                                        minimumFractionDigits: 2,

                                        maximumFractionDigits: 2

                                    }

                                ) + ' kg';



                            }



                        }



                    }



                },



                scales: {



                    y: {



                        beginAtZero: true,



                        title: {

                            display: true,

                            text: 'Weight (kg)'

                        }



                    },



                    x: {



                        ticks: {

                            autoSkip: false,

                            maxRotation: 45,

                            minRotation: 0

                        }



                    }



                }



            }



        });



    }





    /*

    |--------------------------------------------------------------------------

    | Monthly Trend

    |--------------------------------------------------------------------------

    */



    const monthlyCanvas =

        document.getElementById('monthlyTrendChart');



    if (monthlyCanvas) {



        const recyclingMonthly =

            @json(

                $monthlyWaste->mapWithKeys(

                    fn ($item) => [

                        $item->month => (float) $item->total_weight

                    ]

                )

            );



        const landfillMonthly =

            @json(

                $monthlyLandfill->mapWithKeys(

                    fn ($item) => [

                        $item->month => (float) $item->total_weight

                    ]

                )

            );





        /*

        |--------------------------------------------------------------------------

        | Merge months from both streams

        |--------------------------------------------------------------------------

        */



        const allMonths = [

            ...new Set([

                ...Object.keys(recyclingMonthly),

                ...Object.keys(landfillMonthly)

            ])

        ].sort();





        const datasets = [];





        /*

        |--------------------------------------------------------------------------

        | Recycling Dataset

        |--------------------------------------------------------------------------

        */



        @if($selectedStream !== 'landfill')



            datasets.push({



                label: 'Recycling',



                data: allMonths.map(

                    month => recyclingMonthly[month] ?? 0

                ),



                borderColor: '#4CAF50',



                backgroundColor: '#4CAF50',



                tension: 0.3,



                fill: false,



                pointRadius: 4,



                pointHoverRadius: 7



            });



        @endif





        /*

        |--------------------------------------------------------------------------

        | Landfill Dataset

        |--------------------------------------------------------------------------

        */



        @if($selectedStream !== 'recycling')



            datasets.push({



                label: 'Waste to Landfill',



                data: allMonths.map(

                    month => landfillMonthly[month] ?? 0

                ),



                borderColor: '#FF5722',



                backgroundColor: '#FF5722',



                tension: 0.3,



                fill: false,



                pointRadius: 4,



                pointHoverRadius: 7



            });



        @endif





        new Chart(monthlyCanvas, {



            type: 'line',



            data: {



                labels: allMonths,



                datasets: datasets



            },



            options: {



                responsive: true,



                maintainAspectRatio: false,



                interaction: {

                    mode: 'index',

                    intersect: false

                },



                plugins: {



                    legend: {

                        position: 'bottom'

                    },



                    tooltip: {



                        callbacks: {



                            label: function(context) {



                                return context.dataset.label +

                                    ': ' +

                                    Number(

                                        context.raw || 0

                                    ).toLocaleString(

                                        undefined,

                                        {

                                            minimumFractionDigits: 2,

                                            maximumFractionDigits: 2

                                        }

                                    ) +

                                    ' kg';



                            }



                        }



                    }



                },



                scales: {



                    y: {



                        beginAtZero: true,



                        title: {

                            display: true,

                            text: 'Weight (kg)'

                        }



                    },



                    x: {



                        title: {

                            display: true,

                            text: 'Month'

                        }



                    }



                }



            }



        });



    }



});

</script>



@endsection
