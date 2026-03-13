@extends('layouts.master')

@section('title', 'My Profile')

@section('content')
<div style="background:#f4f6fb; min-height:calc(100vh - 70px);">

    {{-- Hero --}}
    <div style="background:linear-gradient(135deg,#0f6fad 0%,#3ab5d4 60%,#2dd4bf 100%); padding:2rem 0;">
        <div class="container">
            <h1 class="text-white fw-700 mb-1" style="font-size:1.4rem; font-weight:700;">My Profile</h1>
            <p class="text-white mb-0" style="opacity:.85; font-size:.9rem;">Manage your account information and security settings.</p>
        </div>
    </div>

    {{-- Content --}}
    <div class="container py-4">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-8">

                {{-- Profile Information --}}
                <div class="card border-0 rounded-3 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h5 class="mb-0 fw-semibold" style="font-size:.95rem;">Profile Information</h5>
                        <p class="text-muted mb-0" style="font-size:.82rem;">Update your account's name and email address.</p>
                    </div>
                    <div class="card-body p-4">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                {{-- Update Password --}}
                <div class="card border-0 rounded-3 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h5 class="mb-0 fw-semibold" style="font-size:.95rem;">Update Password</h5>
                        <p class="text-muted mb-0" style="font-size:.82rem;">Ensure your account is using a long, random password to stay secure.</p>
                    </div>
                    <div class="card-body p-4">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                {{-- Delete Account --}}
                <div class="card border-0 rounded-3 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h5 class="mb-0 fw-semibold text-danger" style="font-size:.95rem;">Delete Account</h5>
                        <p class="text-muted mb-0" style="font-size:.82rem;">Once deleted, all your data will be permanently removed.</p>
                    </div>
                    <div class="card-body p-4">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
