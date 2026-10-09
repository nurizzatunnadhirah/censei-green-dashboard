@extends('layouts.app')



@section('title', 'Dashboard')



@section('content')



<div class="container-lg">



    {{-- ========================================== --}}

    {{-- HEADER --}}

    {{-- ========================================== --}}



    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">



        <div>



            <h4 class="fw-bold mb-0">

                Welcome Back, {{ Auth::user()->name }}.

            </h4>



            <h5 class="mb-0">

                {{ date('d M Y') }}

            </h5>



        </div>





        {{-- Filters --}}

        <form

            method="GET"

            action="{{ route('dashboard') }}"

            id="dashboardFilterForm"

            class="d-flex align-items-center gap-3 flex-wrap">



            {{-- Waste Stream --}}

            <div class="d-flex align-items-center gap-2">



                <label

                    for="waste_stream"

                    class="form-label mb-0">



                    Waste Stream:



                </label>



                <select

                    name="waste_stream"

                    id="waste_stream"

                    class="form-select"

                    style="width: 150px;"

                    onchange="

                        document.getElementById('year').value = '';

                        document.getElementById('dashboardFilterForm').submit();

                    ">



                    <option

                        value="Overall"

                        {{ $selectedStream === 'Overall' ? 'selected' : '' }}>



                        Overall



                    </option>



                    <option

                        value="Recycling"

                        {{ $selectedStream === 'Recycling' ? 'selected' : '' }}>



                        Recycling



                    </option>



                    <option

                        value="Landfill"

                        {{ $selectedStream === 'Landfill' ? 'selected' : '' }}>



                        Landfill



                    </option>



                </select>



            </div>





            {{-- Year --}}

            <div class="d-flex align-items-center gap-2">



                <label

                    for="year"

                    class="form-label mb-0">



                    Year:



                </label>



                <select

                    name="year"

                    id="year"

                    class="form-select"

                    style="width: 140px;"

                    onchange="this.form.submit()">



                    <option value="">

                        All Years

                    </option>



                    @foreach($availableYears as $availableYear)



                        <option

                            value="{{ $availableYear }}"

                            {{ $selectedYear == $availableYear ? 'selected' : '' }}>



                            {{ $availableYear }}



                        </option>



                    @endforeach



                </select>



            </div>



        </form>



    </div>





    {{-- ========================================== --}}

    {{-- OVERALL KPI CARDS --}}

    {{-- ========================================== --}}



    @if($selectedStream === 'Overall')



        <div class="row g-4 mb-4">



            <div class="col-md-6 col-xl-3">



                <div class="card kpi-organic">



                    <div class="card-body">



                        <h6>

                            Total Waste

                        </h6>



                        <h2 class="fw-bold">

                            {{ number_format($totalWaste, 2) }} kg

                        </h2>



                        <small>

                            Recycling + Landfill

                        </small>



                    </div>



                </div>



            </div>





            <div class="col-md-6 col-xl-3">



                <div class="card kpi-inorganic">



                    <div class="card-body">



                        <h6>

                            Recycling Waste

                        </h6>



                        <h2 class="fw-bold">

                            {{ number_format($recyclingTotal, 2) }} kg

                        </h2>



                        <small>

                            Total recycling waste

                        </small>



                    </div>



                </div>



            </div>





            <div class="col-md-6 col-xl-3">



                <div class="card kpi-toxic">



                    <div class="card-body">



                        <h6>

                            Landfill Waste

                        </h6>



                        <h2 class="fw-bold">

                            {{ number_format($landfillTotal, 2) }} kg

                        </h2>



                        <small>

                            Total waste to landfill

                        </small>



                    </div>



                </div>



            </div>





            <div class="col-md-6 col-xl-3">



                <div class="card kpi-carbon">



                    <div class="card-body">



                        <h6>

                            Total Records

                        </h6>



                        <h2 class="fw-bold">

                            {{ number_format($totalRecords) }}

                        </h2>



                        <small>

                            All waste records

                        </small>



                    </div>



                </div>



            </div>



        </div>





    {{-- ========================================== --}}

    {{-- RECYCLING KPI CARDS --}}

    {{-- ========================================== --}}



    @elseif($selectedStream === 'Recycling')



        <div class="row g-4 mb-4">



            <div class="col-md-6 col-xl-3">



                <div class="card kpi-organic">



                    <div class="card-body">



                        <h6>

                            Total Recycling Waste

                        </h6>



                        <h2 class="fw-bold">

                            {{ number_format($totalWaste, 2) }} kg

                        </h2>



                        <small>

                            Selected period

                        </small>



                    </div>



                </div>



            </div>





            <div class="col-md-6 col-xl-3">



                <div class="card kpi-inorganic">



                    <div class="card-body">



                        <h6>

                            Total Records

                        </h6>



                        <h2 class="fw-bold">

                            {{ number_format($totalRecords) }}

                        </h2>



                        <small>

                            Recycling records

                        </small>



                    </div>



                </div>



            </div>





            <div class="col-md-6 col-xl-3">



                <div class="card kpi-toxic">



                    <div class="card-body">



                        <h6>

                            Locations / Programs

                        </h6>



                        <h2 class="fw-bold">

                            {{ $totalLocations }}

                        </h2>



                        <small>

                            Active locations

                        </small>



                    </div>



                </div>



            </div>





            <div class="col-md-6 col-xl-3">



                <div class="card kpi-carbon">



                    <div class="card-body">



                        <h6>

                            Waste Types

                        </h6>



                        <h2 class="fw-bold">

                            {{ $totalWasteTypes }}

                        </h2>



                        <small>

                            Waste types recorded

                        </small>



                    </div>



                </div>



            </div>



        </div>





    {{-- ========================================== --}}

    {{-- LANDFILL KPI CARDS --}}

    {{-- ========================================== --}}



    @else



        <div class="row g-4 mb-4">



            <div class="col-md-6 col-xl-3">



                <div class="card kpi-organic">



                    <div class="card-body">



                        <h6>

                            Total Landfill Waste

                        </h6>



                        <h2 class="fw-bold">

                            {{ number_format($totalWaste, 2) }} kg

                        </h2>



                        <small>

                            Selected period

                        </small>



                    </div>



                </div>



            </div>





            <div class="col-md-6 col-xl-3">



                <div class="card kpi-inorganic">



                    <div class="card-body">



                        <h6>

                            Collection Records

                        </h6>



                        <h2 class="fw-bold">

                            {{ number_format($totalRecords) }}

                        </h2>



                        <small>

                            Landfill collections

                        </small>



                    </div>



                </div>



            </div>





            <div class="col-md-6 col-xl-3">



                <div class="card kpi-toxic">



                    <div class="card-body">



                        <h6>

                            Average Collection Weight

                        </h6>



                        <h2 class="fw-bold">

                            {{ number_format($averageWeight, 2) }} kg

                        </h2>



                        <small>

                            Average per collection

                        </small>



                    </div>



                </div>



            </div>





            <div class="col-md-6 col-xl-3">



                <div class="card kpi-carbon">



                    <div class="card-body">



                        <h6>

                            Collection Vehicles

                        </h6>



                        <h2 class="fw-bold">

                            {{ $totalVehicles }}

                        </h2>



                        <small>

                            Unique vehicle numbers

                        </small>



                    </div>



                </div>



            </div>



        </div>



    @endif





        {{-- Recycling Waste Category Breakdown: Overall and Recycling --}}
    @if($selectedStream !== 'Landfill')
        @php
            $categoryBreakdownTotal = $wasteByCategory->sum('total_weight');
        @endphp

        <div class="card mb-4">
            <div class="card-header">
                <strong>Recycling Waste Category Breakdown</strong>
            </div>
            <div class="card-body">
                <p class="text-body-secondary small mb-3">
                    Breakdown of classified recycling waste only. Landfill waste is not included.
                </p>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Waste Category</th>
                                <th class="text-end">Total Weight (kg)</th>
                                <th class="text-end">Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wasteByCategory as $category)
                                <tr>
                                    <td>{{ $category->category_name }}</td>
                                    <td class="text-end">{{ number_format($category->total_weight, 2) }}</td>
                                    <td class="text-end">
                                        {{ $categoryBreakdownTotal > 0 ? number_format($category->total_weight / $categoryBreakdownTotal * 100, 2) : '0.00' }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-body-secondary py-4">
                                        No categorized recycling waste available for the selected year.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-group-divider">
                            <tr class="fw-semibold">
                                <td>Total Classified Recycling Waste</td>
                                <td class="text-end">{{ number_format($categoryBreakdownTotal, 2) }}</td>
                                <td class="text-end">{{ $categoryBreakdownTotal > 0 ? '100.00' : '0.00' }}%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif

{{-- ========================================== --}}

    {{-- OVERALL ANALYTICS --}}

    {{-- ========================================== --}}



    @if($selectedStream === 'Overall')



        <div class="row g-4">



            <div class="col-12">



                <div class="card">



                    <div class="card-header">

                        Recycling vs Landfill by Month

                    </div>



                    <div class="card-body">



                        <div style="height: 380px;">



                            <canvas id="overallStreamChart"></canvas>



                        </div>



                    </div>



                </div>



            </div>



        </div>





    {{-- ========================================== --}}

{{-- RECYCLING ANALYTICS --}}

{{-- ========================================== --}}



@elseif($selectedStream === 'Recycling')



    <div class="row g-4">



        {{-- Waste Type --}}

        <div class="col-lg-6">



            <div class="card">



                <div class="card-header">

                    Waste Distribution by Type

                </div>



                <div class="card-body">



                    <div style="height:350px">



                        <canvas id="wasteChart"></canvas>



                    </div>



                </div>



            </div>



        </div>





        {{-- Waste Category --}}

        <div class="col-lg-6">



            <div class="card">



                <div class="card-header">

                    Waste Distribution by Category

                </div>



                <div class="card-body">



                    <div style="height:350px">



                        <canvas id="wasteCategoryChart"></canvas>



                    </div>



                </div>



            </div>



        </div>





        {{-- Location Chart --}}

        <div class="col-12">



            <div class="card">



                <div class="card-header">

                    Waste by Location / Program

                </div>



                <div class="card-body">



                    <div style="height:350px">



                        <canvas id="locationChart"></canvas>



                    </div>



                </div>



            </div>



        </div>



    </div>





    {{-- ========================================== --}}

    {{-- LANDFILL ANALYTICS --}}

    {{-- ========================================== --}}



    @else



        <div class="row g-4">



            {{-- Average Weight --}}

            <div class="col-lg-6">



                <div class="card">



                    <div class="card-header">

                        Average Waste per Collection by Month

                    </div>



                    <div class="card-body">



                        <div style="height:350px">



                            <canvas id="averageCollectionChart"></canvas>



                        </div>



                    </div>



                </div>



            </div>





            {{-- Collection Frequency --}}

            <div class="col-lg-6">



                <div class="card">



                    <div class="card-header">

                        Monthly Collection Frequency

                    </div>



                    <div class="card-body">



                        <div style="height:350px">



                            <canvas id="collectionCountChart"></canvas>



                        </div>



                    </div>



                </div>



            </div>



        </div>



    @endif





    {{-- ========================================== --}}

    {{-- MONTHLY WASTE TREND --}}

    {{-- ========================================== --}}



    @if($selectedStream !== 'Overall')



        <div class="row g-4 mt-1">



            <div class="col-12">



                <div class="card">



                    <div class="card-header">



                        @if($selectedStream === 'Landfill')



                            Monthly Landfill Waste Trend



                        @else



                            Monthly Recycling Waste Trend



                        @endif



                    </div>



                    <div class="card-body">



                        <div style="height:350px">



                            <canvas id="monthlyWasteChart"></canvas>



                        </div>



                    </div>



                </div>



            </div>



        </div>



    @endif



</div>





{{-- ========================================== --}}

{{-- CHART DATA --}}

{{-- ========================================== --}}



<script>



const selectedStream =

    @json($selectedStream);





@if($selectedStream === 'Overall')



const overallMonthLabels =

    @json($overallMonthly->pluck('month'));



const overallRecyclingData =

    @json($overallMonthly->pluck('recycling'));



const overallLandfillData =

    @json($overallMonthly->pluck('landfill'));



@endif





@if($selectedStream === 'Recycling')



const wasteLabels =

    @json($wasteByType->pluck('type_name'));



const wasteData =

    @json($wasteByType->pluck('total_weight'));





const categoryLabels =

    @json($wasteByCategory->pluck('category_name'));



const categoryData =

    @json($wasteByCategory->pluck('total_weight'));





const locationLabels =

    @json($wasteByLocation->pluck('location_name'));



const locationData =

    @json($wasteByLocation->pluck('total_weight'));



@endif





@if($selectedStream === 'Landfill')



const averageMonthLabels =

    @json($monthlyAverageWeight->pluck('month'));



const averageWeightData =

    @json($monthlyAverageWeight->pluck('average_weight'));



const collectionMonthLabels =

    @json($monthlyCollections->pluck('month'));



const collectionCountData =

    @json($monthlyCollections->pluck('collection_count'));



@endif





@if($selectedStream !== 'Overall')



const monthlyLabels =

    @json($monthlyWaste->pluck('month'));



const monthlyData =

    @json($monthlyWaste->pluck('total_weight'));



@endif



</script>



@endsection





@push('scripts')



<script>



@if($selectedStream === 'Overall')



/*

|--------------------------------------------------------------------------

| Overall Recycling vs Landfill

|--------------------------------------------------------------------------

*/



const overallStreamChart = new Chart(

    document.getElementById('overallStreamChart'),

    {

        type: 'line',



        data: {



            labels: overallMonthLabels,



            datasets: [



                {

                    label: 'Recycling',



                    data: overallRecyclingData,



                    borderColor: '#4CAF50',



                    backgroundColor: '#4CAF50',



                    fill: false,



                    tension: 0.3,



                    borderWidth: 3,



                    pointRadius: 4,



                    pointHoverRadius: 7

                },



                {

                    label: 'Waste to Landfill',



                    data: overallLandfillData,



                    borderColor: '#FF5722',



                    backgroundColor: '#FF5722',



                    fill: false,



                    tension: 0.3,



                    borderWidth: 3,



                    pointRadius: 4,



                    pointHoverRadius: 7

                }



            ]



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



                    enabled: true,



                    mode: 'index',



                    intersect: false,



                    callbacks: {



                        label: function(context) {



                            const value =

                                Number(context.raw || 0);



                            return context.dataset.label +

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



    }

);



@endif





@if($selectedStream === 'Recycling')



/*

|--------------------------------------------------------------------------

| Recycling Pie Chart

|--------------------------------------------------------------------------

*/



const wasteChart = new Chart(

    document.getElementById('wasteChart'),

    {

        type: 'pie',



        data: {



            labels: wasteLabels,



            datasets: [{



                data: wasteData,



                backgroundColor: [

                    '#4CAF50',

                    '#2196F3',

                    '#FFC107',

                    '#FF5722',

                    '#9C27B0',

                    '#795548',

                    '#00BCD4',

                    '#8BC34A',

                    '#3F51B5',

                    '#FF9800'

                ],



                borderWidth: 2



            }]



        },



        plugins: [

            ChartDataLabels

        ],



        options: {



            responsive: true,

            maintainAspectRatio: false,



            plugins: {



                legend: {

                    position: 'bottom'

                },



                datalabels: {



                    color: '#ffffff',



                    font: {

                        weight: 'bold',

                        size: 13

                    },



                    formatter: function(

                        value,

                        context

                    ) {



                        const data =

                            context

                                .chart

                                .data

                                .datasets[0]

                                .data;



                        const total =

                            data.reduce(

                                (

                                    sum,

                                    currentValue

                                ) =>

                                    sum +

                                    Number(

                                        currentValue

                                    ),

                                0

                            );



                        if (total === 0) {

                            return '';

                        }



                        const percentage =

                            Number(value)

                            / total

                            * 100;



                        if (percentage < 10) {

                            return '';

                        }



                        return percentage

                            .toFixed(1)

                            + '%';

                    }



                }



            }



        }



    }

);



/*

|--------------------------------------------------------------------------

| Recycling Waste Category Chart

|--------------------------------------------------------------------------

*/



const wasteCategoryChart = new Chart(

    document.getElementById('wasteCategoryChart'),

    {

        type: 'pie',



        data: {



            labels: categoryLabels,



            datasets: [{



                data: categoryData,



                backgroundColor: [

                    '#4CAF50',

                    '#2196F3',

                    '#FF5722'

                ],



                borderWidth: 2



            }]



        },



        plugins: [

            ChartDataLabels

        ],



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



                },



                datalabels: {



                    color: '#ffffff',



                    font: {

                        weight: 'bold',

                        size: 13

                    },



                    formatter: function(

                        value,

                        context

                    ) {



                        const data =

                            context

                                .chart

                                .data

                                .datasets[0]

                                .data;



                        const total =

                            data.reduce(

                                (

                                    sum,

                                    currentValue

                                ) =>

                                    sum +

                                    Number(

                                        currentValue

                                    ),

                                0

                            );



                        if (total === 0) {

                            return '';

                        }



                        const percentage =

                            Number(value)

                            / total

                            * 100;



                        if (percentage < 10) {

                            return '';

                        }



                        return percentage

                            .toFixed(1)

                            + '%';

                    }



                }



            }



        }



    }

);





/*

|--------------------------------------------------------------------------

| Recycling Location Chart

|--------------------------------------------------------------------------

*/



const locationChart = new Chart(

    document.getElementById('locationChart'),

    {

        type: 'bar',



        data: {



            labels: locationLabels,



            datasets: [{



                label: 'Waste Collected (kg)',



                data: locationData,



                backgroundColor:

                    'rgba(32, 201, 151, 0.82)',



                borderColor:

                    'rgb(32, 201, 151)',



                borderWidth: 1,



                borderRadius: 6,



                hoverBackgroundColor:

                    'rgba(32, 201, 151, 1)'



            }]



        },



        options: {



            responsive: true,

            maintainAspectRatio: false,



            scales: {



                y: {



                    beginAtZero: true,



                    title: {

                        display: true,

                        text: 'Weight (kg)'

                    }



                }



            },



            plugins: {



                legend: {

                    display: false

                }



            }



        }



    }

);



@endif





@if($selectedStream === 'Landfill')



/*

|--------------------------------------------------------------------------

| Average Waste per Collection

|--------------------------------------------------------------------------

*/



const averageCollectionChart = new Chart(

    document.getElementById(

        'averageCollectionChart'

    ),

    {

        type: 'bar',



        data: {



            labels: averageMonthLabels,



            datasets: [{



                label: 'Average Weight (kg)',



                data: averageWeightData,



                backgroundColor:

                    'rgba(54, 162, 235, 0.82)',



                borderColor:

                    'rgb(54, 162, 235)',



                borderWidth: 1,



                borderRadius: 6,



                hoverBackgroundColor:

                    'rgba(54, 162, 235, 1)'



            }]



        },



        options: {



            responsive: true,

            maintainAspectRatio: false,



            scales: {



                y: {



                    beginAtZero: true,



                    title: {

                        display: true,

                        text: 'Average Weight (kg)'

                    }



                }



            },



            plugins: {



                legend: {

                    display: false

                }



            }



        }



    }

);





/*

|--------------------------------------------------------------------------

| Monthly Collection Frequency

|--------------------------------------------------------------------------

*/



const collectionCountChart = new Chart(

    document.getElementById(

        'collectionCountChart'

    ),

    {

        type: 'bar',



        data: {



            labels: collectionMonthLabels,



            datasets: [{



                label: 'Collections',



                data: collectionCountData,



                backgroundColor:

                    'rgba(255, 159, 64, 0.82)',



                borderColor:

                    'rgb(255, 159, 64)',



                borderWidth: 1,



                borderRadius: 6,



                hoverBackgroundColor:

                    'rgba(255, 159, 64, 1)'



            }]



        },



        options: {



            responsive: true,

            maintainAspectRatio: false,



            scales: {



                y: {



                    beginAtZero: true,



                    ticks: {

                        precision: 0

                    },



                    title: {

                        display: true,

                        text: 'Number of Collections'

                    }



                }



            },



            plugins: {



                legend: {

                    display: false

                }



            }



        }



    }

);



@endif





@if($selectedStream !== 'Overall')



/*

|--------------------------------------------------------------------------

| Monthly Waste Trend

|--------------------------------------------------------------------------

*/



const monthlyWasteChart = new Chart(

    document.getElementById(

        'monthlyWasteChart'

    ),

    {

        type: 'line',



        data: {



            labels: monthlyLabels,



            datasets: [{



                label:

                    selectedStream === 'Landfill'

                        ? 'Landfill Waste (kg)'

                        : 'Recycling Waste (kg)',



                data: monthlyData,



                fill: false,



                tension: 0.3,



                borderWidth: 3,



                pointRadius: 4,



                pointHoverRadius: 7



            }]



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

                    display: true

                },



                tooltip: {

                    enabled: true,

                    mode: 'index',

                    intersect: false

                }



            },



            scales: {



                y: {



                    beginAtZero: true,



                    title: {

                        display: true,

                        text: 'Weight (kg)'

                    }



                }



            }



        }



    }

);



@endif



</script>



@endpush