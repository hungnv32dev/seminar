@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<!-- Form -->
<form class="form w-100" method="POST" action="{{ route('password.email') }}" novalidate="novalidate" id="kt_password_reset_form">
    @csrf
    
    <!-- Heading -->
    <div class="text-center mb-10">
        <!-- Title -->
        <h1 class="text-gray-900 fw-bolder mb-3">Forgot Password?</h1>
        <!-- End Title -->
        <!-- Subtitle -->
        <div class="text-gray-500 fw-semibold fs-6">
            Enter your email to reset your password.
        </div>
        <!-- End Subtitle -->
    </div>
    <!-- End Heading -->

    <!-- Description -->
    <div class="mb-10 bg-light-info px-8 py-5 rounded">
        <div class="text-info">
            Use your email address to receive a password reset link.
        </div>
    </div>
    <!-- End Description -->

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success mb-8" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <!-- Email Input -->
    <div class="fv-row mb-8">
        <input type="email" 
               placeholder="Email" 
               name="email" 
               value="{{ old('email') }}"
               autocomplete="username" 
               class="form-control bg-transparent @error('email') is-invalid @enderror" 
               required 
               autofocus />
        @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>
    <!-- End Email Input -->

    <!-- Actions -->
    <div class="d-flex flex-wrap justify-content-center pb-lg-0">
        <button type="submit" id="kt_password_reset_submit" class="btn btn-primary me-4">
            <!-- Indicator label -->
            <span class="indicator-label">Submit</span>
            <!-- End Indicator label -->
            <!-- Indicator progress -->
            <span class="indicator-progress">Please wait... 
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
            <!-- End Indicator progress -->
        </button>
        <a href="{{ route('login') }}" class="btn btn-light">Cancel</a>
    </div>
    <!-- End Actions -->
</form>
<!-- End Form -->

@push('scripts')
<script src="{{ asset('demo1/assets/js/custom/authentication/password-reset/password-reset.js') }}"></script>
@endpush
@endsection
