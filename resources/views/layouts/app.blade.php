<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />

    <!-- Metronic Styles -->
    <link href="{{ asset('demo1/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo1/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo1/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo1/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('demo1/assets/media/logos/favicon.ico') }}" />

    @stack('styles')
</head>
<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
    <!-- Theme mode setup -->
    <script>
        var defaultThemeMode = "light"; 
        var themeMode; 
        if (document.documentElement) { 
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) { 
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); 
            } else { 
                if (localStorage.getItem("data-bs-theme") !== null) { 
                    themeMode = localStorage.getItem("data-bs-theme"); 
                } else { 
                    themeMode = defaultThemeMode; 
                } 
            } 
            if (themeMode === "system") { 
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; 
            } 
            document.documentElement.setAttribute("data-bs-theme", themeMode); 
        }
    </script>

    <!-- App -->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!-- Page -->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <!-- Header -->
            @include('layouts.partials.header')
            
            <!-- Wrapper -->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                <!-- Sidebar -->
                @include('layouts.partials.sidebar')
                
                <!-- Main -->
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <!-- Content wrapper -->
                    <div class="d-flex flex-column flex-column-fluid">
                        <!-- Toolbar -->
                        @hasSection('toolbar')
                            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                                    @yield('toolbar')
                                </div>
                            </div>
                        @endif
                        
                        <!-- Content -->
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div id="kt_app_content_container" class="app-container container-xxl">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    @include('layouts.partials.footer')
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>var hostUrl = "{{ asset('demo1/assets/') }}/";</script>
    <script src="{{ asset('demo1/assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('demo1/assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('demo1/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
    <script src="{{ asset('demo1/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('demo1/assets/js/widgets.bundle.js') }}"></script>
    <script src="{{ asset('demo1/assets/js/custom/widgets.js') }}"></script>
    <script src="{{ asset('demo1/assets/js/custom/apps/chat/chat.js') }}"></script>
    <script src="{{ asset('demo1/assets/js/custom/utilities/modals/upgrade-plan.js') }}"></script>
    <script src="{{ asset('demo1/assets/js/custom/utilities/modals/create-app.js') }}"></script>
    <script src="{{ asset('demo1/assets/js/custom/utilities/modals/users-search.js') }}"></script>

    @stack('scripts')
</body>
</html>