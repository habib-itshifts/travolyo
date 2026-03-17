<x-admin::layouts.master>
    <x-slot name="title">Edit Customer — {{ $customer->name }}</x-slot>

    {{-- ── Breadcrumb ── --}}
    <nav class="mb-4" style="font-size:13px;">
        <a href="{{ route('admin.customers.index') }}" class="text-muted text-decoration-none">Customers</a>
        <span class="text-muted mx-1">/</span>
        <a href="{{ route('admin.customers.show', $customer) }}" class="text-muted text-decoration-none">{{ $customer->name }}</a>
        <span class="text-muted mx-1">/</span>
        <span class="text-dark fw-semibold">Edit</span>
    </nav>

    {{-- ── Flash ── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.customers.update', $customer) }}">
        @csrf @method('PUT')

        <div class="row g-4">

            {{-- ── Personal Info ── --}}
            <div class="col-lg-6">
                <div class="card border rounded-4 shadow-sm h-100" style="border-color:rgba(0,0,0,0.06)!important;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-4" style="font-size:13px;letter-spacing:.03em;">Personal Information</h6>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name', $customer->first_name) }}"
                                       class="form-control form-control-sm rounded-3 @error('first_name') is-invalid @enderror"
                                       style="font-size:13px;">
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name', $customer->last_name) }}"
                                       class="form-control form-control-sm rounded-3 @error('last_name') is-invalid @enderror"
                                       style="font-size:13px;">
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Email</label>
                                <input type="email" name="email" value="{{ old('email', $customer->email) }}"
                                       class="form-control form-control-sm rounded-3 @error('email') is-invalid @enderror"
                                       style="font-size:13px;">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                                       class="form-control form-control-sm rounded-3 @error('phone') is-invalid @enderror"
                                       style="font-size:13px;">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Address ── --}}
            <div class="col-lg-6">
                <div class="card border rounded-4 shadow-sm h-100" style="border-color:rgba(0,0,0,0.06)!important;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-4" style="font-size:13px;letter-spacing:.03em;">Address</h6>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Address Line 1</label>
                                <input type="text" name="address_line_1" value="{{ old('address_line_1', $customer->address_line_1) }}"
                                       class="form-control form-control-sm rounded-3" style="font-size:13px;">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Address Line 2</label>
                                <input type="text" name="address_line_2" value="{{ old('address_line_2', $customer->address_line_2) }}"
                                       class="form-control form-control-sm rounded-3" style="font-size:13px;">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">City</label>
                                <input type="text" name="city" value="{{ old('city', $customer->city) }}"
                                       class="form-control form-control-sm rounded-3" style="font-size:13px;">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">State</label>
                                <input type="text" name="state" value="{{ old('state', $customer->state) }}"
                                       class="form-control form-control-sm rounded-3" style="font-size:13px;">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Country</label>
                                <input type="text" name="country" value="{{ old('country', $customer->country) }}"
                                       class="form-control form-control-sm rounded-3" style="font-size:13px;">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Zip Code</label>
                                <input type="text" name="zip_code" value="{{ old('zip_code', $customer->zip_code) }}"
                                       class="form-control form-control-sm rounded-3" style="font-size:13px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Actions ── --}}
        <div class="d-flex gap-2 mt-4">
            <button type="submit"
                    class="btn btn-sm fw-semibold text-white"
                    style="background:linear-gradient(135deg,#15803d,#16a34a);border:none;border-radius:8px;padding:8px 24px;font-size:13px;">
                Save Changes
            </button>
            <a href="{{ route('admin.customers.show', $customer) }}"
               class="btn btn-sm btn-outline-secondary fw-semibold rounded-3"
               style="font-size:13px;padding:8px 20px;">
                Cancel
            </a>
        </div>
    </form>

</x-admin::layouts.master>
