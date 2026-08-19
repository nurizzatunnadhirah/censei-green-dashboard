@extends('layouts.app')

@section('title', 'Waste Categories')

@section('content')

<div class="container-fluid px-4 py-4">

    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            Waste Categories
        </h2>

        <div class="text-body-secondary">
            Manage waste categories used by waste types.
        </div>

    </div>


    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

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

        {{-- Add Category --}}
        <div class="col-lg-4">

            <div class="card">

                <div class="card-header">
                    <strong>Add Waste Category</strong>
                </div>

                <div class="card-body">

                    <form
                        method="POST"
                        action="{{ route('waste-categories.store') }}"
                    >

                        @csrf


                        <div class="mb-3">

                            <label
                                for="category_name"
                                class="form-label"
                            >
                                Category Name
                            </label>

                            <input
                                type="text"
                                name="category_name"
                                id="category_name"
                                class="form-control"
                                value="{{ old('category_name') }}"
                                required
                            >

                        </div>


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
                            Add Category
                        </button>

                    </form>

                </div>

            </div>

        </div>


        {{-- Category List --}}
        <div class="col-lg-8">

            <div class="card mb-4">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <strong>
                        Waste Category List
                    </strong>

                    <span class="text-body-secondary">
                        {{ $categories->total() }} category(s)
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
                                        Category
                                    </th>

                                    <th>
                                        Description
                                    </th>

                                    <th style="width: 120px;">
                                        Waste Types
                                    </th>

                                    <th style="width: 180px;">
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($categories as $category)

                                    <tr>

                                        <td>
                                            {{ $category->id }}
                                        </td>

                                        <td>
                                            {{ $category->category_name }}
                                        </td>

                                        <td>
                                            {{ $category->description ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $category->waste_types_count }}
                                        </td>

                                        <td>

                                            <div class="d-flex gap-2">

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-warning"
                                                    data-coreui-toggle="modal"
                                                    data-coreui-target="#editCategory{{ $category->id }}"
                                                >
                                                    Edit
                                                </button>


                                                <form
                                                    method="POST"
                                                    action="{{ route('waste-categories.destroy', $category) }}"
                                                    onsubmit="return confirm('Delete this waste category?')"
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
                                        id="editCategory{{ $category->id }}"
                                        tabindex="-1"
                                        aria-hidden="true"
                                    >

                                        <div class="modal-dialog">

                                            <div class="modal-content">

                                                <form
                                                    method="POST"
                                                    action="{{ route('waste-categories.update', $category) }}"
                                                >

                                                    @csrf
                                                    @method('PUT')


                                                    <div class="modal-header">

                                                        <h5 class="modal-title">
                                                            Edit Waste Category
                                                        </h5>

                                                        <button
                                                            type="button"
                                                            class="btn-close"
                                                            data-coreui-dismiss="modal"
                                                            aria-label="Close"
                                                        ></button>

                                                    </div>


                                                    <div class="modal-body">

                                                        <div class="mb-3">

                                                            <label class="form-label">
                                                                Category Name
                                                            </label>

                                                            <input
                                                                type="text"
                                                                name="category_name"
                                                                class="form-control"
                                                                value="{{ $category->category_name }}"
                                                                required
                                                            >

                                                        </div>


                                                        <div class="mb-3">

                                                            <label class="form-label">
                                                                Description
                                                            </label>

                                                            <textarea
                                                                name="description"
                                                                class="form-control"
                                                                rows="3"
                                                            >{{ $category->description }}</textarea>

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
                                            No waste categories found.
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>


                @if($categories->hasPages())

                    <div class="card-footer">

                        {{ $categories->links() }}

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>

@endsection