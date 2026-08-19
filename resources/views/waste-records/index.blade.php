@extends('layouts.app')

@section('title', 'Waste Records')

@section('content')

<div class="container-fluid px-4 py-4">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-coreui-dismiss="alert">
            </button>

        </div>
    @endif


    {{-- Search & Filters --}}
    <div class="card mb-4">

        <div class="card-header">
            <strong>Search & Filters</strong>
        </div>

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('waste-records.index') }}"
                class="row g-3">

                {{-- Search --}}
                <div class="col-md-3">

                    <label class="form-label">
                        Search
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Waste type or remarks"
                        value="{{ request('search') }}">

                </div>


                {{-- Location --}}
                <div class="col-md-3">

                    <label class="form-label">
                        Location / Program
                    </label>

                    <select
                        name="location"
                        class="form-select">

                        <option value="">
                            All Locations
                        </option>

                        @foreach($locations as $location)

                            <option
                                value="{{ $location->id }}"
                                {{ request('location') == $location->id ? 'selected' : '' }}>

                                {{ $location->location_name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Waste Type --}}
                <div class="col-md-3">

                    <label class="form-label">
                        Waste Type
                    </label>

                    <select
                        name="waste_type"
                        class="form-select">

                        <option value="">
                            All Waste Types
                        </option>

                        @foreach($wasteTypes as $type)

                            <option
                                value="{{ $type->id }}"
                                {{ request('waste_type') == $type->id ? 'selected' : '' }}>

                                {{ $type->type_name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-3">

    <label class="form-label">
        Waste Stream
    </label>

    <select
        name="waste_stream"
        class="form-select">

        <option value="">
            All Waste Streams
        </option>

        <option
            value="Recycling"
            {{ request('waste_stream') === 'Recycling' ? 'selected' : '' }}>
            Recycling
        </option>

        <option
            value="Landfill"
            {{ request('waste_stream') === 'Landfill' ? 'selected' : '' }}>
            Landfill
        </option>

    </select>

</div>


                {{-- Collection Date --}}
                <div class="col-md-3">

                    <label class="form-label">
                        Collection Date
                    </label>

                    <input
                        type="date"
                        name="collection_date"
                        class="form-control"
                        value="{{ request('collection_date') }}">

                </div>


                {{-- Buttons --}}
                <div class="col-12">

                    <button
                        type="submit"
                        class="btn btn-success">

                        Search

                    </button>

                    <a
                        href="{{ route('waste-records.index') }}"
                        class="btn btn-secondary">

                        Reset

                    </a>

                </div>

            </form>

        </div>

    </div>


    {{-- Waste Records Table --}}
<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <div>
            <h4 class="mb-0">Waste Records</h4>

            @if(request('waste_stream') === 'Landfill')
                <small class="text-body-secondary">
                    Showing landfill waste records
                </small>
            @elseif(request('waste_stream') === 'Recycling')
                <small class="text-body-secondary">
                    Showing recycling waste records
                </small>
            @endif
        </div>

        <a
            href="{{ route('waste-records.create') }}"
            class="btn btn-success">

            <i class="cil-plus me-1"></i>
            Add Waste Record

        </a>

    </div>


    <div class="card-body">

        <div class="table-responsive">

            {{-- ========================================== --}}
            {{-- LANDFILL TABLE --}}
            {{-- ========================================== --}}

            @if(request('waste_stream') === 'Landfill')

                <table class="table table-striped table-hover align-middle">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Collection Date</th>
                            <th>Vehicle No.</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Weight (kg)</th>
                            <th>Method</th>
                            <th>Recorded By</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($records as $record)

                            <tr>

                                <td>
                                    {{ $record->id }}
                                </td>

                                <td>
                                    {{ $record->collection_date }}
                                </td>

                                <td>
                                    {{ $record->vehicle_no ?? '-' }}
                                </td>

                                <td>
                                    {{ $record->time_in
                                        ? \Carbon\Carbon::parse($record->time_in)->format('H:i')
                                        : '-' }}
                                </td>

                                <td>
                                    {{ $record->time_out
                                        ? \Carbon\Carbon::parse($record->time_out)->format('H:i')
                                        : '-' }}
                                </td>

                                <td>
                                    {{ number_format($record->actual_weight, 2) }}
                                </td>

                                <td>
                                    {{ $record->collection_method ?? '-' }}
                                </td>

                                <td>
                                    {{ $record->recordedBy->name ?? '-' }}
                                </td>

                                <td class="text-center">

                                    <a
                                        href="{{ route('waste-records.edit', $record) }}"
                                        class="btn btn-sm btn-primary"
                                        title="Edit">

                                        <i class="cil-pencil"></i>

                                    </a>

                                    <form
                                        action="{{ route('waste-records.destroy', $record) }}"
                                        method="POST"
                                        class="d-inline">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                            title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this waste record?')">

                                            <i class="cil-trash"></i>

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="9"
                                    class="text-center py-4">

                                    No landfill waste records found.

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>


            {{-- ========================================== --}}
{{-- RECYCLING TABLE --}}
{{-- ========================================== --}}

@elseif(request('waste_stream') === 'Recycling')

    <table class="table table-striped table-hover align-middle">

        <thead>
            <tr>
                <th>ID</th>
                <th>Location / Program</th>
                <th>Waste Type</th>
                <th>Weight (kg)</th>
                <th>Collection Date</th>
                <th>Method</th>
                <th>Recorded By</th>
                <th class="text-center">Files</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>

        <tbody>

            @forelse($records as $record)

                <tr>

                    <td>
                        {{ $record->id }}
                    </td>

                    <td>
                        {{ $record->location->location_name ?? '-' }}
                    </td>

                    <td>
                        {{ $record->wasteType->type_name ?? '-' }}
                    </td>

                    <td>
                        {{ number_format($record->actual_weight, 2) }}
                    </td>

                    <td>
                        {{ $record->collection_date }}
                    </td>

                    <td>
                        {{ $record->collection_method ?? '-' }}
                    </td>

                    <td>
                        {{ $record->recordedBy->name ?? '-' }}
                    </td>

                    <td class="text-center">

                        @if($record->files->count())

                            @php
                                $file = $record->files->first();
                            @endphp

                            <a
                                href="{{ asset('storage/' . $file->file_path) }}"
                                target="_blank"
                                class="btn btn-sm btn-info">

                                <i class="fas fa-file me-1"></i>
                                Open

                            </a>

                        @else

                            <span class="text-muted">
                                No file
                            </span>

                        @endif

                    </td>

                    <td class="text-center">

                        <a
                            href="{{ route('waste-records.edit', $record) }}"
                            class="btn btn-sm btn-primary"
                            title="Edit">

                            <i class="cil-pencil"></i>

                        </a>

                        <form
                            action="{{ route('waste-records.destroy', $record) }}"
                            method="POST"
                            class="d-inline">

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn btn-sm btn-danger"
                                title="Delete"
                                onclick="return confirm('Are you sure you want to delete this waste record?')">

                                <i class="cil-trash"></i>

                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>
                    <td
                        colspan="9"
                        class="text-center py-4">

                        No recycling waste records found.

                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>


{{-- ========================================== --}}
{{-- ALL WASTE STREAMS TABLE --}}
{{-- ========================================== --}}

@else

    <table class="table table-striped table-hover align-middle">

        <thead>
            <tr>
                <th>ID</th>
                <th>Waste Stream</th>
                <th>Weight (kg)</th>
                <th>Collection Date</th>
                <th>Location / Vehicle</th>
                <th>Waste Type / Method</th>
                <th>Recorded By</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>

        <tbody>

            @forelse($records as $record)

                <tr>

                    <td>
                        {{ $record->id }}
                    </td>

                    <td>

                        @if($record->waste_stream === 'Landfill')

                            <span class="badge bg-warning text-dark">
                                Landfill
                            </span>

                        @elseif($record->waste_stream === 'Recycling')

                            <span class="badge bg-success">
                                Recycling
                            </span>

                        @else

                            <span class="badge bg-secondary">
                                -
                            </span>

                        @endif

                    </td>

                    <td>
                        {{ number_format($record->actual_weight, 2) }}
                    </td>

                    <td>
                        {{ $record->collection_date }}
                    </td>

                    <td>

                        @if($record->waste_stream === 'Landfill')

                            {{ $record->vehicle_no ?? '-' }}

                        @else

                            {{ $record->location->location_name ?? '-' }}

                        @endif

                    </td>

                    <td>

                        @if($record->waste_stream === 'Landfill')

                            {{ $record->collection_method ?? 'Landfill PDF Import' }}

                        @else

                            {{ $record->wasteType->type_name ?? '-' }}

                        @endif

                    </td>

                    <td>
                        {{ $record->recordedBy->name ?? '-' }}
                    </td>

                    <td class="text-center">

                        <a
                            href="{{ route('waste-records.edit', $record) }}"
                            class="btn btn-sm btn-primary"
                            title="Edit">

                            <i class="cil-pencil"></i>

                        </a>

                        <form
                            action="{{ route('waste-records.destroy', $record) }}"
                            method="POST"
                            class="d-inline">

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn btn-sm btn-danger"
                                title="Delete"
                                onclick="return confirm('Are you sure you want to delete this waste record?')">

                                <i class="cil-trash"></i>

                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>
                    <td
                        colspan="8"
                        class="text-center py-4">

                        No waste records found.

                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

@endif

        </div>

            {{-- Pagination --}}
            <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-3">

    <div class="text-body-secondary">
        Showing
        <strong>{{ $records->firstItem() ?? 0 }}</strong>
        to
        <strong>{{ $records->lastItem() ?? 0 }}</strong>
        of
        <strong>{{ $records->total() }}</strong>
        results
    </div>

    <nav>
        <ul class="pagination mb-0">

            {{-- Previous --}}
            <li class="page-item {{ $records->onFirstPage() ? 'disabled' : '' }}">
                <a
                    class="page-link"
                    href="{{ $records->previousPageUrl() ?? '#' }}"
                    aria-label="Previous">
                    ‹
                </a>
            </li>

            {{-- Page Numbers --}}
            @foreach($records->getUrlRange(
                max(1, $records->currentPage() - 2),
                min($records->lastPage(), $records->currentPage() + 2)
            ) as $page => $url)

                <li class="page-item {{ $page == $records->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">
                        {{ $page }}
                    </a>
                </li>

            @endforeach

            {{-- Next --}}
            <li class="page-item {{ !$records->hasMorePages() ? 'disabled' : '' }}">
                <a
                    class="page-link"
                    href="{{ $records->nextPageUrl() ?? '#' }}"
                    aria-label="Next">
                    ›
                </a>
            </li>

        </ul>
    </nav>

</div>

        </div>

    </div>

</div>

@endsection