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


    <div class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>


    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf


        <!-- Password -->
        <div>

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
                    autocomplete="current-password"
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


        <!-- Confirm Button -->
        <div class="flex justify-end mt-4">

            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>

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

</x-guest-layout>