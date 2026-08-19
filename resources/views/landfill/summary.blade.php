@extends('layouts.app')

@section('title', 'Landfill Import Summary')

@section('content')

<div class="container-fluid px-4 py-4">

    <div class="card">

        <div class="card-header">
            <strong>Landfill Import Summary</strong>
        </div>

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md">
                    <div class="card">
                        <div class="card-body">

                            <div class="text-body-secondary">
                                Total Records
                            </div>

                            <div class="fs-4 fw-semibold">
                                {{ $total }}
                            </div>

                        </div>
                    </div>
                </div>


                <div class="col-md">
                    <div class="card">
                        <div class="card-body">

                            <div class="text-body-secondary">
                                Imported
                            </div>

                            <div class="fs-4 fw-semibold text-success">
                                {{ $imported }}
                            </div>

                        </div>
                    </div>
                </div>


                <div class="col-md">
                    <div class="card">
                        <div class="card-body">

                            <div class="text-body-secondary">
                                Duplicates
                            </div>

                            <div class="fs-4 fw-semibold text-warning">
                                {{ $duplicates }}
                            </div>

                        </div>
                    </div>
                </div>


                <div class="col-md">
                    <div class="card">
                        <div class="card-body">

                            <div class="text-body-secondary">
                                Skipped
                            </div>

                            <div class="fs-4 fw-semibold text-warning">
                                {{ $skipped }}
                            </div>

                        </div>
                    </div>
                </div>


                <div class="col-md">
                    <div class="card">
                        <div class="card-body">

                            <div class="text-body-secondary">
                                Failed
                            </div>

                            <div class="fs-4 fw-semibold text-danger">
                                {{ $failed }}
                            </div>

                        </div>
                    </div>
                </div>

            </div>


            @if(!empty($skipDetails))

                <div class="card mt-4">

                    <div class="card-header">
                        <strong>Skipped Record Details</strong>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped align-middle">

                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Reason</th>
                                        <th>Date</th>
                                        <th>Vehicle</th>
                                        <th>Time In</th>
                                        <th>Time Out</th>
                                        <th>Weight (kg)</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach($skipDetails as $index => $detail)

                                        <tr>

                                            <td>
                                                {{ $index + 1 }}
                                            </td>

                                            <td>
                                                {{ $detail['reason'] ?? 'Skipped' }}
                                            </td>

                                            <td>
                                                {{ $detail['collection_date'] ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $detail['vehicle_no'] ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $detail['time_in'] ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $detail['time_out'] ?? '-' }}
                                            </td>

                                            <td>
                                                @if(isset($detail['weight_kg']))
                                                    {{ number_format((float) $detail['weight_kg'], 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            @endif


            <div class="mt-4 d-flex gap-2">

                <a
                    href="{{ route('landfill.index') }}"
                    class="btn btn-primary">

                    Import Another PDF

                </a>

                <a
                    href="{{ route('waste-records.index') }}"
                    class="btn btn-secondary">

                    View Waste Records

                </a>

            </div>

        </div>

    </div>

</div>

@endsection