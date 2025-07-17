{{-- Navbar --}}
<div class="app-navbar flex-shrink-0">
    <!-- Search -->
    <div class="app-navbar-item align-items-stretch ms-1 ms-lg-3">
        <div id="kt_header_search" class="header-search d-flex align-items-stretch" data-kt-search-keypress="true" data-kt-search-min-length="2" data-kt-search-enter="enter" data-kt-search-layout="menu" data-kt-menu-trigger="auto" data-kt-menu-overflow="false" data-kt-menu-permanent="true" data-kt-menu-placement="bottom-end">
            <div class="d-flex align-items-center" data-kt-search-element="toggle" id="kt_header_search_toggle">
                <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px">
                    <i class="ki-duotone ki-magnifier fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            
            <div data-kt-search-element="content" class="menu menu-sub menu-sub-dropdown p-7 w-325px w-md-375px">
                <div data-kt-search-element="wrapper">
                    <form data-kt-search-element="form" class="w-100 position-relative mb-3" autocomplete="off">
                        <i class="ki-duotone ki-magnifier fs-2 text-gray-500 position-absolute top-50 translate-middle-y ms-0">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <input type="text" class="search-input form-control form-control-flush ps-10" name="search" value="" placeholder="Search..." data-kt-search-element="input" />
                        <span class="search-spinner position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-1" data-kt-search-element="spinner">
                            <span class="spinner-border h-15px w-15px align-middle text-gray-500"></span>
                        </span>
                        <span class="search-reset btn btn-flush btn-active-color-primary position-absolute top-50 end-0 translate-middle-y lh-0 d-none" data-kt-search-element="clear">
                            <i class="ki-duotone ki-cross fs-2 fs-lg-1 me-0">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </form>
                    
                    <div data-kt-search-element="main" class="d-none">
                        <div class="d-flex flex-stack fw-semibold mb-4">
                            <span class="text-muted fs-6 me-2">Recently Searched:</span>
                        </div>
                        
                        <div class="scroll-y mh-200px mh-lg-325px">
                            <div class="d-flex align-items-center mb-5">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light">
                                        <i class="ki-duotone ki-laptop fs-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="#" class="fs-6 text-gray-800 text-hover-primary fw-semibold">BoomApp by Keenthemes</a>
                                    <span class="fs-7 text-muted fw-semibold">Metronic admin theme</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Activities -->
    <div class="app-navbar-item ms-1 ms-lg-3">
        <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px position-relative" id="kt_activities_toggle">
            <i class="ki-duotone ki-chart-simple fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
                <span class="path4"></span>
            </i>
        </div>
    </div>
    
    <!-- Notifications -->
    <div class="app-navbar-item ms-1 ms-lg-3">
        <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px position-relative" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
            <i class="ki-duotone ki-notification-status fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
                <span class="path4"></span>
            </i>
        </div>
        
        <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true" id="kt_menu_notifications">
            <div class="d-flex flex-column bgi-no-repeat rounded-top" style="background-image:url('{{ asset('assets/media/misc/menu-header-bg.jpg') }}')">
                <h3 class="text-white fw-semibold px-9 mt-10 mb-6">Notifications <span class="fs-8 opacity-75 ps-3">24 reports</span></h3>
                
                <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-semibold px-9">
                    <li class="nav-item">
                        <a class="nav-link text-white opacity-75 opacity-state-100 pb-4 active" data-bs-toggle="tab" href="#kt_topbar_notifications_1">Alerts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white opacity-75 opacity-state-100 pb-4" data-bs-toggle="tab" href="#kt_topbar_notifications_2">Updates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white opacity-75 opacity-state-100 pb-4" data-bs-toggle="tab" href="#kt_topbar_notifications_3">Logs</a>
                    </li>
                </ul>
            </div>
            
            <div class="tab-content">
                <div class="tab-pane fade show active" id="kt_topbar_notifications_1" role="tabpanel">
                    <div class="scroll-y mh-325px my-5 px-8">
                        <div class="d-flex flex-stack py-4">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-35px me-4">
                                    <span class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-abstract-28 fs-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="mb-0 me-2">
                                    <a href="#" class="fs-6 text-gray-800 text-hover-primary fw-bold">Project Alice</a>
                                    <div class="text-gray-500 fs-7">Phase 1 development</div>
                                </div>
                            </div>
                            <span class="badge badge-light fs-8">1 hr</span>
                        </div>
                    </div>
                    
                    <div class="py-3 text-center border-top">
                        <a href="#" class="btn btn-color-gray-600 btn-active-color-primary">View All <i class="ki-duotone ki-arrow-right fs-5"><span class="path1"></span><span class="path2"></span></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick links -->
    <div class="app-navbar-item ms-1 ms-lg-3">
        <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
            <i class="ki-duotone ki-element-plus fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
                <span class="path4"></span>
                <span class="path5"></span>
            </i>
        </div>
        
        <div class="menu menu-sub menu-sub-dropdown menu-column w-250px w-lg-325px" data-kt-menu="true">
            <div class="d-flex flex-column flex-center bgi-no-repeat rounded-top px-9 py-10" style="background-image:url('{{ asset('assets/media/misc/menu-header-bg.jpg') }}')">
                <h3 class="text-white fw-semibold mb-3">Quick Links</h3>
                <span class="badge bg-primary text-inverse-primary py-2 px-3">25 pending tasks</span>
            </div>
            
            <div class="row g-0">
                <div class="col-6">
                    <a href="#" class="d-flex flex-column flex-center h-100 p-6 bg-hover-light border-end border-bottom">
                        <i class="ki-duotone ki-dollar fs-3x text-primary mb-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <span class="fs-5 fw-semibold text-gray-800 mb-0">Accounting</span>
                        <span class="fs-7 text-gray-500">eCommerce</span>
                    </a>
                </div>
                
                <div class="col-6">
                    <a href="#" class="d-flex flex-column flex-center h-100 p-6 bg-hover-light border-bottom">
                        <i class="ki-duotone ki-sms fs-3x text-primary mb-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <span class="fs-5 fw-semibold text-gray-800 mb-0">Administration</span>
                        <span class="fs-7 text-gray-500">Console</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Theme mode -->
    <div class="app-navbar-item ms-1 ms-lg-3">
        <a href="#" class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px" data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
            <i class="ki-duotone ki-night-day theme-light-show fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
                <span class="path4"></span>
                <span class="path5"></span>
                <span class="path6"></span>
                <span class="path7"></span>
                <span class="path8"></span>
                <span class="path9"></span>
                <span class="path10"></span>
            </i>
            <i class="ki-duotone ki-moon theme-dark-show fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </a>
        
        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px" data-kt-menu="true" data-kt-element="theme-mode-menu">
            <div class="menu-item px-3 my-0">
                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                    <span class="menu-icon" data-kt-element="icon">
                        <i class="ki-duotone ki-night-day fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                            <span class="path6"></span>
                            <span class="path7"></span>
                            <span class="path8"></span>
                            <span class="path9"></span>
                            <span class="path10"></span>
                        </i>
                    </span>
                    <span class="menu-title">Light</span>
                </a>
            </div>
            
            <div class="menu-item px-3 my-0">
                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                    <span class="menu-icon" data-kt-element="icon">
                        <i class="ki-duotone ki-moon fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </span>
                    <span class="menu-title">Dark</span>
                </a>
            </div>
            
            <div class="menu-item px-3 my-0">
                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                    <span class="menu-icon" data-kt-element="icon">
                        <i class="ki-duotone ki-screen fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                    </span>
                    <span class="menu-title">System</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- User menu -->
    <div class="app-navbar-item ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
        <div class="cursor-pointer symbol symbol-35px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
            <img src="{{ asset('assets/media/avatars/300-3.jpg') }}" class="rounded-3" alt="user" />
        </div>
        
        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
            <div class="menu-item px-3">
                <div class="menu-content d-flex align-items-center px-3">
                    <div class="symbol symbol-50px me-5">
                        <img alt="Logo" src="{{ asset('assets/media/avatars/300-3.jpg') }}" />
                    </div>
                    
                    <div class="d-flex flex-column">
                        <div class="fw-bold d-flex align-items-center fs-5">
                            @auth
                                {{ auth()->user()->name }}
                            @else
                                Guest User
                            @endauth
                            <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">Pro</span>
                        </div>
                        <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">
                            @auth
                                {{ auth()->user()->email }}
                            @else
                                guest@seminars.com
                            @endauth
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="separator my-2"></div>
            
            <div class="menu-item px-5">
                <a href="#" class="menu-link px-5">My Profile</a>
            </div>
            
            <div class="menu-item px-5">
                <a href="#" class="menu-link px-5">
                    <span class="menu-text">My Projects</span>
                    <span class="menu-badge">
                        <span class="badge badge-light-danger badge-circle fw-bold fs-7">3</span>
                    </span>
                </a>
            </div>
            
            <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                <a href="#" class="menu-link px-5">
                    <span class="menu-title">My Subscription</span>
                    <span class="menu-arrow"></span>
                </a>
                
                <div class="menu-sub menu-sub-dropdown w-175px py-4">
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-5">Referrals</a>
                    </div>
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-5">Billing</a>
                    </div>
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-5">Payments</a>
                    </div>
                </div>
            </div>
            
            <div class="menu-item px-5">
                <a href="#" class="menu-link px-5">My Statements</a>
            </div>
            
            <div class="separator my-2"></div>
            
            <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                <a href="#" class="menu-link px-5">
                    <span class="menu-title position-relative">Language 
                        <span class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">English <img class="w-15px h-15px rounded-1 ms-2" src="{{ asset('assets/media/flags/united-states.svg') }}" alt="" /></span>
                    </span>
                </a>
                
                <div class="menu-sub menu-sub-dropdown w-175px py-4">
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link d-flex px-5 active">
                            <span class="symbol symbol-20px me-4">
                                <img class="rounded-1" src="{{ asset('assets/media/flags/united-states.svg') }}" alt="" />
                            </span>English
                        </a>
                    </div>
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link d-flex px-5">
                            <span class="symbol symbol-20px me-4">
                                <img class="rounded-1" src="{{ asset('assets/media/flags/spain.svg') }}" alt="" />
                            </span>Spanish
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="menu-item px-5 my-1">
                <a href="#" class="menu-link px-5">Account Settings</a>
            </div>
            
            <div class="menu-item px-5">
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="#" class="menu-link px-5" onclick="event.preventDefault(); this.closest('form').submit();">
                            Sign Out
                        </a>
                    </form>
                @else
                    <a href="#" class="menu-link px-5">
                        Sign In
                    </a>
                @endauth
            </div>
        </div>
    </div>
    
    <!-- Header menu toggle -->
    <div class="app-navbar-item d-lg-none ms-2 me-n2" title="Show header menu">
        <div class="btn btn-flex btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_header_menu_toggle">
            <i class="ki-duotone ki-text-align-left fs-1">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
                <span class="path4"></span>
            </i>
        </div>
    </div>
</div>
