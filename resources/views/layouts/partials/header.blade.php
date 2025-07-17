<div id="kt_app_header" class="app-header" data-kt-sticky="true" data-kt-sticky-activate="{default: true, lg: true}" data-kt-sticky-name="app-header-minimize" data-kt-sticky-offset="{default: '200px', lg: '0'}" data-kt-sticky-animation="false">
    <!-- Header container -->
    <div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_container">
        <!-- Sidebar mobile toggle -->
        <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
            <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
                <i class="ki-duotone ki-abstract-14 fs-2 fs-md-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
        </div>
        
        <!-- Mobile logo -->
        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
            <a href="{{ route('dashboard') }}" class="d-lg-none">
                <img alt="Logo" src="{{ asset('demo1/assets/media/logos/default-small.svg') }}" class="h-30px" />
            </a>
        </div>
        
        <!-- Header wrapper -->
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">
            <!-- Menu wrapper -->
            <div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="{default: 'append', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
                <!-- Menu -->
                <div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0" id="kt_app_header_menu" data-kt-menu="true">
                    <!-- Dashboard -->
                    <div class="menu-item me-0 me-lg-2">
                        <a class="menu-link py-3" href="{{ route('dashboard') }}">
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </div>
                    
                    <!-- Workshops -->
                    <div class="menu-item me-0 me-lg-2">
                        <a class="menu-link py-3" href="#">
                            <span class="menu-title">Workshops</span>
                        </a>
                    </div>
                    
                    <!-- Participants -->
                    <div class="menu-item me-0 me-lg-2">
                        <a class="menu-link py-3" href="#">
                            <span class="menu-title">Participants</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Navbar -->
            <div class="app-navbar flex-shrink-0">
                <!-- User menu -->
                <div class="app-navbar-item ms-1 ms-md-4" id="kt_header_user_menu_toggle">
                    <!-- Menu wrapper -->
                    <div class="cursor-pointer symbol symbol-35px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <img src="{{ asset('demo1/assets/media/avatars/300-3.jpg') }}" class="rounded-3" alt="user" />
                    </div>
                    
                    <!-- User account menu -->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                        <!-- Menu item -->
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <!-- Avatar -->
                                <div class="symbol symbol-50px me-5">
                                    <img alt="Logo" src="{{ asset('demo1/assets/media/avatars/300-3.jpg') }}" />
                                </div>
                                
                                <!-- Username -->
                                <div class="d-flex flex-column">
                                    <div class="fw-bold d-flex align-items-center fs-5">
                                        {{ Auth::user()->name }}
                                        @if(Auth::user()->hasRole('admin'))
                                            <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">Admin</span>
                                        @elseif(Auth::user()->hasRole('organizer'))
                                            <span class="badge badge-light-primary fw-bold fs-8 px-2 py-1 ms-2">Organizer</span>
                                        @elseif(Auth::user()->hasRole('staff'))
                                            <span class="badge badge-light-info fw-bold fs-8 px-2 py-1 ms-2">Staff</span>
                                        @endif
                                    </div>
                                    <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">{{ Auth::user()->email }}</a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Menu separator -->
                        <div class="separator my-2"></div>
                        
                        <!-- Menu item -->
                        <div class="menu-item px-5">
                            <a href="{{ route('profile.edit') }}" class="menu-link px-5">My Profile</a>
                        </div>
                        
                        <!-- Menu separator -->
                        <div class="separator my-2"></div>
                        
                        <!-- Menu item -->
                        <div class="menu-item px-5">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="#" class="menu-link px-5" onclick="event.preventDefault(); this.closest('form').submit();">
                                    Sign Out
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>