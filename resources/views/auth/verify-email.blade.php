@extends('layouts.auth')

@section('title', 'Verify Email')

@section('content')
<!-- Form -->
<div class="w-100">
    <!-- Heading -->
    <div class="text-center mb-10">
        <!-- Title -->
        <h1 class="text-gray-900 fw-bolder mb-3">Verify Your Email</h1>
        <!-- End Title -->
        <!-- Subtitle -->
        <div class="text-gray-500 fw-semibold fs-6">
            Workshop Management System
        </div>
        <!-- End Subtitle -->
    </div>
    <!-- End Heading -->

    <!-- Description -->
    <div class="mb-10 bg-light-info px-8 py-5 rounded">
        <div class="text-info">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </div>
    </div>
    <!-- End Description -->

    <!-- Session Status -->
    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-8" role="alert">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <!-- Actions -->
    <div class="d-flex flex-wrap justify-content-between pb-lg-0">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary me-4">
                Resend Verification Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-light">
                Log Out
            </button>
        </form>
    </div>
    <!-- End Actions -->
</div>
<!-- End Form -->
@endsection
