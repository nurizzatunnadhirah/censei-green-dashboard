@extends('layouts.app')

@section('title', 'Locations / Programs')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Locations / Programs</h2>
        <div class="text-body-secondary">Manage locations and programs used by waste records and legacy imports.</div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><strong>Add Location / Program</strong></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('locations.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="location_name" class="form-label">Location / Program Name</label>
                            <input type="text" name="location_name" id="location_name"
                                   class="form-control @error('location_name') is-invalid @enderror"
                                   value="{{ old('location_name') }}" maxlength="255" required>
                            @error('location_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Add Location</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Location / Program List</strong>
                    <span class="text-body-secondary">{{ $locations->total() }} location(s)</span>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('locations.index') }}" class="row g-2 mb-3">
                        <div class="col-sm-9">
                            <input type="search" name="search" class="form-control" value="{{ $search }}"
                                   placeholder="Search locations or programs" aria-label="Search locations or programs">
                        </div>
                        <div class="col-sm-3 d-flex gap-2">
                            <button class="btn btn-primary" type="submit">Search</button>
                            @if($search !== '')
                                <a class="btn btn-secondary" href="{{ route('locations.index') }}">Clear</a>
                            @endif
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:70px">ID</th>
                                    <th>Location / Program</th>
                                    <th style="width:180px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($locations as $location)
                                    <tr>
                                        <td>{{ $location->id }}</td>
                                        <td>{{ $location->location_name }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-warning"
                                                        data-coreui-toggle="modal"
                                                        data-coreui-target="#editLocation{{ $location->id }}">Edit</button>
                                                <form method="POST" action="{{ route('locations.destroy', $location) }}"
                                                      onsubmit="return confirm('Delete this location / program?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="search" value="{{ $search }}">
                                                    <input type="hidden" name="page" value="{{ $locations->currentPage() }}">
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-4 text-body-secondary">No locations found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($locations->hasPages())
                    <div class="card-footer">{{ $locations->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Keep modal markup outside table elements to ensure valid HTML. --}}
@foreach($locations as $location)
    <div class="modal fade" id="editLocation{{ $location->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('locations.update', $location) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="search" value="{{ $search }}">
                    <input type="hidden" name="page" value="{{ $locations->currentPage() }}">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Location / Program</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label" for="editLocationName{{ $location->id }}">Location / Program Name</label>
                        <input type="text" class="form-control" name="location_name"
                               id="editLocationName{{ $location->id }}"
                               value="{{ $location->location_name }}" maxlength="255" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection
