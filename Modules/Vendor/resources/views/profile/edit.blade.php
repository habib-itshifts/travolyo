<x-vendor::layouts.master>
    <x-slot name="title">My Profile</x-slot>

    {{-- ── Page Header ── --}}
    <div class="mb-4">
        <h1 class="fw-bold text-dark mb-1" style="font-size:1.2rem;">My Profile</h1>
        <p class="text-muted small mb-0">Manage your personal information, business details, and account settings.</p>
    </div>

    {{-- ── Flash Messages ── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Profile Header Card ── --}}
    <div class="card border rounded-4 shadow-sm mb-4" style="border-color:rgba(0,0,0,0.06)!important;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                     style="width:72px;height:72px;font-size:1.6rem;background:linear-gradient(135deg,#14532d,#16a34a);">
                    {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                </div>
                <div>
                    <h5 class="fw-bold mb-0">{{ $user->name }}</h5>
                    <p class="text-muted small mb-1">{{ $user->email }}</p>
                    <div class="d-flex gap-2">
                        <span class="badge rounded-pill" style="background:rgba(22,163,74,0.12);color:#15803d;font-size:11px;">Vendor</span>
                        @if($user->vendor_status === \App\Enums\VendorStatusEnum::Verified)
                            <span class="badge rounded-pill" style="background:rgba(22,163,74,0.12);color:#15803d;font-size:11px;">✓ Verified</span>
                        @elseif($user->vendor_status === \App\Enums\VendorStatusEnum::DocsSubmitted)
                            <span class="badge rounded-pill" style="background:rgba(99,102,241,0.12);color:#4f46e5;font-size:11px;">Documents Under Review</span>
                        @else
                            <span class="badge rounded-pill" style="background:rgba(234,179,8,0.12);color:#a16207;font-size:11px;">Pending Verification</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tabs ── --}}
    <ul class="nav nav-tabs border-bottom mb-4" id="profileTabs">
        <li class="nav-item">
            <a class="nav-link active fw-semibold" href="#personal" data-bs-toggle="tab" style="font-size:13px;">Personal Info</a>
        </li>
        <li class="nav-item">
            <a class="nav-link fw-semibold" href="#contact" data-bs-toggle="tab" style="font-size:13px;">Contact & Address</a>
        </li>
        <li class="nav-item">
            <a class="nav-link fw-semibold" href="#business" data-bs-toggle="tab" style="font-size:13px;">Business</a>
        </li>
        <li class="nav-item">
            <a class="nav-link fw-semibold" href="#security" data-bs-toggle="tab" style="font-size:13px;">Security</a>
        </li>
    </ul>

    <div class="tab-content">

        {{-- ── Tab: Personal Info ── --}}
        <div class="tab-pane fade show active" id="personal">
            <div class="card border rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('vendor.profile.update') }}">
                        @csrf @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control rounded-3 @error('first_name') is-invalid @enderror"
                                       value="{{ old('first_name', $user->first_name) }}">
                                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Last Name</label>
                                <input type="text" name="last_name" class="form-control rounded-3 @error('last_name') is-invalid @enderror"
                                       value="{{ old('last_name', $user->last_name) }}">
                                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-start-3">@</span>
                                    <input type="text" name="username" class="form-control rounded-end-3 @error('username') is-invalid @enderror"
                                           value="{{ old('username', $user->username) }}">
                                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Email</label>
                                <input type="email" class="form-control rounded-3 bg-light" value="{{ $user->email }}" disabled>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Gender</label>
                                <select name="gender" class="form-select rounded-3">
                                    <option value="">— Select —</option>
                                    <option value="male"   {{ old('gender', $user->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other"  {{ old('gender', $user->gender) === 'other'  ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Birthday</label>
                                <input type="date" name="birthday" class="form-control rounded-3"
                                       value="{{ old('birthday', $user->birthday?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Nationality</label>
                                <input type="text" name="nationality" class="form-control rounded-3"
                                       value="{{ old('nationality', $user->nationality) }}" placeholder="e.g. Pakistani">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-sm fw-semibold text-white px-4"
                                    style="background:linear-gradient(135deg,#15803d,#16a34a);border:none;border-radius:8px;">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Tab: Contact & Address ── --}}
        <div class="tab-pane fade" id="contact">
            <div class="card border rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('vendor.profile.update') }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="first_name" value="{{ $user->first_name }}">
                        <input type="hidden" name="last_name"  value="{{ $user->last_name }}">

                        <h6 class="fw-bold text-muted small text-uppercase mb-3 mt-1">Contact</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">Country Code</label>
                                <input type="text" name="phone_country_code" class="form-control rounded-3"
                                       value="{{ old('phone_country_code', $user->phone_country_code) }}" placeholder="+92">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold small">Phone</label>
                                <input type="text" name="phone" class="form-control rounded-3"
                                       value="{{ old('phone', $user->phone) }}" placeholder="3001234567">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">WhatsApp Number</label>
                                <input type="text" name="whatsapp_number" class="form-control rounded-3"
                                       value="{{ old('whatsapp_number', $user->whatsapp_number) }}">
                            </div>
                        </div>

                        <h6 class="fw-bold text-muted small text-uppercase mb-3">Address</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Address Line 1</label>
                                <input type="text" name="address_line_1" class="form-control rounded-3"
                                       value="{{ old('address_line_1', $user->address_line_1) }}" placeholder="Street address">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Address Line 2</label>
                                <input type="text" name="address_line_2" class="form-control rounded-3"
                                       value="{{ old('address_line_2', $user->address_line_2) }}" placeholder="Apartment, suite, floor">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">City</label>
                                <input type="text" name="city" class="form-control rounded-3"
                                       value="{{ old('city', $user->city) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">State / Province</label>
                                <input type="text" name="state" class="form-control rounded-3"
                                       value="{{ old('state', $user->state) }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold small">ZIP / Postal</label>
                                <input type="text" name="zip_code" class="form-control rounded-3"
                                       value="{{ old('zip_code', $user->zip_code) }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold small">Country</label>
                                <input type="text" name="country" class="form-control rounded-3"
                                       value="{{ old('country', $user->country) }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-sm fw-semibold text-white px-4"
                                    style="background:linear-gradient(135deg,#15803d,#16a34a);border:none;border-radius:8px;">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Tab: Business ── --}}
        <div class="tab-pane fade" id="business">
            <div class="card border rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4" style="max-width:600px;">
                    <h6 class="fw-bold mb-1">Business Information</h6>
                    <p class="text-muted small mb-4">This information may be displayed on your public vendor profile.</p>

                    <form method="POST" action="{{ route('vendor.profile.update') }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="first_name" value="{{ $user->first_name }}">
                        <input type="hidden" name="last_name"  value="{{ $user->last_name }}">

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Business Name</label>
                            <input type="text" name="business_name" class="form-control rounded-3 @error('business_name') is-invalid @enderror"
                                   value="{{ old('business_name', $user->business_name) }}" placeholder="Registered business or brand name">
                            @error('business_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Tax / VAT Number</label>
                            <input type="text" name="tax_number" class="form-control rounded-3 @error('tax_number') is-invalid @enderror"
                                   value="{{ old('tax_number', $user->tax_number) }}" placeholder="VAT / GST / NTN registration number">
                            @error('tax_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-sm fw-semibold text-white px-4"
                                style="background:linear-gradient(135deg,#15803d,#16a34a);border:none;border-radius:8px;">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Tab: Security ── --}}
        <div class="tab-pane fade" id="security">
            <div class="card border rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4" style="max-width:520px;">
                    <h6 class="fw-bold mb-1">Change Password</h6>
                    <p class="text-muted small mb-4">Use a strong password you don't use elsewhere.</p>

                    <form method="POST" action="{{ route('vendor.profile.password') }}">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Current Password</label>
                            <input type="password" name="current_password"
                                   class="form-control rounded-3 @error('current_password') is-invalid @enderror">
                            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">New Password</label>
                            <input type="password" name="password"
                                   class="form-control rounded-3 @error('password') is-invalid @enderror">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3">
                        </div>

                        <button type="submit" class="btn btn-sm fw-semibold text-white px-4"
                                style="background:linear-gradient(135deg,#15803d,#16a34a);border:none;border-radius:8px;">
                            Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>{{-- /tab-content --}}

    @push('scripts')
    <script>
        const activeTab = sessionStorage.getItem('profileTab');
        if (activeTab) {
            const tab = document.querySelector(`[href="${activeTab}"]`);
            if (tab) new bootstrap.Tab(tab).show();
        }
        document.querySelectorAll('#profileTabs .nav-link').forEach(el => {
            el.addEventListener('shown.bs.tab', e => sessionStorage.setItem('profileTab', e.target.getAttribute('href')));
        });
    </script>
    @endpush

</x-vendor::layouts.master>
