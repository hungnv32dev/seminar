@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<!-- Form -->
<form class="form w-100" method="POST" action="{{ route('password.store') }}" novalidate="novalidate" id="kt_new_password_form">
    @csrf
    
    <!-- Password Reset Token -->
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    
    <!-- Heading -->
    <div class="text-center mb-10">
        <!-- Title -->
        <h1 class="text-gray-900 fw-bolder mb-3">Setup New Password</h1>
        <!-- End Title -->
        <!-- Subtitle -->
        <div class="text-gray-500 fw-semibold fs-6">
            Have you already reset the password? 
            <a href="{{ route('login') }}" class="link-primary fw-bold">Sign in</a>
        </div>
        <!-- End Subtitle -->
    </div>
    <!-- End Heading -->

    <!-- Email Input -->
    <div class="fv-row mb-8">
        <input type="email" 
               placeholder="Email" 
               name="email" 
               value="{{ old('email', $request->email) }}"
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
               placeholder="Repeat Password" 
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
        <button type="submit" id="kt_new_password_submit" class="btn btn-primary">
            <!-- Indicator label -->
            <span class="indicator-label">Submit</span>
            <!-- End Indicator label -->
            <!-- Indicator progress -->
            <span class="indicator-progress">Please wait... 
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
            <!-- End Indicator progress -->
        </button>
    </div>
    <!-- End Submit Button -->
</form>
<!-- End Form -->

@push('scripts')
<script src="{{ asset('demo1/assets/js/custom/authentication/password-reset/new-password.js') }}"></script>
@endpush
@endsection
