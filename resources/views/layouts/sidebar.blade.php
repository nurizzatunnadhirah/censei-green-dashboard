<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">

    <!-- Sidebar Header -->
    <div class="sidebar-header border-bottom">

        <div class="sidebar-brand me-auto d-flex flex-column justify-content-center align-items-center py-3">

            <!-- Expanded Sidebar -->
            <div class="sidebar-brand-full text-center">
                <div class="fs-3 fw-bold text-success">
                    CENSEi
                </div>

                <div class="small text-light opacity-75">
                    Green Dashboard
                </div>
            </div>

            <!-- Collapsed Sidebar -->
            <div class="sidebar-brand-narrow fs-4 fw-bold text-success">
                CG
            </div>

        </div>

        <button
            class="btn-close d-lg-none"
            type="button"
            data-coreui-theme="dark"
            aria-label="Close"
            onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
        </button>

    </div>

    <!-- Sidebar Navigation -->
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar>

        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link active" href="/">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path fill="currentColor"
                        d="M425.706 142.294A240 240 0 0 0 16 312v88h144v-32H48v-56c0-114.691 93.309-208 208-208s208 93.309 208 208v56H352v32h144v-88a238.43 238.43 0 0 0-70.294-169.706"/>
                    <path fill="currentColor"
                        d="M80 264h32v32H80zm160-136h32v32h-32zm-104 40h32v32h-32zm264 96h32v32h-32zm-102.778 71.1 69.2-144.173-28.85-13.848-69.183 144.135a64.141 64.141 0 1 0 28.833 13.886M256 416a32 32 0 1 1 32-32 32.036 32.036 0 0 1-32 32"/>
                </svg>

                Dashboard
            </a>
        </li>

        <!-- DATA MANAGEMENT -->
        <li class="nav-group">

            <a class="nav-link nav-group-toggle" href="#">
                <i class="nav-icon cil-storage"></i>
                Data Management
            </a>

            <ul class="nav-group-items compact">

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>
                        Organic Waste
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>
                        Inorganic Waste
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>
                        Toxic Waste
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>
                        Non-recyclable Waste
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>
                        Waste Reduction
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>
                        Carbon Reduction
                    </a>
                </li>

            </ul>

        </li>

        <!-- ANALYTICS -->
        <li class="nav-group">

            <a class="nav-link nav-group-toggle" href="#">
                <i class="nav-icon cil-chart-line"></i>
                Analytics
            </a>

            <ul class="nav-group-items compact">

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>
                        Waste Analytics
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>
                        Carbon Analytics
                    </a>
                </li>

            </ul>

        </li>

        <!-- REPORTS -->
        <li class="nav-group">

            <a class="nav-link nav-group-toggle" href="#">
                <i class="nav-icon cil-description"></i>
                Reports
            </a>

            <ul class="nav-group-items compact">

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>
                        Monthly Report
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>
                        Annual Report
                    </a>
                </li>

            </ul>

        </li>

        <!-- USERS -->
        <li class="nav-group">

            <a class="nav-link nav-group-toggle" href="#">
                <i class="nav-icon cil-people"></i>
                User Management
            </a>

            <ul class="nav-group-items compact">

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>
                        Staff
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>
                        Admin
                    </a>
                </li>

            </ul>

        </li>

    </ul>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer border-top d-none d-md-flex">
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
    </div>

</div>