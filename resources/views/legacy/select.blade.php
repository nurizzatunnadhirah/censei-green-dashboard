@extends('layouts.app')

@section('title', 'Select Legacy Data')

@section('content')

<div class="container-fluid px-4 py-4">

    <div class="card">

        <div class="card-header">
            <strong>Select Data to Import</strong>
        </div>

        <div class="card-body">

            <div class="alert alert-info">
                Select the worksheets you want to process.
                You may also limit the import to a specific month range.
            </div>

            <form
                action="{{ route('legacy.preview') }}"
                method="POST">

                @csrf

                <input
                    type="hidden"
                    name="temp_file"
                    value="{{ $tempFile }}">

                {{-- Worksheet Selection --}}
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Worksheets
                    </label>

                    <div class="card">

                        <div class="card-body">

                            @foreach($sheetNames as $sheet)

                                <div class="form-check mb-2">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="sheets[]"
                                        value="{{ $sheet }}"
                                        id="sheet_{{ $loop->index }}">

                                    <label
                                        class="form-check-label"
                                        for="sheet_{{ $loop->index }}">

                                        {{ $sheet }}

                                    </label>

                                </div>

                            @endforeach

                        </div>

                    </div>

                </div>


                {{-- Optional Month Range --}}
                <div class="row g-3 mb-4">

                    <div class="col-md-6">

                        <label class="form-label">
                            From Month
                        </label>

                        <input
                            type="month"
                            name="from_month"
                            class="form-control">

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            To Month
                        </label>

                        <input
                            type="month"
                            name="to_month"
                            class="form-control">

                    </div>

                </div>


                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-success">

                        Preview Selected Data

                    </button>

                    <a
                        href="{{ route('legacy.index') }}"
                        class="btn btn-secondary">

                        Cancel

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection