@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        
        <!--begin::Error-->
        <div class="d-flex flex-column flex-center">
            <!--begin::Title-->
            <h1 class="d-flex flex-wrap fw-semibold fs-9x text-gray-400 mb-7">
                4<span class="text-primary">0</span>3
            </h1>
            <!--end::Title-->
            
            <!--begin::Subtitle-->
            <div class="mb-3">
                <img alt="Logo" src="{{ asset('demo1/assets/media/auth/agency.png') }}" class="h-300px" />
            </div>
            <!--end::Subtitle-->
            
            <!--begin::Title-->
            <h1 class="fw-semibold mb-5 fs-5 text-gray-700">Access Denied</h1>
            <!--end::Title-->
            
            <!--begin::Subtitle-->
            <div class="fw-semibold fs-6 text-gray-500 mb-7">
                You don't have permission to access this resource.
                <br />Please contact your administrator if you believe this is an error.
            </div>
            <!--end::Subtitle-->
            
            <!--begin::Actions-->
            <div class="mb-3">
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Return to Dashboard</a>
            </div>
            <!--end::Actions-->
            
        </div>
        <!--end::Error-->
        
    </div>
</div>
<!--end::Content-->
@endsection