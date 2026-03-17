<x-admin::layouts.master>
    <x-slot name="title">Edit Vendor — {{ $vendor->name }}</x-slot>

    {{-- ── Breadcrumb ── --}}
    <nav class="mb-4" style="font-size:13px;">
        <a href="{{ route('admin.vendors.index') }}" class="text-muted text-decoration-none">Vendors</a>
        <span class="text-muted mx-1">/</span>
        <a href="{{ route('admin.vendors.show', $vendor) }}" class="text-muted text-decoration-none">{{ $vendor->name }}</a>
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

    <form method="POST" action="{{ route('admin.vendors.update', $vendor) }}">
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
                                <input type="text" name="first_name" value="{{ old('first_name', $vendor->first_name) }}"
                                       class="form-control form-control-sm rounded-3 @error('first_name') is-invalid @enderror"
                                       style="font-size:13px;">
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name', $vendor->last_name) }}"
                                       class="form-control form-control-sm rounded-3 @error('last_name') is-invalid @enderror"
                                       style="font-size:13px;">
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Email</label>
                                <input type="email" name="email" value="{{ old('email', $vendor->email) }}"
                                       class="form-control form-control-sm rounded-3 @error('email') is-invalid @enderror"
                                       style="font-size:13px;">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $vendor->phone) }}"
                                       class="form-control form-control-sm rounded-3 @error('phone') is-invalid @enderror"
                                       style="font-size:13px;">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Business Info ── --}}
            <div class="col-lg-6">
                <div class="card border rounded-4 shadow-sm h-100" style="border-color:rgba(0,0,0,0.06)!important;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-4" style="font-size:13px;letter-spacing:.03em;">Business Information</h6>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Business Name</label>
                                <input type="text" name="business_name" value="{{ old('business_name', $vendor->business_name) }}"
                                       class="form-control form-control-sm rounded-3 @error('business_name') is-invalid @enderror"
                                       style="font-size:13px;">
                                @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Tax / VAT Number</label>
                                <input type="text" name="tax_number" value="{{ old('tax_number', $vendor->tax_number) }}"
                                       class="form-control form-control-sm rounded-3 @error('tax_number') is-invalid @enderror"
                                       style="font-size:13px;">
                                @error('tax_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Vendor Status</label>
                                <select name="vendor_status"
                                        class="form-select form-select-sm rounded-3 @error('vendor_status') is-invalid @enderror"
                                        style="font-size:13px;">
                                    @foreach(\App\Enums\VendorStatusEnum::cases() as $status)
                                        <option value="{{ $status->value }}"
                                            {{ old('vendor_status', $vendor->vendor_status?->value) === $status->value ? 'selected' : '' }}>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vendor_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Commission ── --}}
            <div class="col-12">
                <div class="card border rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <h6 class="fw-bold text-dark mb-0" style="font-size:13px;letter-spacing:.03em;">Commission Settings</h6>
                            <span class="badge rounded-pill fw-semibold"
                                  style="font-size:10px;background:rgba(234,179,8,0.12);color:#a16207;">Admin Only</span>
                        </div>

                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Commission Type</label>
                                <select name="vendor_commission_type" id="commissionType"
                                        class="form-select form-select-sm rounded-3 @error('vendor_commission_type') is-invalid @enderror"
                                        style="font-size:13px;">
                                    <option value="">— None —</option>
                                    <option value="percent"
                                        {{ old('vendor_commission_type', $vendor->vendor_commission_type) === 'percent' ? 'selected' : '' }}>
                                        Percentage (%)
                                    </option>
                                    <option value="fixed"
                                        {{ old('vendor_commission_type', $vendor->vendor_commission_type) === 'fixed' ? 'selected' : '' }}>
                                        Fixed Amount
                                    </option>
                                </select>
                                @error('vendor_commission_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">
                                    Commission Value
                                    <span id="commissionUnit" class="text-muted"
                                          style="font-size:11px;font-weight:400;">
                                        @if(old('vendor_commission_type', $vendor->vendor_commission_type) === 'percent')
                                            (%)
                                        @elseif(old('vendor_commission_type', $vendor->vendor_commission_type) === 'fixed')
                                            ({{ $vendor->currency_preference ?? 'USD' }})
                                        @endif
                                    </span>
                                </label>
                                <input type="number" name="vendor_commission_amount" id="commissionAmount"
                                       value="{{ old('vendor_commission_amount', $vendor->vendor_commission_amount) }}"
                                       step="0.01" min="0"
                                       class="form-control form-control-sm rounded-3 @error('vendor_commission_amount') is-invalid @enderror"
                                       style="font-size:13px;"
                                       placeholder="e.g. 10">
                                @error('vendor_commission_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <div class="rounded-3 p-3" style="background:rgba(234,179,8,0.06);border:1px dashed rgba(234,179,8,0.4);">
                                    <p class="mb-1 fw-semibold text-dark" style="font-size:12px;">Current Setting</p>
                                    <p class="mb-0 text-muted" style="font-size:13px;" id="commissionPreview">
                                        @if($vendor->vendor_commission_type === 'percent')
                                            <strong>{{ $vendor->vendor_commission_amount }}%</strong> per booking
                                        @elseif($vendor->vendor_commission_type === 'fixed')
                                            <strong>{{ $vendor->currency_preference ?? 'USD' }} {{ number_format($vendor->vendor_commission_amount, 2) }}</strong> per booking
                                        @else
                                            No commission set
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <p class="text-muted mt-3 mb-0" style="font-size:11px;">
                            This commission is deducted from each booking's total before releasing payment to the vendor.
                            <strong>Percent</strong> = % of booking total. <strong>Fixed</strong> = flat amount per booking.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── Address ── --}}
            <div class="col-12">
                <div class="card border rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-4" style="font-size:13px;letter-spacing:.03em;">Address</h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Address Line 1</label>
                                <input type="text" name="address_line_1" value="{{ old('address_line_1', $vendor->address_line_1) }}"
                                       class="form-control form-control-sm rounded-3" style="font-size:13px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Address Line 2</label>
                                <input type="text" name="address_line_2" value="{{ old('address_line_2', $vendor->address_line_2) }}"
                                       class="form-control form-control-sm rounded-3" style="font-size:13px;">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">City</label>
                                <input type="text" name="city" value="{{ old('city', $vendor->city) }}"
                                       class="form-control form-control-sm rounded-3" style="font-size:13px;">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">State</label>
                                <input type="text" name="state" value="{{ old('state', $vendor->state) }}"
                                       class="form-control form-control-sm rounded-3" style="font-size:13px;">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Country</label>
                                <input type="text" name="country" value="{{ old('country', $vendor->country) }}"
                                       class="form-control form-control-sm rounded-3" style="font-size:13px;">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-muted" style="font-size:12px;">Zip Code</label>
                                <input type="text" name="zip_code" value="{{ old('zip_code', $vendor->zip_code) }}"
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
                    style="background:linear-gradient(135deg,#0f1f38,#1a3a5c);border:none;border-radius:8px;padding:8px 24px;font-size:13px;">
                Save Changes
            </button>
            <a href="{{ route('admin.vendors.show', $vendor) }}"
               class="btn btn-sm btn-outline-secondary fw-semibold rounded-3"
               style="font-size:13px;padding:8px 20px;">
                Cancel
            </a>
        </div>
    </form>

@push('scripts')
<script>
    const typeSelect  = document.getElementById('commissionType');
    const amountInput = document.getElementById('commissionAmount');
    const unitLabel   = document.getElementById('commissionUnit');
    const preview     = document.getElementById('commissionPreview');
    const currency    = '{{ $vendor->currency_preference ?? "USD" }}';

    function updatePreview() {
        const type   = typeSelect.value;
        const amount = parseFloat(amountInput.value) || 0;

        if (type === 'percent') {
            unitLabel.textContent  = '(%)';
            preview.innerHTML = amount > 0
                ? `<strong>${amount}%</strong> per booking`
                : 'Enter a percentage value';
        } else if (type === 'fixed') {
            unitLabel.textContent  = `(${currency})`;
            preview.innerHTML = amount > 0
                ? `<strong>${currency} ${amount.toFixed(2)}</strong> per booking`
                : 'Enter a fixed amount';
        } else {
            unitLabel.textContent  = '';
            preview.innerHTML = 'No commission set';
        }
    }

    typeSelect.addEventListener('change', updatePreview);
    amountInput.addEventListener('input', updatePreview);
</script>
@endpush

</x-admin::layouts.master>
