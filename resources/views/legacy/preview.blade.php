@extends('layouts.app')

@section('title', 'Legacy Import Preview')

@section('content')

<div class="container-fluid px-4 py-4">

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>CENSEi Legacy Import Preview</strong>

            <span class="badge bg-primary">
                {{ count($records) }} records detected
            </span>
        </div>

        <div class="card-body">

            <div class="alert alert-info">
                <strong>Processed Worksheets:</strong>

<ul class="mb-0 mt-2">
    @foreach($processedSheets as $sheet)
        <li>{{ $sheet }}</li>
    @endforeach
</ul>

                <br>

                No records have been saved to the database yet.
            </div>

            <div class="row g-3 mb-4">

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-body-secondary">
                                Total Detected
                            </div>

                            <div class="fs-4 fw-semibold">
                                {{ count($records) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-body-secondary">
                                Valid Records
                            </div>

                            <div class="fs-4 fw-semibold text-success">
                                {{ $validCount }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-body-secondary">
                                Invalid Records
                            </div>

                            <div class="fs-4 fw-semibold text-danger">
                                {{ $invalidCount }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            @if(count($records) > 0)

                <div class="table-responsive">

                    <table class="table table-bordered table-striped align-middle">

                        <thead>
                            <tr>
                                <th style="width: 70px;">#</th>
                                <th>Source Sheet</th>
                                <th>Location / Program</th>
                                <th>Waste Type</th>
                                <th>Collection Date</th>
                                <th>Weight (kg)</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($records as $index => $record)

    <tr class="{{ $record['valid'] ? '' : 'table-danger' }}">

        {{-- Number --}}
        <td>
            {{ $index + 1 }}
        </td>

        {{-- Source Worksheet --}}
        <td>
            {{ $record['source_sheet'] ?? '-' }}
        </td>

        {{-- Location / Program --}}
        <td>
            {{ $record['location'] }}
        </td>

        {{-- Waste Type --}}
        <td>
            {{ $record['waste_type'] }}
        </td>

        {{-- Collection Date --}}
        <td>
            {{ $record['collection_date'] }}
        </td>

        {{-- Weight --}}
        <td>
            {{ number_format($record['weight'], 2) }}
        </td>

        {{-- Status --}}
        <td>
            @if($record['valid'])

                <span class="badge bg-success">
                    Valid
                </span>

            @else

                <span class="badge bg-danger">
                    Invalid
                </span>

                <div class="small text-danger mt-1">
                    {{ implode(', ', $record['errors']) }}
                </div>

            @endif
        </td>

    </tr>

@endforeach

                        </tbody>

                    </table>

                    <form action="{{ route('legacy.import') }}"
      method="POST"
      class="mt-4">

    @csrf

    <input
    type="hidden"
    name="temp_file"
    value="{{ $tempFile }}">

    @foreach($records as $index => $record)

        <input type="hidden"
               name="records[{{ $index }}][location_id]"
               value="{{ $record['location_id'] }}">

        <input type="hidden"
               name="records[{{ $index }}][waste_type_id]"
               value="{{ $record['waste_type_id'] }}">

        <input type="hidden"
               name="records[{{ $index }}][collection_date]"
               value="{{ $record['collection_date'] }}">

        <input type="hidden"
               name="records[{{ $index }}][weight]"
               value="{{ $record['weight'] }}">

        <input type="hidden"
               name="records[{{ $index }}][valid]"
               value="{{ $record['valid'] ? 1 : 0 }}">

    @endforeach

    <button type="submit"
            class="btn btn-success"
            {{ $validCount === 0 ? 'disabled' : '' }}>

        Import Valid Records

    </button>

</form>

                </div>

            @else

                <div class="alert alert-warning mb-0">
                    No importable waste records were detected in this worksheet.
                </div>

            @endif

        </div>

    </div>

</div>

@endsection