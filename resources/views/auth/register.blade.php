@extends('layouts.auth')

@section('title', 'Sign Up')

@section('content')
<!-- Form -->
<form class="form w-100" method="POST" action="{{ route('register') }}" novalidate="novalidate" id="kt_sign_up_form">
    @csrf
    
    <!-- Heading -->
    <div class="text-center mb-11">
        <!-- Title -->
        <h1 class="text-gray-900 fw-bolder mb-3">Sign Up</h1>
        <!-- End Title -->
        <!-- Subtitle -->
        <div class="text-gray-500 fw-semibold fs-6">Create your account</div>
        <!-- End Subtitle -->
    </div>
    <!-- End Heading -->

    <!-- Name Input -->
    <div class="fv-row mb-8">
        <input type="text" 
               placeholder="Full Name" 
               name="name" 
               value="{{ old('name') }}"
               autocomplete="name" 
               class="form-control bg-transparent @error('name') is-invalid @enderror" 
               required 
               autofocus />
        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>
    <!-- End Name Input -->

    <!-- Email Input -->
    <div class="fv-row mb-8">
        <input type="email" 
               placeholder="Email" 
               name="email" 
               value="{{ old('email') }}"
               autocomplete="username" 
               class="form-control bg-transparent @error('email') is-invalid @enderror" 
               required />
        @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>
    <!-- End Email Input -->

    <!-- Password Input -->
    <div class="fv-row mb-8">
        <input type="password" 
               placeholder="Password" 
               name="password" 
               autocomplete="new-password" 
               class="form-control bg-transparent @error('password') is-invalid @enderror" 
               required />
        @error('password')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>
    <!-- End Password Input -->

    <!-- Confirm Password Input -->
    <div class="fv-row mb-8">
        <input type="password" 
               placeholder="Confirm Password" 
               name="password_confirmation" 
               autocomplete="new-password" 
               class="form-control bg-transparent @error('password_confirmation') is-invalid @enderror" 
               required />
        @error('password_confirmation')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>
    <!-- End Confirm Password Input -->

    <!-- Submit Button -->
    <div class="d-grid mb-10">
        <button type="submit" id="kt_sign_up_submit" class="btn btn-primary">
            <!-- Indicator label -->
            <span class="indicator-label">Sign Up</span>
            <!-- End Indicator label -->
            <!-- Indicator progress -->
            <span class="indicator-progress">Please wait... 
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
            <!-- End Indicator progress -->
        </button>
    </div>
    <!-- End Submit Button -->

    <!-- Sign in link -->
    <div class="text-gray-500 text-center fw-semibold fs-6">
        Already have an account? 
        <a href="{{ route('login') }}" class="link-primary">Sign in</a>
    </div>
    <!-- End Sign in link -->
</form>
<!-- End Form -->

@push('scripts')
<script src="{{ asset('demo1/assets/js/custom/authentication/sign-up/general.js') }}"></script>
@endpush
@endsection
