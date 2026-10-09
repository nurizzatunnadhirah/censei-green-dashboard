<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">

    <style>
        .censei-brand {
            padding: 22px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .censei-brand-inner {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
        }

        .censei-brand-icon {
            width: 46px;
            height: 46px;
            min-width: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(46, 184, 92, 0.14);
            color: #2eb85c;
            font-size: 22px;
            border: 1px solid rgba(46, 184, 92, 0.22);
        }

        .censei-brand-text {
            line-height: 1.2;
            overflow: hidden;
        }

        .censei-brand-org {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #2eb85c;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .censei-brand-title {
            font-size: 17px;
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
        }

        .censei-brand-subtitle {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.48);
            margin-top: 4px;
            white-space: nowrap;
        }

        .sidebar-unfoldable .censei-brand-text {
            display: none;
        }

        .sidebar-unfoldable .censei-brand {
            padding-left: 10px;
            padding-right: 10px;
        }

        .sidebar-unfoldable .censei-brand-inner {
            justify-content: center;
        }

        .sidebar-unfoldable .censei-brand-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;
        }
    </style>


    {{-- Sidebar Branding --}}
    <div class="sidebar-header censei-brand">

        <div class="censei-brand-inner">

            <div class="censei-brand-icon">
                <i class="cil-leaf"></i>
            </div>

            <div class="censei-brand-text">

                <div class="censei-brand-org">
                    CENSEi
                </div>

                <div class="censei-brand-title">
                    Green Dashboard
                </div>

                <div class="censei-brand-subtitle">
                    Sustainability Management
                </div>

            </div>

        </div>


        <button
            class="btn-close d-lg-none"
            type="button"
            data-coreui-theme="dark"
            aria-label="Close"
            onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"
        >
        </button>

    </div>


{{-- Sidebar Navigation --}}
<ul
    class="sidebar-nav"
    data-coreui="navigation"
    data-simplebar
>

    {{-- ========================================== --}}
    {{-- PUBLIC SIDEBAR --}}
    {{-- ========================================== --}}

    @guest

        {{-- Dashboard --}}
        <li class="nav-item">

            <a
                class="nav-link {{ request()->routeIs('public.dashboard') ? 'active' : '' }}"
                href="{{ route('public.dashboard') }}"
            >

                <svg
                    class="nav-icon"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 512 512"
                >

                    <path
                        fill="currentColor"
                        d="M425.706 142.294A240 240 0 0 0 16 312v88h144v-32H48v-56c0-114.691 93.309-208 208-208s208 93.309 208 208v56H352v32h144v-88a238.43 238.43 0 0 0-70.294-169.706"
                    />

                    <path
                        fill="currentColor"
                        d="M80 264h32v32H80zm160-136h32v32h-32zm-104 40h32v32h-32zm264 96h32v32h-32zm-102.778 71.1 69.2-144.173-28.85-13.848-69.183 144.135a64.141 64.141 0 1 0 28.833 13.886M256 416a32 32 0 1 1 32-32 32.036 32.036 0 0 1-32 32"
                    />

                </svg>

                Dashboard

            </a>

        </li>

    @endguest


    {{-- ========================================== --}}
    {{-- AUTHENTICATED SIDEBAR --}}
    {{-- ========================================== --}}

    @auth

        {{-- Dashboard --}}
        <li class="nav-item">

            <a
                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                href="{{ route('dashboard') }}"
            >

                <svg
                    class="nav-icon"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 512 512"
                >

                    <path
                        fill="currentColor"
                        d="M425.706 142.294A240 240 0 0 0 16 312v88h144v-32H48v-56c0-114.691 93.309-208 208-208s208 93.309 208 208v56H352v32h144v-88a238.43 238.43 0 0 0-70.294-169.706"
                    />

                    <path
                        fill="currentColor"
                        d="M80 264h32v32H80zm160-136h32v32h-32zm-104 40h32v32h-32zm264 96h32v32h-32zm-102.778 71.1 69.2-144.173-28.85-13.848-69.183 144.135a64.141 64.141 0 1 0 28.833 13.886M256 416a32 32 0 1 1 32-32 32.036 32.036 0 0 1-32 32"
                    />

                </svg>

                Dashboard

            </a>

        </li>


        {{-- Waste Records --}}
        <li class="nav-item">

            <a
                class="nav-link {{ request()->routeIs('waste-records.*') ? 'active' : '' }}"
                href="{{ route('waste-records.index') }}"
            >

                <i class="nav-icon cil-trash"></i>

                Waste Records

            </a>

        </li>


        {{-- Legacy Import --}}
        <li class="nav-item">

            <a
                class="nav-link {{ request()->routeIs('legacy.*') ? 'active' : '' }}"
                href="{{ route('legacy.index') }}"
            >

                <i class="nav-icon cil-cloud-upload"></i>

                Legacy Import

            </a>

        </li>


        {{-- Landfill Import --}}
        <li class="nav-item">

            <a
                class="nav-link {{ request()->routeIs('landfill.*') ? 'active' : '' }}"
                href="{{ route('landfill.index') }}"
            >

                <i class="nav-icon cil-file"></i>

                Landfill Import

            </a>

        </li>


        {{-- Reports --}}
        <li class="nav-item">

            <a
                class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                href="{{ route('reports.index') }}"
            >

                <i class="nav-icon cil-description"></i>

                Reports

            </a>

        </li>


        {{-- Data Management --}}
        <li
            class="nav-group {{ request()->routeIs('waste-types.*', 'waste-categories.*', 'locations.*') ? 'show' : '' }}"
        >

            <a
                class="nav-link nav-group-toggle"
                href="#"
            >

                <i class="nav-icon cil-storage"></i>

                Data Management

            </a>

            <ul class="nav-group-items compact">

                {{-- Waste Types --}}
                <li class="nav-item">

                    <a
                        class="nav-link {{ request()->routeIs('waste-types.*') ? 'active' : '' }}"
                        href="{{ route('waste-types.index') }}"
                    >

                        <span class="nav-icon">
                            <span class="nav-icon-bullet"></span>
                        </span>

                        Waste Types

                    </a>

                </li>


                {{-- Waste Categories --}}
                <li class="nav-item">

                    <a
                        class="nav-link {{ request()->routeIs('waste-categories.*') ? 'active' : '' }}"
                        href="{{ route('waste-categories.index') }}"
                    >

                        <span class="nav-icon">
                            <span class="nav-icon-bullet"></span>
                        </span>

                        Waste Categories

                    </a>

                </li>

                {{-- Locations / Programs: insert inside Data Management <ul class="nav-group-items compact"> --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}"
       href="{{ route('locations.index') }}">
        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>
        Locations / Programs
    </a>
</li>


            </ul>

        </li>


        {{-- User Management --}}
        @if(auth()->user()->role === 'Admin')

            <li class="nav-item">

                <a
                    class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                    href="{{ route('users.index') }}"
                >

                    <i class="nav-icon cil-people"></i>

                    User Management

                </a>

            </li>

        @endif

    @endauth

</ul>

    {{-- Sidebar Footer --}}
    <div class="sidebar-footer border-top d-none d-md-flex">

        <button
            class="sidebar-toggler"
            type="button"
            data-coreui-toggle="unfoldable"
        >
        </button>

    </div>

</div>