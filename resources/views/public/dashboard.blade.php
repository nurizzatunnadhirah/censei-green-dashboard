@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="container-lg">

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            CENSEi Green Dashboard
        </h4>
        <h5 class="mb-0">
            Public Sustainability Overview
        </h5>
    </div>
</div>


{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">

        <form method="GET"
              action="{{ route('public.dashboard') }}"
              id="dashboardFilterForm"
              class="d-flex align-items-center gap-3 flex-wrap">

            {{-- Waste Stream --}}
            <div>
                <label for="waste_stream" class="form-label fw-semibold mb-1">
                    Waste Stream
                </label>

                <select name="waste_stream"
                        id="waste_stream"
                        class="form-select"
                        onchange="this.form.submit()">

                    <option value="Overall"
                        {{ $selectedStream === 'Overall' ? 'selected' : '' }}>
                        Overall
                    </option>

                    <option value="Recycling"
                        {{ $selectedStream === 'Recycling' ? 'selected' : '' }}>
                        Recycling
                    </option>

                    <option value="Landfill"
                        {{ $selectedStream === 'Landfill' ? 'selected' : '' }}>
                        Landfill
                    </option>

                </select>
            </div>


            {{-- Year --}}
            <div>
                <label for="year" class="form-label fw-semibold mb-1">
                    Year
                </label>

                <select name="year"
                        id="year"
                        class="form-select"
                        onchange="this.form.submit()">

                    <option value="">
                        All Years
                    </option>

                    @foreach ($availableYears as $year)
                        <option value="{{ $year }}"
                            {{ (string) $selectedYear === (string) $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach

                </select>
            </div>

        </form>

    </div>
</div>


{{-- ========================================================= --}}
{{-- OVERALL DASHBOARD --}}
{{-- ========================================================= --}}

@if ($selectedStream === 'Overall')

    {{-- KPI Cards --}}
    <div class="row g-4 mb-4">

        {{-- Total Waste --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card kpi-organic">
                <div class="card-body py-3">
                    <div class="fs-5 fw-semibold">
                        Total Waste
                    </div>

                    <div class="fs-2 fw-bold mt-2">
                        {{ number_format($totalWaste, 2) }} kg
                    </div>

                    <div class="text-body-secondary">
                        Recycling + Landfill
                    </div>
                </div>
            </div>
        </div>


        {{-- Recycling --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card kpi-inorganic">
                <div class="card-body py-3">
                    <div class="fs-5 fw-semibold">
                        Recycling Waste
                    </div>

                    <div class="fs-2 fw-bold mt-2">
                        {{ number_format($recyclingTotal, 2) }} kg
                    </div>
                </div>
            </div>
        </div>


        {{-- Landfill --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card kpi-toxic">
                <div class="card-body py-3">
                    <div class="fs-5 fw-semibold">
                        Landfill Waste
                    </div>

                    <div class="fs-2 fw-bold mt-2">
                        {{ number_format($landfillTotal, 2) }} kg
                    </div>
                </div>
            </div>
        </div>


        {{-- Records --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card kpi-carbon">
                <div class="card-body py-3">
                    <div class="fs-5 fw-semibold">
                        Total Records
                    </div>

                    <div class="fs-2 fw-bold mt-2">
                        {{ number_format($totalRecords) }}
                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- Recycling vs Landfill --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>Recycling vs Landfill by Month</strong>
        </div>

        <div class="card-body">
            <div style="height: 350px;">
                <canvas id="overallStreamChart"></canvas>
            </div>
        </div>
    </div>

@endif


{{-- ========================================================= --}}
{{-- RECYCLING DASHBOARD --}}
{{-- ========================================================= --}}

@if ($selectedStream === 'Recycling')

    {{-- KPI Cards --}}
    <div class="row g-4 mb-4">

        {{-- Total Recycling Waste --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card kpi-organic">
                <div class="card-body py-3">
                    <div class="fs-5 fw-semibold">
                        Total Recycling Waste
                    </div>

                    <div class="fs-2 fw-bold mt-2">
                        {{ number_format($totalWaste, 2) }} kg
                    </div>
                </div>
            </div>
        </div>


        {{-- Total Records --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card kpi-inorganic">
                <div class="card-body py-3">
                    <div class="fs-5 fw-semibold">
                        Total Records
                    </div>

                    <div class="fs-2 fw-bold mt-2">
                        {{ number_format($totalRecords) }}
                    </div>
                </div>
            </div>
        </div>


        {{-- Locations --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card kpi-toxic">
                <div class="card-body py-3">
                    <div class="fs-5 fw-semibold">
                        Locations / Programs
                    </div>

                    <div class="fs-2 fw-bold mt-2">
                        {{ number_format($totalLocations) }}
                    </div>
                </div>
            </div>
        </div>


        {{-- Waste Types --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card kpi-carbon">
                <div class="card-body py-3">
                    <div class="fs-5 fw-semibold">
                        Waste Types
                    </div>

                    <div class="fs-2 fw-bold mt-2">
                        {{ number_format($totalWasteTypes) }}
                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- Recycling Charts --}}
    <div class="row g-4 mb-4">

        {{-- Waste by Type --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <strong>Waste Distribution by Type</strong>
                </div>

                <div class="card-body">
                    <div style="height: 350px;">
                        <canvas id="wasteChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


        {{-- Waste by Location --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <strong>Waste by Location / Program</strong>
                </div>

                <div class="card-body">
                    <div style="height: 350px;">
                        <canvas id="locationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- Monthly Recycling Trend --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>Monthly Recycling Waste</strong>
        </div>

        <div class="card-body">
            <div style="height: 350px;">
                <canvas id="monthlyWasteChart"></canvas>
            </div>
        </div>
    </div>

@endif


{{-- ========================================================= --}}
{{-- LANDFILL DASHBOARD --}}
{{-- ========================================================= --}}

@if ($selectedStream === 'Landfill')

    {{-- KPI Cards --}}
    <div class="row g-4 mb-4">

        {{-- Total Landfill Waste --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card kpi-organic">
                <div class="card-body py-3">
                    <div class="fs-5 fw-semibold">
                        Total Landfill Waste
                    </div>

                    <div class="fs-2 fw-bold mt-2">
                        {{ number_format($totalWaste, 2) }} kg
                    </div>
                </div>
            </div>
        </div>


        {{-- Collection Records --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card kpi-inorganic">
                <div class="card-body py-3">
                    <div class="fs-5 fw-semibold">
                        Collection Records
                    </div>

                    <div class="fs-2 fw-bold mt-2">
                        {{ number_format($totalRecords) }}
                    </div>
                </div>
            </div>
        </div>


        {{-- Average Weight --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card kpi-toxic">
                <div class="card-body py-3">
                    <div class="fs-5 fw-semibold">
                        Average Collection Weight
                    </div>

                    <div class="fs-2 fw-bold mt-2">
                        {{ number_format($averageWeight, 2) }} kg
                    </div>
                </div>
            </div>
        </div>


        {{-- Vehicles --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card kpi-carbon">
                <div class="card-body py-3">
                    <div class="fs-5 fw-semibold">
                        Collection Vehicles
                    </div>

                    <div class="fs-2 fw-bold mt-2">
                        {{ number_format($totalVehicles) }}
                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- Landfill Charts --}}
    <div class="row g-4 mb-4">

        {{-- Average Waste --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <strong>Average Waste per Collection by Month</strong>
                </div>

                <div class="card-body">
                    <div style="height: 350px;">
                        <canvas id="averageCollectionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


        {{-- Collection Frequency --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <strong>Monthly Collection Frequency</strong>
                </div>

                <div class="card-body">
                    <div style="height: 350px;">
                        <canvas id="collectionCountChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- Monthly Landfill Trend --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>Monthly Landfill Waste</strong>
        </div>

        <div class="card-body">
            <div style="height: 350px;">
                <canvas id="monthlyWasteChart"></canvas>
            </div>
        </div>
    </div>

@endif

</div>

{{-- ============================================================= --}}
{{-- CHARTS --}}
{{-- ============================================================= --}}

@push('scripts')

<script>

    /*
    |--------------------------------------------------------------------------
    | Overall Stream Chart
    |--------------------------------------------------------------------------
    */

    @if ($selectedStream === 'Overall')

        const overallLabels = @json(
            $overallMonthly->pluck('month')
        );

        const recyclingData = @json(
            $overallMonthly->pluck('recycling')
        );

        const landfillData = @json(
            $overallMonthly->pluck('landfill')
        );


        new Chart(
            document.getElementById('overallStreamChart'),
            {
                type: 'line',

                data: {
                    labels: overallLabels,

                    datasets: [
                        {
                            label: 'Recycling',
                            data: recyclingData,
                            borderColor: '#4CAF50',
                            backgroundColor: 'rgba(76, 175, 80, 0.15)',
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Landfill',
                            data: landfillData,
                            borderColor: '#FF5722',
                            backgroundColor: 'rgba(255, 87, 34, 0.15)',
                            tension: 0.3,
                            fill: true
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

                    elements: {
                        point: {
                            radius: 3,
                            hoverRadius: 6
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
                    },

                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false,

                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label +
                                        ': ' +
                                        Number(context.raw).toFixed(2) +
                                        ' kg';
                                }
                            }
                        }
                    }
                }
            }
        );

    @endif



    /*
    |--------------------------------------------------------------------------
    | Recycling Charts
    |--------------------------------------------------------------------------
    */

    @if ($selectedStream === 'Recycling')

        const wasteTypeLabels = @json(
            $wasteByType->pluck('type_name')
        );

        const wasteTypeData = @json(
            $wasteByType->pluck('total_weight')
        );


        new Chart(
            document.getElementById('wasteChart'),
            {
                type: 'pie',

                data: {
                    labels: wasteTypeLabels,

                    datasets: [
                        {
                            data: wasteTypeData,

                            backgroundColor: [
                                '#4CAF50',
                                '#8BC34A',
                                '#CDDC39',
                                '#FFC107',
                                '#FF9800',
                                '#FF5722',
                                '#795548',
                                '#607D8B',
                                '#00ACC1',
                                '#7E57C2'
                            ]
                        }
                    ]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    interaction: {
                        mode: 'nearest',
                        intersect: false
                    },

                    plugins: {
                        legend: {
                            position: 'right'
                        },

                        datalabels: {
                            formatter: function(value, context) {

                                const data =
                                    context.chart.data.datasets[0].data;

                                const total =
                                    data.reduce(
                                        (sum, current) =>
                                            sum + Number(current),
                                        0
                                    );

                                const percentage =
                                    total > 0
                                        ? (value / total * 100).toFixed(1)
                                        : 0;

                                return percentage + '%';
                            },

                            color: '#fff',

                            font: {
                                weight: 'bold'
                            }
                        }
                    }
                },

                plugins: [ChartDataLabels]
            }
        );


        const locationLabels = @json(
            $wasteByLocation->pluck('location_name')
        );

        const locationData = @json(
            $wasteByLocation->pluck('total_weight')
        );


        new Chart(
            document.getElementById('locationChart'),
            {
                type: 'bar',

                data: {
                    labels: locationLabels,

                    datasets: [
                        {
                            label: 'Waste Weight (kg)',
                            data: locationData,

                            backgroundColor:
                                'rgba(0, 150, 136, 0.7)'
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
                                text: 'Location / Program'
                            }
                        }
                    }
                }
            }
        );

    @endif



    /*
    |--------------------------------------------------------------------------
    | Landfill Charts
    |--------------------------------------------------------------------------
    */

    @if ($selectedStream === 'Landfill')

        const averageLabels = @json(
            $monthlyAverageWeight->pluck('month')
        );

        const averageData = @json(
            $monthlyAverageWeight->pluck('average_weight')
        );


        new Chart(
            document.getElementById('averageCollectionChart'),
            {
                type: 'bar',

                data: {
                    labels: averageLabels,

                    datasets: [
                        {
                            label: 'Average Weight (kg)',
                            data: averageData,

                            backgroundColor:
                                'rgba(255, 152, 0, 0.7)'
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

                    scales: {
                        y: {
                            beginAtZero: true,

                            title: {
                                display: true,
                                text: 'Average Weight (kg)'
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


        const collectionLabels = @json(
            $monthlyCollections->pluck('month')
        );

        const collectionData = @json(
            $monthlyCollections->pluck('collection_count')
        );


        new Chart(
            document.getElementById('collectionCountChart'),
            {
                type: 'bar',

                data: {
                    labels: collectionLabels,

                    datasets: [
                        {
                            label: 'Collection Records',
                            data: collectionData,

                            backgroundColor:
                                'rgba(121, 85, 72, 0.7)'
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



    /*
    |--------------------------------------------------------------------------
    | Monthly Waste Trend
    |--------------------------------------------------------------------------
    */

    @if ($selectedStream !== 'Overall')

        const monthlyWasteLabels = @json(
            $monthlyWaste->pluck('month')
        );

        const monthlyWasteData = @json(
            $monthlyWaste->pluck('total_weight')
        );


        new Chart(
            document.getElementById('monthlyWasteChart'),
            {
                type: 'line',

                data: {
                    labels: monthlyWasteLabels,

                    datasets: [
                        {
                            label:
                                '{{ $selectedStream }} Waste (kg)',

                            data: monthlyWasteData,

                            borderColor: '#4CAF50',

                            backgroundColor:
                                'rgba(76, 175, 80, 0.15)',

                            tension: 0.3,

                            fill: true
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

                    elements: {
                        point: {
                            radius: 3,
                            hoverRadius: 6
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
                    },

                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            }
        );

    @endif

</script>

@endpush

@endsection
