<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CENSEi Waste Management Report</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 30px;
            color: #222;
            background: #fff;
        }

        .report-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .report-header {
            border-bottom: 3px solid #2eb85c;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .report-header h1 {
            margin: 0 0 5px;
            font-size: 24px;
        }

        .report-header p {
            margin: 0;
            color: #666;
        }

        .section-title {
            margin-top: 25px;
            margin-bottom: 10px;
            font-size: 17px;
            font-weight: bold;
        }

        .filters {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .filters td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }

        .filters td:first-child {
            width: 180px;
            font-weight: bold;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 25px;
        }

        .summary-card {
            border: 1px solid #ccc;
            padding: 12px;
            border-radius: 6px;
        }

        .summary-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
        }

        .analytics-section {
            margin-top: 25px;
            margin-bottom: 30px;
        }

        .analytics-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .chart-card {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 15px;
            background: #fff;
            break-inside: avoid;
        }

        .chart-card-full {
            width: 100%;
            margin-bottom: 15px;
        }

        .chart-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .chart-wrapper {
            position: relative;
            height: 280px;
        }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 25px;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #bbb;
            padding: 7px;
            vertical-align: top;
        }

        .report-table th {
            background: #f1f1f1;
            text-align: left;
        }

        .report-table td:nth-child(1) {
            white-space: nowrap;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 9px 14px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #2eb85c;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .report-footer {
            margin-top: 25px;
            font-size: 11px;
            color: #666;
            text-align: right;
        }

        @media print {

            body {
                padding: 0;
            }

            .actions {
                display: none;
            }

            .report-container {
                max-width: none;
            }

            .summary-card,
            .chart-card,
            .analytics-section {
                break-inside: avoid;
            }

            .report-table tr {
                break-inside: avoid;
            }

            canvas {
                max-width: 100% !important;
            }

            @page {
                size: landscape;
                margin: 12mm;
            }
        }

    </style>

</head>

<body>

<div class="report-container">

    {{-- Buttons --}}
    <div class="actions">

        <a
            href="{{ route('reports.index', request()->query()) }}"
            class="btn btn-secondary"
        >
            Back to Reports
        </a>

        <button
            type="button"
            class="btn btn-primary"
            onclick="window.print()"
        >
            Print / Save PDF
        </button>

    </div>


    {{-- Header --}}
    <div class="report-header">

        <h1>
            CENSEi Waste Management Report
        </h1>

        <p>
            Green Dashboard
        </p>

    </div>


    {{-- Filters --}}
    <div class="section-title">
        Report Filters
    </div>

    <table class="filters">

        <tr>
            <td>Year</td>
            <td>{{ request('year') ?: 'All Years' }}</td>
        </tr>

        <tr>
            <td>Waste Stream</td>
            <td>{{ $streamLabel }}</td>
        </tr>

        <tr>
            <td>Location / Program</td>

            <td>
                {{ $selectedStream === 'landfill'
                    ? 'Not Applicable'
                    : $locationName }}
            </td>
        </tr>

        <tr>
            <td>Waste Type</td>

            <td>
                {{ $selectedStream === 'landfill'
                    ? 'Not Applicable'
                    : $wasteTypeName }}
            </td>
        </tr>

        <tr>
            <td>From Date</td>

            <td>
                @if(request('date_from'))

                    {{ \Carbon\Carbon::parse(
                        request('date_from')
                    )->format('d M Y') }}

                @else

                    -

                @endif
            </td>
        </tr>

        <tr>
            <td>To Date</td>

            <td>
                @if(request('date_to'))

                    {{ \Carbon\Carbon::parse(
                        request('date_to')
                    )->format('d M Y') }}

                @else

                    -

                @endif
            </td>
        </tr>

    </table>


    {{-- Summary --}}
    <div class="section-title">
        Report Summary
    </div>

    <div class="summary-grid">

        <div class="summary-card">

            <div class="summary-label">
                Total Waste
            </div>

            <div class="summary-value">
                {{ number_format($totalWaste, 2) }} kg
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-label">
                Total Records
            </div>

            <div class="summary-value">
                {{ number_format($totalRecords) }}
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-label">
                Average Weight
            </div>

            <div class="summary-value">
                {{ number_format($averageWeight ?? 0, 2) }} kg
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-label">
                Highest Waste Type
            </div>

            <div class="summary-value">

                @if($selectedStream === 'landfill')

                    Not Applicable

                @else

                    {{ $highestWasteType->type_name ?? 'No Data' }}

                @endif

            </div>

        </div>


        <div class="summary-card">

            <div class="summary-label">
                Highest Location
            </div>

            <div class="summary-value">

                @if($selectedStream === 'landfill')

                    Not Applicable

                @else

                    {{ $highestLocation->location_name ?? 'No Data' }}

                @endif

            </div>

        </div>

    </div>


    {{-- Recycling Analytics --}}
    @if($selectedStream !== 'landfill')

        <div class="analytics-section">

            <div class="section-title">
                Recycling Analytics
            </div>

            <div class="analytics-row">

                <div class="chart-card">

                    <div class="chart-title">
                        Waste by Type
                    </div>

                    <div class="chart-wrapper">
                        <canvas id="wasteTypeChart"></canvas>
                    </div>

                </div>


                <div class="chart-card">

                    <div class="chart-title">
                        Waste by Location
                    </div>

                    <div class="chart-wrapper">
                        <canvas id="locationChart"></canvas>
                    </div>

                </div>

            </div>


            <div class="chart-card chart-card-full">

                <div class="chart-title">
                    Monthly Recycling Waste Trend
                </div>

                <div class="chart-wrapper">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>

            </div>

        </div>

    @endif


    {{-- Landfill Analytics --}}
    @if($selectedStream !== 'recycling')

        <div class="analytics-section">

            <div class="section-title">
                Waste to Landfill Analytics
            </div>

            <div class="chart-card chart-card-full">

                <div class="chart-title">
                    Monthly Waste to Landfill Trend
                </div>

                <div class="chart-wrapper">
                    <canvas id="landfillTrendChart"></canvas>
                </div>

            </div>

        </div>

    @endif


    {{-- Recycling Records --}}
    @if($selectedStream !== 'landfill')

        <div class="section-title">
            Recycling Waste Records
        </div>

        <table class="report-table">

            <thead>

                <tr>

                    <th>Date</th>

                    <th>Location / Program</th>

                    <th>Waste Type</th>

                    <th>Weight (kg)</th>

                    <th>Collection Method</th>

                    <th>Recorded By</th>

                    <th>Remarks</th>

                </tr>

            </thead>


            <tbody>

                @forelse($records as $record)

                    <tr>

                        <td>
                            {{ \Carbon\Carbon::parse(
                                $record->collection_date
                            )->format('d M Y') }}
                        </td>

                        <td>
                            {{ $record->location->location_name ?? '-' }}
                        </td>

                        <td>
                            {{ $record->wasteType->type_name ?? '-' }}
                        </td>

                        <td>
                            {{ number_format(
                                $record->actual_weight,
                                2
                            ) }}
                        </td>

                        <td>
                            {{ $record->collection_method ?? '-' }}
                        </td>

                        <td>
                            {{ $record->recordedBy->name ?? '-' }}
                        </td>

                        <td>
                            {{ $record->remarks ?? '-' }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            style="text-align: center;"
                        >
                            No recycling records found.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    @endif


    {{-- Landfill Records --}}
@if($selectedStream !== 'recycling')

    <div class="section-title">
        Waste to Landfill Records
    </div>

    <table class="report-table">

        <thead>

            <tr>
                <th>Date</th>
                <th>Vehicle Number</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Weight (kg)</th>
            </tr>

        </thead>

        <tbody>

            @forelse($landfillRecords as $landfillRecord)

                <tr>

                    <td>
                        {{ $landfillRecord->collection_date
                            ? \Carbon\Carbon::parse(
                                $landfillRecord->collection_date
                            )->format('d M Y')
                            : '-' }}
                    </td>

                    <td>
                        {{ $landfillRecord->vehicle_no ?? '-' }}
                    </td>

                    <td>
                        {{ $landfillRecord->time_in
                            ? substr((string) $landfillRecord->time_in, 0, 5)
                            : '-' }}
                    </td>

                    <td>
                        {{ $landfillRecord->time_out
                            ? substr((string) $landfillRecord->time_out, 0, 5)
                            : '-' }}
                    </td>

                    <td>
                        {{ number_format(
                            $landfillRecord->actual_weight,
                            2
                        ) }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="5"
                        style="text-align: center;"
                    >
                        No landfill records found.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

@endif

    {{-- Footer --}}
    <div class="report-footer">

        Generated on
        {{ now()->format('d M Y, h:i A') }}

    </div>

</div>


<script>

    /*
    |--------------------------------------------------------------------------
    | Recycling Chart Data
    |--------------------------------------------------------------------------
    */

    const wasteTypeLabels = @json(
        $wasteByType->pluck('type_name')
    );

    const wasteTypeData = @json(
        $wasteByType->pluck('total_weight')->map(
            fn($value) => (float) $value
        )
    );

    const locationLabels = @json(
        $wasteByLocation->pluck('location_name')
    );

    const locationData = @json(
        $wasteByLocation->pluck('total_weight')->map(
            fn($value) => (float) $value
        )
    );

    const monthlyLabels = @json(
        $monthlyWaste->pluck('month')
    );

    const monthlyData = @json(
        $monthlyWaste->pluck('total_weight')->map(
            fn($value) => (float) $value
        )
    );


    /*
    |--------------------------------------------------------------------------
    | Landfill Chart Data
    |--------------------------------------------------------------------------
    */

    const landfillMonthlyLabels = @json(
        $monthlyLandfill->pluck('month')
    );

    const landfillMonthlyData = @json(
        $monthlyLandfill->pluck('total_weight')->map(
            fn($value) => (float) $value
        )
    );

</script>


@if($selectedStream !== 'landfill')

<script>

    new Chart(
        document.getElementById('wasteTypeChart'),
        {
            type: 'pie',

            data: {

                labels: wasteTypeLabels,

                datasets: [{

                    data: wasteTypeData,

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

            plugins: [ChartDataLabels],

            options: {

                responsive: true,

                maintainAspectRatio: false,

                animation: false,

                plugins: {

                    legend: {
                        position: 'bottom'
                    },

                    datalabels: {

                        color: '#ffffff',

                        font: {
                            weight: 'bold',
                            size: 12
                        },

                        formatter: function(value, context) {

                            const data =
                                context.chart.data.datasets[0].data;

                            const total = data.reduce(
                                (sum, currentValue) =>
                                    sum + Number(currentValue),
                                0
                            );

                            if (total === 0) {
                                return '';
                            }

                            const percentage =
                                Number(value) / total * 100;

                            if (percentage < 8) {
                                return '';
                            }

                            return percentage.toFixed(1) + '%';
                        }
                    }
                }
            }
        }
    );


    new Chart(
        document.getElementById('locationChart'),
        {
            type: 'bar',

            data: {

                labels: locationLabels,

                datasets: [{

                    label: 'Waste Collected (kg)',

                    data: locationData,

                    backgroundColor:
                        'rgba(46, 184, 92, 0.75)',

                    borderColor:
                        'rgb(46, 184, 92)',

                    borderWidth: 1,

                    borderRadius: 4
                }]
            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                animation: false,

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


    new Chart(
        document.getElementById('monthlyTrendChart'),
        {
            type: 'line',

            data: {

                labels: monthlyLabels,

                datasets: [{

                    label: 'Recycling Waste (kg)',

                    data: monthlyData,

                    fill: false,

                    tension: 0.3,

                    pointRadius: 3
                }]
            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                animation: false,

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
                        display: true
                    }
                }
            }
        }
    );

</script>

@endif


@if($selectedStream !== 'recycling')

<script>

    new Chart(
        document.getElementById('landfillTrendChart'),
        {
            type: 'line',

            data: {

                labels: landfillMonthlyLabels,

                datasets: [{

                    label: 'Waste to Landfill (kg)',

                    data: landfillMonthlyData,

                    fill: false,

                    tension: 0.3,

                    pointRadius: 3,

                    borderColor: 'rgb(255, 99, 132)',

                    backgroundColor: 'rgba(255, 99, 132, 0.15)'
                }]
            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                animation: false,

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
                        display: true
                    }
                }
            }
        }
    );

</script>

@endif

</body>

</html>