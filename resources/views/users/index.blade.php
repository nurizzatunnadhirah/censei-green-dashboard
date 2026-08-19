@extends('layouts.app')

@section('title', 'User Management')

@section('content')

<div class="container-fluid px-4 py-4">

    {{-- Page Header --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-1">
            User Management
        </h2>

        <div class="text-body-secondary">
            Manage system users, roles and account status.
        </div>
    </div>


    {{-- Success Message --}}
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

        {{-- ========================================================= --}}
        {{-- ADD USER --}}
        {{-- ========================================================= --}}

        <div class="col-lg-4">

            <div class="card">

                <div class="card-header">
                    <strong>Add User</strong>
                </div>

                <div class="card-body">

                    <form
                        method="POST"
                        action="{{ route('users.store') }}"
                    >

                        @csrf


                        {{-- Name --}}
                        <div class="mb-3">

                            <label
                                for="name"
                                class="form-label"
                            >
                                Name
                            </label>

                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control"
                                value="{{ old('name') }}"
                                required
                            >

                        </div>


                        {{-- Email --}}
                        <div class="mb-3">

                            <label
                                for="email"
                                class="form-label"
                            >
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                value="{{ old('email') }}"
                                required
                            >

                        </div>


                        {{-- Phone Number --}}
                        <div class="mb-3">

                            <label
                                for="phone_number"
                                class="form-label"
                            >
                                Phone Number
                            </label>

                            <input
                                type="text"
                                name="phone_number"
                                id="phone_number"
                                class="form-control"
                                value="{{ old('phone_number') }}"
                            >

                        </div>


                        {{-- Position --}}
                        <div class="mb-3">

                            <label
                                for="position"
                                class="form-label"
                            >
                                Position
                            </label>

                            <input
                                type="text"
                                name="position"
                                id="position"
                                class="form-control"
                                value="{{ old('position') }}"
                            >

                        </div>


                        <div class="row">

                            {{-- Role --}}
                            <div class="col-md-6">

                                <div class="mb-3">

                                    <label
                                        for="role"
                                        class="form-label"
                                    >
                                        Role
                                    </label>

                                    <select
                                        name="role"
                                        id="role"
                                        class="form-select"
                                        required
                                    >

                                        <option value="">
                                            Select Role
                                        </option>

                                        <option
                                            value="Admin"
                                            {{ old('role') === 'Admin' ? 'selected' : '' }}
                                        >
                                            Admin
                                        </option>

                                        <option
                                            value="Staff"
                                            {{ old('role') === 'Staff' ? 'selected' : '' }}
                                        >
                                            Staff
                                        </option>

                                    </select>

                                </div>

                            </div>


                            {{-- Status --}}
                            <div class="col-md-6">

                                <div class="mb-3">

                                    <label
                                        for="status"
                                        class="form-label"
                                    >
                                        Status
                                    </label>

                                    <select
                                        name="status"
                                        id="status"
                                        class="form-select"
                                        required
                                    >

                                        <option
                                            value="Active"
                                            {{ old('status', 'Active') === 'Active' ? 'selected' : '' }}
                                        >
                                            Active
                                        </option>

                                        <option
                                            value="Inactive"
                                            {{ old('status') === 'Inactive' ? 'selected' : '' }}
                                        >
                                            Inactive
                                        </option>

                                    </select>

                                </div>

                            </div>

                        </div>


                        {{-- Password --}}
                        <div class="mb-3">

                            <label
                                for="password"
                                class="form-label"
                            >
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control"
                                required
                            >

                            <div class="form-text">
                                Minimum 8 characters.
                            </div>

                        </div>


                        {{-- Password Confirmation --}}
                        <div class="mb-3">

                            <label
                                for="password_confirmation"
                                class="form-label"
                            >
                                Confirm Password
                            </label>

                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="form-control"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Add User
                        </button>

                    </form>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- USER LIST --}}
        {{-- ========================================================= --}}

        <div class="col-lg-8">

            <div class="card mb-4">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <strong>
                        User List
                    </strong>

                    <span class="text-body-secondary">
                        {{ $users->total() }} user(s)
                    </span>

                </div>


                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead>

                                <tr>

                                    <th>
                                        User
                                    </th>

                                    <th>
                                        Position
                                    </th>

                                    <th>
                                        Role
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th style="width: 100px;">
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($users as $user)

                                    <tr>

                                        {{-- User --}}
                                        <td>

                                            <div class="fw-semibold">
                                                {{ $user->name }}
                                            </div>

                                            <div class="small text-body-secondary">
                                                {{ $user->email }}
                                            </div>

                                            @if($user->phone_number)

                                                <div class="small text-body-secondary">
                                                    {{ $user->phone_number }}
                                                </div>

                                            @endif

                                        </td>


                                        {{-- Position --}}
                                        <td>

                                            {{ $user->position ?? '-' }}

                                        </td>


                                        {{-- Role --}}
                                        <td>

                                            @if($user->role === 'Admin')

                                                <span class="badge bg-primary">
                                                    Admin
                                                </span>

                                            @else

                                                <span class="badge bg-info">
                                                    Staff
                                                </span>

                                            @endif

                                        </td>


                                        {{-- Status --}}
                                        <td>

                                            @if($user->status === 'Active')

                                                <span class="badge bg-success">
                                                    Active
                                                </span>

                                            @else

                                                <span class="badge bg-secondary">
                                                    Inactive
                                                </span>

                                            @endif

                                        </td>


                                        {{-- Actions --}}
                                        <td>

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-warning"
                                                data-coreui-toggle="modal"
                                                data-coreui-target="#editUser{{ $user->id }}"
                                            >
                                                Edit
                                            </button>

                                        </td>

                                    </tr>


                                    {{-- ================================================= --}}
                                    {{-- EDIT USER MODAL --}}
                                    {{-- ================================================= --}}

                                    <div
                                        class="modal fade"
                                        id="editUser{{ $user->id }}"
                                        tabindex="-1"
                                        aria-hidden="true"
                                    >

                                        <div class="modal-dialog modal-lg">

                                            <div class="modal-content">

                                                <form
                                                    method="POST"
                                                    action="{{ route('users.update', $user) }}"
                                                >

                                                    @csrf
                                                    @method('PUT')


                                                    {{-- Modal Header --}}
                                                    <div class="modal-header">

                                                        <h5 class="modal-title">
                                                            Edit User
                                                        </h5>

                                                        <button
                                                            type="button"
                                                            class="btn-close"
                                                            data-coreui-dismiss="modal"
                                                            aria-label="Close"
                                                        ></button>

                                                    </div>


                                                    {{-- Modal Body --}}
                                                    <div class="modal-body">

                                                        <div class="row">


                                                            {{-- Name --}}
                                                            <div class="col-md-6">

                                                                <div class="mb-3">

                                                                    <label class="form-label">
                                                                        Name
                                                                    </label>

                                                                    <input
                                                                        type="text"
                                                                        name="name"
                                                                        class="form-control"
                                                                        value="{{ $user->name }}"
                                                                        required
                                                                    >

                                                                </div>

                                                            </div>


                                                            {{-- Email --}}
                                                            <div class="col-md-6">

                                                                <div class="mb-3">

                                                                    <label class="form-label">
                                                                        Email
                                                                    </label>

                                                                    <input
                                                                        type="email"
                                                                        name="email"
                                                                        class="form-control"
                                                                        value="{{ $user->email }}"
                                                                        required
                                                                    >

                                                                </div>

                                                            </div>


                                                            {{-- Phone --}}
                                                            <div class="col-md-6">

                                                                <div class="mb-3">

                                                                    <label class="form-label">
                                                                        Phone Number
                                                                    </label>

                                                                    <input
                                                                        type="text"
                                                                        name="phone_number"
                                                                        class="form-control"
                                                                        value="{{ $user->phone_number }}"
                                                                    >

                                                                </div>

                                                            </div>


                                                            {{-- Position --}}
                                                            <div class="col-md-6">

                                                                <div class="mb-3">

                                                                    <label class="form-label">
                                                                        Position
                                                                    </label>

                                                                    <input
                                                                        type="text"
                                                                        name="position"
                                                                        class="form-control"
                                                                        value="{{ $user->position }}"
                                                                    >

                                                                </div>

                                                            </div>


                                                            {{-- Role --}}
                                                            <div class="col-md-6">

                                                                <div class="mb-3">

                                                                    <label class="form-label">
                                                                        Role
                                                                    </label>

                                                                    <select
                                                                        name="role"
                                                                        class="form-select"
                                                                        required
                                                                    >

                                                                        <option
                                                                            value="Admin"
                                                                            {{ $user->role === 'Admin' ? 'selected' : '' }}
                                                                        >
                                                                            Admin
                                                                        </option>

                                                                        <option
                                                                            value="Staff"
                                                                            {{ $user->role === 'Staff' ? 'selected' : '' }}
                                                                        >
                                                                            Staff
                                                                        </option>

                                                                    </select>

                                                                </div>

                                                            </div>


                                                            {{-- Status --}}
                                                            <div class="col-md-6">

                                                                <div class="mb-3">

                                                                    <label class="form-label">
                                                                        Status
                                                                    </label>

                                                                    <select
                                                                        name="status"
                                                                        class="form-select"
                                                                        required
                                                                    >

                                                                        <option
                                                                            value="Active"
                                                                            {{ $user->status === 'Active' ? 'selected' : '' }}
                                                                        >
                                                                            Active
                                                                        </option>

                                                                        <option
                                                                            value="Inactive"
                                                                            {{ $user->status === 'Inactive' ? 'selected' : '' }}
                                                                        >
                                                                            Inactive
                                                                        </option>

                                                                    </select>

                                                                </div>

                                                            </div>


                                                            {{-- New Password --}}
                                                            <div class="col-md-6">

                                                                <div class="mb-3">

                                                                    <label class="form-label">
                                                                        New Password
                                                                    </label>

                                                                    <input
                                                                        type="password"
                                                                        name="password"
                                                                        class="form-control"
                                                                    >

                                                                    <div class="form-text">
                                                                        Leave blank to keep the current password.
                                                                    </div>

                                                                </div>

                                                            </div>


                                                            {{-- Confirm New Password --}}
                                                            <div class="col-md-6">

                                                                <div class="mb-3">

                                                                    <label class="form-label">
                                                                        Confirm New Password
                                                                    </label>

                                                                    <input
                                                                        type="password"
                                                                        name="password_confirmation"
                                                                        class="form-control"
                                                                    >

                                                                </div>

                                                            </div>

                                                        </div>

                                                    </div>


                                                    {{-- Modal Footer --}}
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
                                            class="text-center py-5 text-body-secondary"
                                        >
                                            No users found.
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>


                {{-- Pagination --}}
                @if($users->hasPages())

                    <div class="card-footer">

                        {{ $users->links() }}

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>

@endsection