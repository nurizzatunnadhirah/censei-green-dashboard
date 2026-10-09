@extends('layouts.app')
@section('title', 'Select Legacy Data')
@section('content')
<div class="container-fluid px-4 py-4">
    <div class="card">
        <div class="card-header"><strong>Select Data to Import</strong></div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            <div class="alert alert-info">Select worksheets, optionally choose a month range, and map missing locations and waste types before previewing. No data is saved until you import.</div>
            <form method="POST" action="{{ route('legacy.preview') }}">
                @csrf
                <input type="hidden" name="temp_file" value="{{ $tempFile }}">
                <div class="mb-4">
                    <label class="form-label fw-semibold">Worksheets</label>
                    <div class="card"><div class="card-body">
                        @foreach($sheetNames as $sheet)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="sheets[]" value="{{ $sheet }}" id="sheet_{{ $loop->index }}" {{ in_array($sheet, old('sheets', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="sheet_{{ $loop->index }}">{{ $sheet }}</label>
                            </div>
                        @endforeach
                    </div></div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6"><label class="form-label">From Month</label><input type="month" name="from_month" value="{{ old('from_month') }}" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">To Month</label><input type="month" name="to_month" value="{{ old('to_month') }}" class="form-control"></div>
                </div>
                <div class="card mb-4">
                    <div class="card-header"><strong>Default Location for Monthly Summary Records</strong></div>
                    <div class="card-body">
                        <p class="text-body-secondary small">Only applies to records without a location. Existing worksheet locations remain unchanged.</p>
                        <select name="default_location_id" class="form-select">
                            <option value="">Leave unassigned</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ (string)old('default_location_id') === (string)$location->id ? 'selected' : '' }}>{{ $location->location_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header"><strong>Waste Type Mapping</strong></div>
                    <div class="card-body">
                        <p class="text-body-secondary small">Choose a database waste type for Excel names that differ from your existing names. Leave blank to use the original name and let validation check it.</p>
                        @forelse($detectedTypes as $key => $excelType)
                            <div class="row g-2 align-items-center mb-3">
                                <label for="map_{{ $key }}" class="col-md-5 col-form-label">{{ $excelType }}</label>
                                <div class="col-md-7">
                                    <select name="type_map[{{ $key }}]" id="map_{{ $key }}" class="form-select">
                                        <option value="">Auto-match exact name / Leave unmapped</option>
                                        @foreach($wasteTypes as $type)
                                            <option value="{{ $type->id }}" {{ (string)old('type_map.'.$key) === (string)$type->id ? 'selected' : '' }}>{{ $type->type_name }} ({{ $type->category->category_name ?? 'Uncategorized' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-warning mb-0">No waste types were detected. The worksheet may need additional parser support.</div>
                        @endforelse
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Preview Selected Data</button>
                    <a href="{{ route('legacy.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
