@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
<!-- Form -->
<form class="form w-100" method="POST" action="{{ route('login') }}" novalidate="novalidate" id="kt_sign_in_form">
    @csrf
    
    <!-- Heading -->
    <div class="text-center mb-11">
        <!-- Title -->
        <h1 class="text-gray-900 fw-bolder mb-3">Sign In</h1>
        <!-- End Title -->
        <!-- Subtitle -->
        <div class="text-gray-500 fw-semibold fs-6">Workshop Management System</div>
        <!-- End Subtitle -->
    </div>
    <!-- End Heading -->

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

    <!-- Password Input -->
    <div class="fv-row mb-3">
        <input type="password" 
               placeholder="Password" 
               name="password" 
               autocomplete="current-password" 
               class="form-control bg-transparent @error('password') is-invalid @enderror" 
               required />
        @error('password')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>
    <!-- End Password Input -->

    <!-- Remember Me & Forgot Password -->
    <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
            <label class="form-check-label text-gray-700" for="remember_me">
                Remember me
            </label>
        </div>
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="link-primary">
                Forgot Password?
            </a>
        @endif
    </div>
    <!-- End Remember Me & Forgot Password -->

    <!-- Submit Button -->
    <div class="d-grid mb-10">
        <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
            <!-- Indicator label -->
            <span class="indicator-label">Sign In</span>
            <!-- End Indicator label -->
            <!-- Indicator progress -->
            <span class="indicator-progress">Please wait... 
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
            <!-- End Indicator progress -->
        </button>
    </div>
    <!-- End Submit Button -->

    <!-- Contact admin link -->
    <div class="text-gray-500 text-center fw-semibold fs-6">
        Need access? Contact your system administrator.
    </div>
    <!-- End Contact admin link -->
</form>
<!-- End Form -->

@push('scripts')
<script src="{{ asset('demo1/assets/js/custom/authentication/sign-in/general.js') }}"></script>
@endpush
@endsection
