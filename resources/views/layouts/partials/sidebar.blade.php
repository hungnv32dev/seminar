<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!-- Logo -->
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <!-- Logo image -->
        <a href="{{ route('dashboard') }}">
            <img alt="Logo" src="{{ asset('demo1/assets/media/logos/default-dark.svg') }}" class="h-25px app-sidebar-logo-default" />
            <img alt="Logo" src="{{ asset('demo1/assets/media/logos/default-small.svg') }}" class="h-20px app-sidebar-logo-minimize" />
        </a>
        
        <!-- Sidebar toggle -->
        <div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
            <i class="ki-duotone ki-black-left-line fs-3 rotate-180">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </div>
    </div>
    
    <!-- Menu wrapper -->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <!-- Menu -->
        <div id="kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false" class="app-sidebar-menu-primary menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6 px-6">
            <!-- Dashboard -->
            <div class="menu-item">
                <a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-element-11 fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                    </span>
                    <span class="menu-title">Dashboard</span>
                </a>
            </div>
            
            @can('view workshops')
            <!-- Workshops -->
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->is('workshops*') ? 'here show' : '' }}">
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-abstract-44 fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </span>
                    <span class="menu-title">Workshops</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion">
                    <div class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">All Workshops</span>
                        </a>
                    </div>
                    @can('create workshops')
                    <div class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Create Workshop</span>
                        </a>
                    </div>
                    @endcan
                </div>
            </div>
            @endcan
            
            @can('view participants')
            <!-- Participants -->
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->is('participants*') ? 'here show' : '' }}">
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-profile-circle fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </span>
                    <span class="menu-title">Participants</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion">
                    <div class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">All Participants</span>
                        </a>
                    </div>
                    @can('import participants')
                    <div class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Import Participants</span>
                        </a>
                    </div>
                    @endcan
                </div>
            </div>
            @endcan
            
            @can('view check-in')
            <!-- Check-in -->
            <div class="menu-item">
                <a class="menu-link" href="#">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-scan-barcode fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                            <span class="path6"></span>
                        </i>
                    </span>
                    <span class="menu-title">Check-in</span>
                </a>
            </div>
            @endcan
            
            @can('manage users')
            <!-- User Management -->
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->is('users*') ? 'here show' : '' }}">
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-people fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                    </span>
                    <span class="menu-title">User Management</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion">
                    <div class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Users List</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Roles & Permissions</span>
                        </a>
                    </div>
                </div>
            </div>
            @endcan
            
            <!-- Settings -->
            <div class="menu-item pt-5">
                <div class="menu-content">
                    <span class="menu-heading fw-bold text-uppercase fs-7">Settings</span>
                </div>
            </div>
            
            @can('manage email templates')
            <div class="menu-item">
                <a class="menu-link" href="#">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-sms fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </span>
                    <span class="menu-title">Email Templates</span>
                </a>
            </div>
            @endcan
            
            <!-- Profile -->
            <div class="menu-item">
                <a class="menu-link" href="{{ route('profile.edit') }}">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-badge fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                    </span>
                    <span class="menu-title">My Profile</span>
                </a>
            </div>
        </div>
    </div>
</div>