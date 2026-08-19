@extends('layouts.app')

@section('title', 'Add Waste Record')

@section('content')

<div class="container-lg">

    <div class="card">

        <div class="card-header">
            <h4 class="mb-0">Add Waste Record</h4>
        </div>

        <div class="card-body">
            
        @if ($errors->any())

    <div class="alert alert-danger">

        <strong>Please correct the following errors:</strong>

        <ul class="mb-0 mt-2">

            @foreach ($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif

            <form action="{{ route('waste-records.store') }}"
            method="POST"
            enctype="multipart/form-data">

                @csrf

                <div class="mb-3">

                    <label class="form-label">
    Location / Program
</label>

<select name="location_id" class="form-select">

    <option value="">
        Select Location / Program
    </option>

    @foreach($locations as $location)

        <option value="{{ $location->id }}"
          {{ old('location_id') == $location->id ? 'selected' : '' }}>
            {{ $location->location_name }}
        </option>

    @endforeach

</select>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Waste Type
                    </label>

                    <select name="waste_type_id" class="form-select">

                        <option value="">
                            Select Waste Type
                        </option>

                        @foreach($wasteTypes as $type)

                            <option value="{{ $type->id }}"
                             {{ old('waste_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->type_name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Weight (kg)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        name="actual_weight"
                        class="form-control"
                        value="{{ old('actual_weight') }}">

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Collection Date
                    </label>

                    <input
                        type="date"
                        name="collection_date"
                        class="form-control"
                        value="{{ old('collection_date') }}">

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Collection Method
                    </label>

                    <input
                        type="text"
                        name="collection_method"
                        class="form-control"
                        value="{{ old('collection_method') }}">
                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Remarks
                    </label>

                    <textarea
                        name="remarks"
                        rows="4"
                        class="form-control">{{ old('remarks') }}</textarea>

                </div>

                <div class="mb-3">

    <label class="form-label">
        Supporting File
    </label>

    <input
        type="file"
        name="attachment"
        class="form-control"
        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">

    <small class="text-muted">
        Accepted formats: JPG, JPEG, PNG, PDF, DOC, DOCX, XLS, XLSX (Maximum 5 MB)
    </small>

    @error('attachment')
        <div class="text-danger mt-1">
            {{ $message }}
        </div>
    @enderror

</div>
                <button class="btn btn-success">
                    Save Record
                </button>

                <a href="{{ route('waste-records.index') }}"
                   class="btn btn-secondary">

                    Cancel

                </a>

            </form>

        </div>

    </div>

</div>

@endsection