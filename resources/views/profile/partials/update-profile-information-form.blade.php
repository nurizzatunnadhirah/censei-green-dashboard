<section>

    <div class="mb-4">
        <h4 class="fw-semibold mb-1">
            Profile Information
        </h4>

        <div class="text-body-secondary">
            Update your account information, email address and profile picture.
        </div>
    </div>


    <form
        method="POST"
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PATCH')


        <div class="row g-4">

            {{-- Profile Picture --}}
            <div class="col-md-4">

                <div class="card h-100">

                    <div class="card-body text-center">

                        <div class="mb-3">

                            @if($user->profile_photo)

                                <img
                                    src="{{ asset('storage/' . $user->profile_photo) }}"
                                    alt="{{ $user->name }}"
                                    style="
                                        width: 120px;
                                        height: 120px;
                                        object-fit: cover;
                                        border-radius: 50%;
                                    "
                                >

                            @else

                                <div
                                    class="d-flex align-items-center justify-content-center bg-secondary text-white fw-bold mx-auto"
                                    style="
                                        width: 120px;
                                        height: 120px;
                                        border-radius: 50%;
                                        font-size: 2.5rem;
                                    "
                                >
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>

                            @endif

                        </div>


                        <div class="fw-semibold mb-1">
                            Profile Picture
                        </div>

                        <div class="small text-body-secondary mb-3">
                            JPG, JPEG, PNG or WebP. Maximum 2 MB.
                        </div>


                        <input
                            type="file"
                            name="profile_photo"
                            class="form-control"
                            accept=".jpg,.jpeg,.png,.webp"
                        >


                        @error('profile_photo')
                            <div class="text-danger small mt-2">
                                {{ $message }}
                            </div>
                        @enderror


                        @if($user->profile_photo)

                            <div class="mt-3">

                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    onclick="
                                        if (confirm('Remove your profile picture?')) {
                                            document.getElementById('removeProfilePhotoForm').submit();
                                        }
                                    "
                                >
                                    Remove Profile Picture
                                </button>

                            </div>

                        @endif

                    </div>

                </div>

            </div>


            {{-- Account Information --}}
            <div class="col-md-8">

                <div class="card h-100">

                    <div class="card-body">

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
                                id="name"
                                name="name"
                                class="form-control"
                                value="{{ old('name', $user->name) }}"
                                required
                                autocomplete="name"
                            >

                            @error('name')
                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Email --}}
                        <div class="mb-3">

                            <label
                                for="email"
                                class="form-label"
                            >
                                Email Address
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email', $user->email) }}"
                                required
                                autocomplete="username"
                            >

                            @error('email')
                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Role --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Role
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ $user->role ?? 'Staff' }}"
                                disabled
                            >

                        </div>


                        {{-- Position --}}
                        @if($user->position)

                            <div class="mb-3">

                                <label class="form-label">
                                    Position
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{ $user->position }}"
                                    disabled
                                >

                            </div>

                        @endif


                        {{-- Save --}}
                        <div class="d-flex align-items-center gap-3 mt-4">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Save Changes
                            </button>


                            @if(session('status') === 'profile-updated')

                                <span class="text-success">
                                    Profile updated successfully.
                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </form>


    {{-- Separate Remove Photo Form --}}
    @if($user->profile_photo)

        <form
            id="removeProfilePhotoForm"
            method="POST"
            action="{{ route('profile.photo.destroy') }}"
            class="d-none"
        >
            @csrf
            @method('DELETE')
        </form>

    @endif

</section>