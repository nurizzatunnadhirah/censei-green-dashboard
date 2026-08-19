@extends('layouts.app')

@section('title', 'Waste Types')

@section('content')

<div class="container-fluid px-4 py-4">

    {{-- Page Header --}}
    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            Waste Types
        </h2>

        <div class="text-body-secondary">
            Manage waste types and their categories.
        </div>

    </div>


    {{-- Success Message --}}
    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- Error Message --}}
    @if(session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

    @endif


    {{-- Validation Errors --}}
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

        {{-- Add Waste Type --}}
        <div class="col-lg-4">

            <div class="card">

                <div class="card-header">

                    <strong>
                        Add Waste Type
                    </strong>

                </div>


                <div class="card-body">

                    <form
                        method="POST"
                        action="{{ route('waste-types.store') }}"
                    >

                        @csrf


                        {{-- Waste Type Name --}}
                        <div class="mb-3">

                            <label
                                for="type_name"
                                class="form-label"
                            >
                                Waste Type Name
                            </label>

                            <input
                                type="text"
                                name="type_name"
                                id="type_name"
                                class="form-control"
                                value="{{ old('type_name') }}"
                                required
                            >

                        </div>


                        {{-- Category --}}
                        <div class="mb-3">

                            <label
                                for="category_id"
                                class="form-label"
                            >
                                Waste Category
                            </label>

                            <select
                                name="category_id"
                                id="category_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Select Category
                                </option>

                                @foreach($categories as $category)

                                    <option
                                        value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}
                                    >
                                        {{ $category->category_name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Description --}}
                        <div class="mb-3">

                            <label
                                for="description"
                                class="form-label"
                            >
                                Description
                            </label>

                            <textarea
                                name="description"
                                id="description"
                                class="form-control"
                                rows="3"
                            >{{ old('description') }}</textarea>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Add Waste Type
                        </button>

                    </form>

                </div>

            </div>

        </div>


        {{-- Waste Type List --}}
        <div class="col-lg-8">

            <div class="card mb-4">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <strong>
                        Waste Type List
                    </strong>

                    <span class="text-body-secondary">
                        {{ $wasteTypes->total() }} type(s)
                    </span>

                </div>


                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead>

                                <tr>

                                    <th style="width: 70px;">
                                        ID
                                    </th>

                                    <th>
                                        Waste Type
                                    </th>

                                    <th>
                                        Category
                                    </th>

                                    <th>
                                        Description
                                    </th>

                                    <th style="width: 180px;">
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($wasteTypes as $wasteType)

                                    <tr>

                                        <td>
                                            {{ $wasteType->id }}
                                        </td>

                                        <td>
                                            {{ $wasteType->type_name }}
                                        </td>

                                        <td>

                                            {{ $wasteType->category->category_name ?? '-' }}

                                        </td>

                                        <td>

                                            {{ $wasteType->description ?? '-' }}

                                        </td>

                                        <td>

                                            <div class="d-flex gap-2">

                                                {{-- Edit --}}
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-warning"
                                                    data-coreui-toggle="modal"
                                                    data-coreui-target="#editWasteType{{ $wasteType->id }}"
                                                >
                                                    Edit
                                                </button>


                                                {{-- Delete --}}
                                                <form
                                                    method="POST"
                                                    action="{{ route('waste-types.destroy', $wasteType) }}"
                                                    onsubmit="return confirm('Delete this waste type?')"
                                                >

                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-danger"
                                                    >
                                                        Delete
                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>


                                    {{-- Edit Modal --}}
                                    <div
                                        class="modal fade"
                                        id="editWasteType{{ $wasteType->id }}"
                                        tabindex="-1"
                                        aria-hidden="true"
                                    >

                                        <div class="modal-dialog">

                                            <div class="modal-content">

                                                <form
                                                    method="POST"
                                                    action="{{ route('waste-types.update', $wasteType) }}"
                                                >

                                                    @csrf
                                                    @method('PUT')


                                                    <div class="modal-header">

                                                        <h5 class="modal-title">
                                                            Edit Waste Type
                                                        </h5>

                                                        <button
                                                            type="button"
                                                            class="btn-close"
                                                            data-coreui-dismiss="modal"
                                                            aria-label="Close"
                                                        ></button>

                                                    </div>


                                                    <div class="modal-body">

                                                        {{-- Waste Type Name --}}
                                                        <div class="mb-3">

                                                            <label class="form-label">
                                                                Waste Type Name
                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="type_name"
                                                                class="form-control"
                                                                value="{{ $wasteType->type_name }}"
                                                                required
                                                            >

                                                        </div>


                                                        {{-- Category --}}
                                                        <div class="mb-3">

                                                            <label class="form-label">
                                                                Waste Category
                                                            </label>

                                                            <select
                                                                name="category_id"
                                                                class="form-select"
                                                                required
                                                            >

                                                                @foreach($categories as $category)

                                                                    <option
                                                                        value="{{ $category->id }}"
                                                                        {{ $wasteType->category_id == $category->id ? 'selected' : '' }}
                                                                    >
                                                                        {{ $category->category_name }}
                                                                    </option>

                                                                @endforeach

                                                            </select>

                                                        </div>


                                                        {{-- Description --}}
                                                        <div class="mb-3">

                                                            <label class="form-label">
                                                                Description
                                                            </label>

                                                            <textarea
                                                                name="description"
                                                                class="form-control"
                                                                rows="3"
                                                            >{{ $wasteType->description }}</textarea>

                                                        </div>

                                                    </div>


                                                    <div class="modal-footer">

                                                        <button
                                                            type="button"
                                                            class="btn btn-secondary"
                                                            data-coreui-dismiss="modal"
                                                        >
                                                            Cancel
                                                        </button>

                                                        <button
                                                            type="submit"
                                                            class="btn btn-primary"
                                                        >
                                                            Save Changes
                                                        </button>

                                                    </div>

                                                </form>

                                            </div>

                                        </div>

                                    </div>

                                @empty

                                    <tr>

                                        <td
                                            colspan="5"
                                            class="text-center py-4 text-body-secondary"
                                        >
                                            No waste types found.
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>


                @if($wasteTypes->hasPages())

                    <div class="card-footer">

                        {{ $wasteTypes->links() }}

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>

@endsection