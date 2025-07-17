@extends('layouts.auth')

@section('title', 'Confirm Password')

@section('content')
<!-- Form -->
<form class="form w-100" method="POST" action="{{ route('password.confirm') }}" novalidate="novalidate" id="kt_password_confirm_form">
    @csrf
    
    <!-- Heading -->
    <div class="text-center mb-10">
        <!-- Title -->
        <h1 class="text-gray-900 fw-bolder mb-3">Password Confirmation</h1>
        <!-- End Title -->
        <!-- Subtitle -->
        <div class="text-gray-500 fw-semibold fs-6">
            This is a secure area. Please confirm your password before continuing.
        </div>
        <!-- End Subtitle -->
    </div>
    <!-- End Heading -->

    <!-- Password Input -->
    <div class="fv-row mb-8">
        <input type="password" 
               placeholder="Password" 
               name="password" 
               autocomplete="current-password" 
               class="form-control bg-transparent @error('password') is-invalid @enderror" 
               required 
               autofocus />
        @error('password')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>
    <!-- End Password Input -->

    <!-- Submit Button -->
    <div class="d-grid mb-10">
        <button type="submit" id="kt_password_confirm_submit" class="btn btn-primary">
            <!-- Indicator label -->
            <span class="indicator-label">Confirm</span>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('kt_password_confirm_form');
    const submitButton = document.getElementById('kt_password_confirm_submit');
    
    if (form && submitButton) {
        form.addEventListener('submit', function() {
            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;
        });
    }
});
</script>
@endpush
@endsection
