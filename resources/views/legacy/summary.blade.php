@extends('layouts.app')

@section('title', 'Legacy Import Summary')

@section('content')

<div class="container-fluid px-4 py-4">

    <div class="card">

        <div class="card-header">
            <strong>Legacy Import Summary</strong>
        </div>

        <div class="card-body">

            {{-- Summary Cards --}}
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


            {{-- Skipped Record Details --}}
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
                                        <th>Location ID</th>
                                        <th>Waste Type ID</th>
                                        <th>Collection Date</th>
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
                                                @if(($detail['reason'] ?? '') === 'Duplicate record')

                                                    <span class="badge bg-warning text-dark">
                                                        Duplicate
                                                    </span>

                                                @else

                                                    <span class="badge bg-danger">
                                                        {{ $detail['reason'] ?? 'Skipped' }}
                                                    </span>

                                                @endif
                                            </td>

                                            <td>
                                                {{ $detail['location_id'] ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $detail['waste_type_id'] ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $detail['collection_date'] ?? '-' }}
                                            </td>

                                            <td>
                                                @if(isset($detail['weight']))
                                                    {{ number_format((float) $detail['weight'], 2) }}
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


            {{-- Actions --}}
            <div class="mt-4 d-flex gap-2">

                <a
                    href="{{ route('legacy.index') }}"
                    class="btn btn-primary">

                    Import Another File

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