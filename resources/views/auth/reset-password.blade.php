<x-guest-layout>

    <style>
        .password-wrapper {
            position: relative;
            margin-top: 0.25rem;
        }

        .password-wrapper input {
            width: 100%;
            padding-right: 65px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            color: #4b5563;
            font-size: 14px;
            padding: 4px;
        }

        .password-toggle:hover {
            color: #111827;
        }
    </style>


    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input
            type="hidden"
            name="token"
            value="{{ $request->route('token') }}"
        >


        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email', $request->email)"
                required
                autofocus
                autocomplete="username"
            />

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"
            />
        </div>


        <!-- Password -->
        <div class="mt-4">

            <x-input-label
                for="password"
                :value="__('Password')"
            />

            <div class="password-wrapper">

                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="border-gray-300 focus:border-indigo-500
                           focus:ring-indigo-500 rounded-md shadow-sm"
                >

                <button
                    type="button"
                    class="password-toggle"
                    data-target="password"
                >
                    Show
                </button>

            </div>

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />

        </div>


        <!-- Confirm Password -->
        <div class="mt-4">

            <x-input-label
                for="password_confirmation"
                :value="__('Confirm Password')"
            />

            <div class="password-wrapper">

                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="border-gray-300 focus:border-indigo-500
                           focus:ring-indigo-500 rounded-md shadow-sm"
                >

                <button
                    type="button"
                    class="password-toggle"
                    data-target="password_confirmation"
                >
                    Show
                </button>

            </div>

            <x-input-error
                :messages="$errors->get('password_confirmation')"
                class="mt-2"
            />

        </div>


        <!-- Reset Button -->
        <div class="flex items-center justify-end mt-4">

            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>

        </div>

    </form>


    <script>
        document.querySelectorAll('.password-toggle').forEach(function (button) {

            button.addEventListener('click', function () {

                const input =
                    document.getElementById(this.dataset.target);

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

</x-guest-layout>