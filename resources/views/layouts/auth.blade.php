<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Authentication')</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />

    <!-- Metronic Styles -->
    <link href="{{ asset('demo1/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo1/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('demo1/assets/media/logos/favicon.ico') }}" />

    <!-- Frame-busting -->
    <script>
        if (window.top != window.self) { 
            window.top.location.replace(window.self.location.href); 
        }
    </script>
</head>
<body id="kt_body" class="app-blank">
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

    <!-- Root -->
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!-- Authentication Layout -->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!-- Body -->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                <!-- Form -->
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <!-- Wrapper -->
                    <div class="w-lg-500px p-10">
                        @yield('content')
                    </div>
                    <!-- End Wrapper -->
                </div>
                <!-- End Form -->
                
                <!-- Footer -->
                <div class="w-lg-500px d-flex flex-stack px-10 mx-auto">
                    <!-- Links -->
                    <div class="d-flex fw-semibold text-primary fs-base gap-5">
                        <a href="#" target="_blank">Terms</a>
                        <a href="#" target="_blank">Privacy</a>
                        <a href="#" target="_blank">Contact Us</a>
                    </div>
                    <!-- End Links -->
                </div>
                <!-- End Footer -->
            </div>
            <!-- End Body -->
            
            <!-- Aside -->
            <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2" 
                 style="background-image: url({{ asset('demo1/assets/media/misc/auth-bg.png') }})">
                <!-- Content -->
                <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
                    <!-- Logo -->
                    <a href="{{ route('login') }}" class="mb-0 mb-lg-12">
                        <img alt="Logo" src="{{ asset('demo1/assets/media/logos/custom-1.png') }}" class="h-60px h-lg-75px" />
                    </a>
                    <!-- End Logo -->
                    
                    <!-- Image -->
                    <img class="d-none d-lg-block mx-auto w-275px w-md-50 w-xl-500px mb-10 mb-lg-20" 
                         src="{{ asset('demo1/assets/media/misc/auth-screens.png') }}" alt="" />
                    <!-- End Image -->
                    
                    <!-- Title -->
                    <h1 class="d-none d-lg-block text-white fs-2qx fw-bolder text-center mb-7">
                        Workshop Management System
                    </h1>
                    <!-- End Title -->
                    
                    <!-- Text -->
                    <div class="d-none d-lg-block text-white fs-base text-center">
                        Manage your internal workshops and courses efficiently
                        <br />with our comprehensive management platform.
                        <br />Track participants, send tickets, and monitor attendance.
                    </div>
                    <!-- End Text -->
                </div>
                <!-- End Content -->
            </div>
            <!-- End Aside -->
        </div>
        <!-- End Authentication Layout -->
    </div>
    <!-- End Root -->

    <!-- Scripts -->
    <script>var hostUrl = "{{ asset('demo1/assets/') }}/";</script>
    <script src="{{ asset('demo1/assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('demo1/assets/js/scripts.bundle.js') }}"></script>
    
    @stack('scripts')
</body>
</html>