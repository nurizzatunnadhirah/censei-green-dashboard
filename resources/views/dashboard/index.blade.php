@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="container-lg">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold">Green Dashboard</h2>
            <p class="text-body-secondary mb-0">
                Welcome back, Admin.
            </p>
        </div>

        <div class="text-end">
            <small class="text-body-secondary">Today</small>
            <h5>{{ date('d M Y') }}</h5>
        </div>

    </div>


    <div class="row g-4 mb-4">

        <div class="col-md-6 col-xl-3">

            <div class="card kpi-organic">

                <div class="card-body">

                    <h6>Organic Waste</h6>

                    <h2 class="fw-bold">
                        125 kg
                    </h2>

                    <small>
                        ↑ 8% from last month
                    </small>

                </div>

            </div>

        </div>


        <div class="col-md-6 col-xl-3">

            <div class="card kpi-inorganic">

                <div class="card-body">

                    <h6>Inorganic Waste</h6>

                    <h2 class="fw-bold">
                        98 kg
                    </h2>

                    <small>
                        ↓ 3% from last month
                    </small>

                </div>

            </div>

        </div>


        <div class="col-md-6 col-xl-3">

            <div class="card kpi-toxic">

                <div class="card-body">

                    <h6>Toxic Waste</h6>

                    <h2 class="fw-bold">
                        12 kg
                    </h2>

                    <small>
                        No change
                    </small>

                </div>

            </div>

        </div>


        <div class="col-md-6 col-xl-3">

            <div class="card kpi-carbon">

                <div class="card-body">

                    <h6>Carbon Reduction</h6>

                    <h2 class="fw-bold">
                        3.2 t
                    </h2>

                    <small>
                        Target 65%
                    </small>

                </div>

            </div>

        </div>

    </div>



    <div class="row g-4">

        <div class="col-lg-6">

            <div class="card">

                <div class="card-header">

                    Waste Distribution

                </div>

                <div class="card-body">

                    <div style="height:350px">

                        <canvas id="wasteChart"></canvas>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-lg-6">

            <div class="card">

                <div class="card-header">

                    Carbon Emission Trend

                </div>

                <div class="card-body">

                    <div style="height:350px">

                        <canvas id="carbonChart"></canvas>

                    </div>

                </div>

            </div>

        </div>

    </div>
<!-- Green Performance & Recent Activity -->
<div class="row g-4 mt-1">

    <!-- Green Performance -->
    <div class="col-lg-8">

        <div class="card">

            <div class="card-header">
                <strong>Green Performance</strong>
            </div>

            <div class="card-body">

                <div class="mb-4">
                    <div class="d-flex justify-content-between">
                        <span>Recycling Rate</span>
                        <strong>82%</strong>
                    </div>

                    <div class="progress mt-2" style="height:8px;">
                        <div class="progress-bar bg-success"
                             style="width:82%">
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between">
                        <span>Carbon Reduction Target</span>
                        <strong>65%</strong>
                    </div>

                    <div class="progress mt-2" style="height:8px;">
                        <div class="progress-bar"
                             style="width:65%">
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between">
                        <span>Water Saving</span>
                        <strong>91%</strong>
                    </div>

                    <div class="progress mt-2" style="height:8px;">
                        <div class="progress-bar bg-info"
                             style="width:91%">
                        </div>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-between">
                        <span>Energy Efficiency</span>
                        <strong>73%</strong>
                    </div>

                    <div class="progress mt-2" style="height:8px;">
                        <div class="progress-bar bg-warning"
                             style="width:73%">
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>


    <!-- Recent Activity -->
    <div class="col-lg-4">

        <div class="card">

            <div class="card-header">
                <strong>Recent Activity</strong>
            </div>

            <div class="list-group list-group-flush">

                <div class="list-group-item">
                    ♻ Waste report uploaded
                    <br>
                    <small class="text-body-secondary">
                        10 minutes ago
                    </small>
                </div>

                <div class="list-group-item">
                    🌿 Carbon emission updated
                    <br>
                    <small class="text-body-secondary">
                        1 hour ago
                    </small>
                </div>

                <div class="list-group-item">
                    💧 Water usage imported
                    <br>
                    <small class="text-body-secondary">
                        Yesterday
                    </small>
                </div>

                <div class="list-group-item">
                    📄 Monthly report generated
                    <br>
                    <small class="text-body-secondary">
                        2 days ago
                    </small>
                </div>

            </div>

        </div>

    </div>

</div>
<!-- Environmental Summary & Quick Actions -->
<div class="row g-4 mt-1">

    <!-- Environmental Summary -->
    <div class="col-lg-8">

        <div class="card">

            <div class="card-header">
                <strong>Environmental Summary</strong>
            </div>

            <div class="card-body">

                <div class="row text-center">

                    <div class="col-md-3 mb-3">
                        <h3 class="fw-bold text-success">235 kg</h3>
                        <small class="text-body-secondary">Total Waste Collected</small>
                    </div>

                    <div class="col-md-3 mb-3">
                        <h3 class="fw-bold text-primary">74%</h3>
                        <small class="text-body-secondary">Recycling Rate</small>
                    </div>

                    <div class="col-md-3 mb-3">
                        <h3 class="fw-bold text-success">3.2 t</h3>
                        <small class="text-body-secondary">CO₂ Reduction</small>
                    </div>

                    <div class="col-md-3 mb-3">
                        <h3 class="fw-bold text-info">820 L</h3>
                        <small class="text-body-secondary">Water Saved</small>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4">

        <div class="card">

            <div class="card-header">
                <strong>Quick Actions</strong>
            </div>

            <div class="card-body d-grid gap-2">

                <button class="btn btn-success">
                    Upload Data
                </button>

                <button class="btn btn-primary">
                    Generate Report
                </button>

                <button class="btn btn-outline-secondary">
                    View Analytics
                </button>

                <button class="btn btn-outline-success">
                    Export Dashboard
                </button>

            </div>

        </div>

    </div>

</div>
</div>


@endsection

@push('scripts')

<script>

new Chart(document.getElementById('wasteChart'), {
    type: 'pie',
    data: {
        labels: ['Organic','Inorganic','Toxic','Non-Recyclable'],
        datasets: [{
            data: [40,30,10,20]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

new Chart(document.getElementById('carbonChart'), {
    type: 'line',
    data: {
        labels:['Jan','Feb','Mar','Apr','May','Jun'],
        datasets:[{
            label:'Carbon Emission',
            data:[90,82,74,68,61,55],
            tension:.4
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false
    }
});

</script>

@endpush