@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@section('content')

<div class="container-fluid px-4 py-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">Dashboard</h2>

            <p class="text-body-secondary mb-0">
                Welcome back, Admin. Monitor CENSEi sustainability performance.
            </p>
        </div>

        <div class="text-end">
            <small class="text-body-secondary">Today</small>
            <h5 class="mb-0">{{ date('d M Y') }}</h5>
        </div>

    </div>

    <!-- KPI Cards -->
    <div class="row">

        <div class="col-md-6 col-xl-3 mb-4">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <h6 class="text-body-secondary">
                        Organic Waste
                    </h6>

                    <h2 class="fw-bold text-success">
                        125 kg
                    </h2>

                    <small class="text-success">
                        ↑ 8% from last month
                    </small>

                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-3 mb-4">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <h6 class="text-body-secondary">
                        Inorganic Waste
                    </h6>

                    <h2 class="fw-bold text-primary">
                        98 kg
                    </h2>

                    <small class="text-danger">
                        ↓ 3% from last month
                    </small>

                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-3 mb-4">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <h6 class="text-body-secondary">
                        Toxic Waste
                    </h6>

                    <h2 class="fw-bold text-warning">
                        12 kg
                    </h2>

                    <small class="text-body-secondary">
                        No change
                    </small>

                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-3 mb-4">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <h6 class="text-body-secondary">
                        Carbon Reduction
                    </h6>

                    <h2 class="fw-bold text-success">
                        3.2 t
                    </h2>

                    <small class="text-success">
                        Target 65%
                    </small>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- Analytics Charts -->
<div class="row">

    <!-- Waste Distribution -->
    <div class="col-lg-6 mb-4">

        <div class="card shadow-sm border-0 h-100">

            <div class="card-header bg-white">
                <h5 class="mb-0">
                    Waste Distribution
                </h5>
            </div>

            <div class="card-body" style="height:320px;">
    
            <canvas id="wasteChart"></canvas>
            
            </div>

        </div>

    </div>

    <!-- Carbon Trend -->
    <div class="col-lg-6 mb-4">

        <div class="card shadow-sm border-0 h-100">

            <div class="card-header bg-white">
                <h5 class="mb-0">
                    Carbon Emission Trend
                </h5>
            </div>

            <div class="card-body" style="height:320px;">
    
            <canvas id="carbonChart"></canvas>

            </div>
        
        </div>

    </div>

</div>

@endsection
@push('scripts')
<script>

// Waste Distribution Pie Chart
new Chart(document.getElementById('wasteChart'), {
    type: 'pie',
    data: {
        labels: [
            'Organic',
            'Inorganic',
            'Toxic',
            'Non-Recyclable'
        ],
        datasets: [{
            data: [40, 30, 10, 20]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Carbon Emission Trend
new Chart(document.getElementById('carbonChart'), {
    type: 'line',
    data: {
        labels: [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun'
        ],
        datasets: [{
            label: 'Carbon Emission',
            data: [90, 82, 74, 68, 61, 55],
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

</script>
@endpush

@endsection