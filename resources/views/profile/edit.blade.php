@extends('layouts.app')

@section('title', 'Profile')

@section('content')

<div class="container-fluid px-4 py-4">

    {{-- Page Header --}}
    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            Profile
        </h2>

        <div class="text-body-secondary">
            Manage your personal account settings.
        </div>

    </div>


    {{-- Profile Information --}}
    <div class="card mb-4">

        <div class="card-body">

            @include('profile.partials.update-profile-information-form')

        </div>

    </div>


    {{-- Update Password --}}
    <div class="card mb-4">

        <div class="card-body">

            @include('profile.partials.update-password-form')

        </div>

    </div>


    {{-- Account Management --}}
    <div class="card mb-4 border-danger">

        <div class="card-body">

            @include('profile.partials.delete-user-form')

        </div>

    </div>

</div>

@endsection