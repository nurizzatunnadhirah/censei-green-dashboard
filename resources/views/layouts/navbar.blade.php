<header class="header header-sticky p-0 mb-4">

    <div class="container-fluid border-bottom px-4">

        {{-- Sidebar Toggle --}}
        <button
            class="header-toggler"
            type="button"
            onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"
            style="margin-inline-start: -14px"
        >
            <svg
                class="icon icon-lg"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 512 512"
            >
                <path
                    fill="var(--ci-primary-color, currentcolor)"
                    d="M80 96h352v32H80zm0 144h352v32H80zm0 144h352v32H80z"
                    class="ci-primary"
                />
            </svg>
        </button>


        {{-- Right Navigation --}}
        <ul class="header-nav ms-auto">

            {{-- Theme Divider --}}
            <li class="nav-item py-1">
                <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
            </li>


            {{-- Theme Switcher --}}
            <li class="nav-item dropdown">

                <button
                    class="btn btn-link nav-link py-2 px-2 d-flex align-items-center"
                    type="button"
                    aria-expanded="false"
                    data-coreui-toggle="dropdown"
                >

                    <svg
                        class="icon icon-lg theme-icon-active"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 512 512"
                    >
                        <path
                            fill="var(--ci-primary-color, currentcolor)"
                            d="M256 16C123.452 16 16 123.452 16 256s107.452 240 240 240 240-107.452 240-240S388.548 16 256 16m-22 446.849a208.35 208.35 0 0 1-169.667-125.9c-.364-.859-.706-1.724-1.057-2.587L234 429.939Zm0-69.582L50.889 290.76A210 210 0 0 1 48 256q0-9.912.922-19.67L234 339.939Zm0-90L54.819 202.96a206 206 0 0 1 9.514-27.913Q67.1 168.5 70.3 162.191L234 253.934Zm0-86.015L86.914 134.819a209.4 209.4 0 0 1 22.008-25.9q3.72-3.72 7.6-7.228L234 166.027Zm0-87.708-89.648-49.093A206.95 206.95 0 0 1 234 49.151ZM464 256a207.775 207.775 0 0 1-198 207.761V48.239A207.79 207.79 0 0 1 464 256"
                            class="ci-primary"
                        />
                    </svg>

                </button>


                <ul
                    class="dropdown-menu dropdown-menu-end"
                    style="--cui-dropdown-min-width: 8rem"
                >

                    {{-- Light --}}
                    <li>

                        <button
                            class="dropdown-item d-flex align-items-center"
                            type="button"
                            data-coreui-theme-value="light"
                        >

                            <i class="icon icon-lg me-3 cil-sun"></i>

                            Light

                        </button>

                    </li>


                    {{-- Dark --}}
                    <li>

                        <button
                            class="dropdown-item d-flex align-items-center"
                            type="button"
                            data-coreui-theme-value="dark"
                        >

                            <i class="icon icon-lg me-3 cil-moon"></i>

                            Dark

                        </button>

                    </li>


                    {{-- Auto --}}
                    <li>

                        <button
                            class="dropdown-item d-flex align-items-center active"
                            type="button"
                            data-coreui-theme-value="auto"
                        >

                            <i class="icon icon-lg me-3 cil-contrast"></i>

                            Auto

                        </button>

                    </li>

                </ul>

            </li>


            {{-- Divider --}}
            <li class="nav-item py-1">
                <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
            </li>


            {{-- Account Dropdown --}}
            <li class="nav-item dropdown">

                <a
                    class="nav-link py-0 pe-0"
                    data-coreui-toggle="dropdown"
                    href="#"
                    role="button"
                    aria-haspopup="true"
                    aria-expanded="false"
                >

                    <div class="avatar avatar-md">

                        @if(auth()->user()->profile_photo ?? false)

                            <img
                                class="avatar-img"
                                src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                                alt="{{ auth()->user()->name }}"
                            >

                        @else

                            <div
                                class="avatar-img d-flex align-items-center justify-content-center bg-secondary text-white fw-semibold"
                                style="
                                    width: 100%;
                                    height: 100%;
                                    border-radius: 50%;
                                    font-size: 0.9rem;
                                "
                            >
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>

                        @endif

                    </div>

                </a>


                <div class="dropdown-menu dropdown-menu-end pt-0">

                    {{-- Account Header --}}
                    <div
                        class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2"
                    >
                        Account
                    </div>


                    {{-- Current User --}}
                    <div class="px-3 py-2">

                        <div class="fw-semibold">
                            {{ auth()->user()->name }}
                        </div>

                        <div class="small text-body-secondary">
                            {{ auth()->user()->email }}
                        </div>

                        <div class="small text-body-secondary">
                            {{ auth()->user()->role ?? 'Staff' }}
                        </div>

                    </div>


                    <div class="dropdown-divider"></div>


                    {{-- Profile --}}
                    <a
                        class="dropdown-item"
                        href="{{ route('profile.edit') }}"
                    >

                        <i class="icon me-2 cil-user"></i>

                        Profile

                    </a>


                    <div class="dropdown-divider"></div>


                    {{-- Logout --}}
                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="dropdown-item border-0 bg-transparent w-100 text-start"
                        >

                            <i class="icon me-2 cil-account-logout"></i>

                            Logout

                        </button>

                    </form>

                </div>

            </li>

        </ul>

    </div>


    {{-- Breadcrumb --}}
    <div class="container-fluid px-4">

        <nav aria-label="breadcrumb">

            <ol class="breadcrumb my-0">

                <li class="breadcrumb-item">

                    <a href="{{ route('dashboard') }}">
                        Home
                    </a>

                </li>

                <li class="breadcrumb-item active">

                    <span>
                        @yield('title', 'Dashboard')
                    </span>

                </li>

            </ol>

        </nav>

    </div>

</header>