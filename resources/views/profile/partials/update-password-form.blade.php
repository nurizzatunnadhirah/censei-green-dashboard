<section>

    <div class="mb-4">

        <h4 class="fw-semibold mb-1">
            Update Password
        </h4>

        <div class="text-body-secondary">
            Use a strong password to keep your account secure.
        </div>

    </div>


    <form
        method="POST"
        action="{{ route('password.update') }}"
    >

        @csrf
        @method('PUT')


        <div class="row g-3">

            {{-- Current Password --}}
            <div class="col-md-12">

                <label
                    for="update_password_current_password"
                    class="form-label"
                >
                    Current Password
                </label>

                <div class="input-group">

                    <input
                        id="update_password_current_password"
                        name="current_password"
                        type="password"
                        class="form-control"
                        autocomplete="current-password"
                    >

                    <button
                        class="btn btn-outline-secondary password-toggle"
                        type="button"
                        data-target="update_password_current_password"
                    >
                        Show
                    </button>

                </div>

                @if($errors->updatePassword->has('current_password'))

                    <div class="text-danger small mt-1">
                        {{ $errors->updatePassword->first('current_password') }}
                    </div>

                @endif

            </div>


            {{-- New Password --}}
            <div class="col-md-6">

                <label
                    for="update_password_password"
                    class="form-label"
                >
                    New Password
                </label>

                <div class="input-group">

                    <input
                        id="update_password_password"
                        name="password"
                        type="password"
                        class="form-control"
                        autocomplete="new-password"
                    >

                    <button
                        class="btn btn-outline-secondary password-toggle"
                        type="button"
                        data-target="update_password_password"
                    >
                        Show
                    </button>

                </div>

                @if($errors->updatePassword->has('password'))

                    <div class="text-danger small mt-1">
                        {{ $errors->updatePassword->first('password') }}
                    </div>

                @endif

            </div>


            {{-- Confirm Password --}}
            <div class="col-md-6">

                <label
                    for="update_password_password_confirmation"
                    class="form-label"
                >
                    Confirm New Password
                </label>

                <div class="input-group">

                    <input
                        id="update_password_password_confirmation"
                        name="password_confirmation"
                        type="password"
                        class="form-control"
                        autocomplete="new-password"
                    >

                    <button
                        class="btn btn-outline-secondary password-toggle"
                        type="button"
                        data-target="update_password_password_confirmation"
                    >
                        Show
                    </button>

                </div>

                @if($errors->updatePassword->has('password_confirmation'))

                    <div class="text-danger small mt-1">
                        {{ $errors->updatePassword->first('password_confirmation') }}
                    </div>

                @endif

            </div>

        </div>


        <div class="d-flex align-items-center gap-3 mt-4">

            <button
                type="submit"
                class="btn btn-primary"
            >
                Update Password
            </button>


            @if(session('status') === 'password-updated')

                <span class="text-success">
                    Password updated successfully.
                </span>

            @endif

        </div>

    </form>


    <script>
        document.querySelectorAll('.password-toggle').forEach(function (button) {

            button.addEventListener('click', function () {

                const input = document.getElementById(this.dataset.target);

                if (input.type === 'password') {

                    input.type = 'text';
                    this.textContent = 'Hide';

                } else {

                    input.type = 'password';
                    this.textContent = 'Show';

                }

            });

        });
    </script>

</section>