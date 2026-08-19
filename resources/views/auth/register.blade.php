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


    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />

            <x-text-input
                id="name"
                class="block mt-1 w-full"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
            />

            <x-input-error
                :messages="$errors->get('name')"
                class="mt-2"
            />
        </div>


        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
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


        <!-- Actions -->
        <div class="flex items-center justify-end mt-4">

            <a
                class="underline text-sm text-gray-600 hover:text-gray-900
                       rounded-md focus:outline-none focus:ring-2
                       focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}"
            >
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
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