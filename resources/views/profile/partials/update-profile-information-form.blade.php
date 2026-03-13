<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div class="mb-3">
        <label for="name" class="form-label fw-semibold" style="font-size:.875rem;">Name</label>
        <input id="name" name="name" type="text"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $user->name) }}"
               required autofocus autocomplete="name">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label fw-semibold" style="font-size:.875rem;">Email</label>
        <input id="email" name="email" type="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $user->email) }}"
               required autocomplete="username">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2">
                <p class="text-muted small mb-1">{{ __('Your email address is unverified.') }}</p>
                <button form="send-verification" class="btn btn-sm btn-link p-0 text-decoration-underline" style="font-size:.82rem;">
                    {{ __('Click here to re-send the verification email.') }}
                </button>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-1 text-success small mb-0">{{ __('A new verification link has been sent to your email address.') }}</p>
                @endif
            </div>
        @endif
    </div>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn text-white px-4"
                style="background:#3ab5d4;border:none;border-radius:10px;font-weight:600;">
            Save
        </button>
        @if (session('status') === 'profile-updated')
            <span class="text-success small fw-semibold">Saved!</span>
        @endif
    </div>
</form>
