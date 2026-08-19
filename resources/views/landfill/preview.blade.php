@extends('layouts.app')

@section('title', 'Landfill Import Preview')

@section('content')

<div class="container-fluid px-4 py-4">

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <strong>Waste to Landfill Import Preview</strong>

            <span class="badge bg-primary">
                {{ $totalRecords }} records detected
            </span>

        </div>

        <div class="card-body">

            <div class="alert alert-info">
                No landfill records have been saved to the database yet.
                Review the extracted data and monthly totals before importing.
            </div>


            {{-- Summary Cards --}}
            <div class="row g-3 mb-4">

                <div class="col-md-4">

                    <div class="card">

                        <div class="card-body">

                            <div class="text-body-secondary">
                                Total Records
                            </div>

                            <div class="fs-4 fw-semibold">
                                {{ $totalRecords }}
                            </div>

                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="card">

                        <div class="card-body">

                            <div class="text-body-secondary">
                                Tables Detected
                            </div>

                            <div class="fs-4 fw-semibold">
                                {{ count($pageSummaries) }}
                            </div>

                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="card">

                        <div class="card-body">

                            <div class="text-body-secondary">
                                Valid Table Totals
                            </div>

                            <div class="fs-4 fw-semibold text-success">

                                {{
                                    collect($pageSummaries)
                                        ->where('total_matches', true)
                                        ->count()
                                }}

                                /
                                {{ count($pageSummaries) }}

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Page / Table Validation --}}
            <div class="card mb-4">

                <div class="card-header">
                    <strong>Table Validation Summary</strong>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered align-middle">

                            <thead>

                                <tr>
                                    <th>Page</th>
                                    <th>Records</th>
                                    <th>Reported Total (MT)</th>
                                    <th>Calculated Total (MT)</th>
                                    <th>Status</th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach($pageSummaries as $summary)

                                    <tr>

                                        <td>
                                            {{ $summary['page_number'] }}
                                        </td>

                                        <td>
                                            {{ $summary['record_count'] }}
                                        </td>

                                        <td>
                                            {{ $summary['reported_total_mt'] !== null
                                                ? number_format($summary['reported_total_mt'], 2)
                                                : '-' }}
                                        </td>

                                        <td>
                                            {{ number_format($summary['calculated_total_mt'], 2) }}
                                        </td>

                                        <td>

                                            @if($summary['total_matches'] === true)

                                                <span class="badge bg-success">
                                                    Match
                                                </span>

                                            @elseif($summary['total_matches'] === false)

                                                <span class="badge bg-danger">
                                                    Mismatch
                                                </span>

                                            @else

                                                <span class="badge bg-warning text-dark">
                                                    No Total Found
                                                </span>

                                            @endif

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            {{-- Extracted Landfill Records --}}
            <div class="card">

                <div class="card-header">
                    <strong>Extracted Landfill Records</strong>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-striped table-bordered align-middle">

                            <thead>

                                <tr>
                                    <th>#</th>
                                    <th>Page</th>
                                    <th>Collection Date</th>
                                    <th>Vehicle No.</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                    <th>Weight (MT)</th>
                                    <th>Weight (kg)</th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach($records as $index => $record)

                                    <tr>

                                        <td>
                                            {{ $index + 1 }}
                                        </td>

                                        <td>
                                            {{ $record['page_number'] }}
                                        </td>

                                        <td>
                                            {{ $record['collection_date'] }}
                                        </td>

                                        <td>
                                            {{ $record['vehicle_no'] }}
                                        </td>

                                        <td>
                                            {{ $record['time_in'] }}
                                        </td>

                                        <td>
                                            {{ $record['time_out'] }}
                                        </td>

                                        <td>
                                            {{ number_format($record['weight_mt'], 2) }}
                                        </td>

                                        <td>
                                            {{ number_format($record['weight_kg'], 2) }}
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            <form
    action="{{ route('landfill.import') }}"
    method="POST"
    class="mt-4">

    @csrf

    @foreach($records as $index => $record)

        <input
            type="hidden"
            name="records[{{ $index }}][collection_date]"
            value="{{ $record['collection_date'] }}">

        <input
            type="hidden"
            name="records[{{ $index }}][vehicle_no]"
            value="{{ $record['vehicle_no'] }}">

        <input
            type="hidden"
            name="records[{{ $index }}][time_in]"
            value="{{ $record['time_in'] }}">

        <input
            type="hidden"
            name="records[{{ $index }}][time_out]"
            value="{{ $record['time_out'] }}">

        <input
            type="hidden"
            name="records[{{ $index }}][weight_kg]"
            value="{{ $record['weight_kg'] }}">

    @endforeach

    <button
        type="submit"
        class="btn btn-success">

        Import Landfill Records

    </button>

</form>

            <div class="mt-4">

                <a
                    href="{{ route('landfill.index') }}"
                    class="btn btn-secondary">

                    Back

                </a>

            </div>

        </div>

    </div>

</div>

@endsection