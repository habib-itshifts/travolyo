@php
    $isEdit  = isset($hotel);
    $action  = $isEdit ? route('admin.hotels.update', $hotel->id) : route('admin.hotels.store');
    $method  = $isEdit ? 'PUT' : 'POST';

    $paymentOptions  = ['cash' => 'Cash', 'card' => 'Credit/Debit Card', 'bank_transfer' => 'Bank Transfer', 'crypto' => 'Crypto'];
    $languageOptions = ['en' => 'English', 'ar' => 'Arabic', 'fr' => 'French', 'de' => 'German', 'zh' => 'Chinese', 'ru' => 'Russian', 'es' => 'Spanish', 'tr' => 'Turkish'];
    $countries = ['AE' => 'UAE', 'SA' => 'Saudi Arabia', 'QA' => 'Qatar', 'KW' => 'Kuwait', 'BH' => 'Bahrain', 'OM' => 'Oman', 'EG' => 'Egypt', 'GB' => 'United Kingdom', 'US' => 'United States', 'FR' => 'France', 'DE' => 'Germany', 'TR' => 'Turkey', 'TH' => 'Thailand', 'MY' => 'Malaysia', 'SG' => 'Singapore', 'IN' => 'India', 'PK' => 'Pakistan', 'MA' => 'Morocco', 'TN' => 'Tunisia', 'JO' => 'Jordan', 'LB' => 'Lebanon'];

    $amenitiesByCategory = $amenities->groupBy('category');
    $servicesByCategory  = $services->groupBy('category');

    $selectedAmenities = $isEdit ? $hotel->amenities->pluck('id')->toArray() : old('amenity_ids', []);
    $selectedServices  = $isEdit ? $hotel->services->pluck('id')->toArray() : old('service_ids', []);
@endphp

<form method="POST" action="{{ $action }}" id="hotelForm">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="row g-4">

        {{-- LEFT: Main Form --}}
        <div class="col-lg-8">

            {{-- Nav Tabs --}}
            <ul class="nav nav-tabs border-0 mb-0" id="hotelTabs" role="tablist"
                style="border-bottom: 2px solid #dee2e6 !important;">
                @foreach (['basic' => 'Basic Info', 'location' => 'Location', 'contact' => 'Contact & Policies', 'amenities' => 'Amenities', 'services' => 'Services'] as $tab => $label)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }} border-0 pb-2"
                                id="{{ $tab }}-tab" data-bs-toggle="tab"
                                data-bs-target="#tab-{{ $tab }}" type="button" role="tab"
                                style="font-size:13px;font-weight:500;">
                            {{ $label }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content bg-white border border-top-0 rounded-bottom-3 rounded-end-3 p-4" id="hotelTabContent">

                {{-- ── TAB 1: BASIC INFO ── --}}
                <div class="tab-pane fade show active" id="tab-basic" role="tabpanel">
                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Hotel Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="hotelName"
                                   value="{{ old('name', $hotel->name ?? '') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g. Arabian Courtyard Hotel & Spa">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold" style="font-size:13px;">Slug</label>
                            <input type="text" name="slug" id="hotelSlug"
                                   value="{{ old('slug', $hotel->slug ?? '') }}"
                                   class="form-control @error('slug') is-invalid @enderror"
                                   placeholder="auto-generated from name">
                            <div class="form-text">Leave empty to auto-generate.</div>
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:13px;">Star Rating</label>
                            <select name="star_rating" class="form-select @error('star_rating') is-invalid @enderror">
                                <option value="">— Select —</option>
                                @for ($s = 1; $s <= 5; $s++)
                                    <option value="{{ $s }}" {{ old('star_rating', $hotel->star_rating ?? '') == $s ? 'selected' : '' }}>
                                        {{ $s }} Star{{ $s > 1 ? 's' : '' }}
                                    </option>
                                @endfor
                            </select>
                            @error('star_rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Short Description</label>
                            <input type="text" name="short_description"
                                   value="{{ old('short_description', $hotel->short_description ?? '') }}"
                                   class="form-control @error('short_description') is-invalid @enderror"
                                   placeholder="One-line summary shown in search results (max 500 chars)" maxlength="500">
                            @error('short_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Full Description</label>
                            <textarea name="description" rows="6"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Detailed hotel description...">{{ old('description', $hotel->description ?? '') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                    </div>
                </div>

                {{-- ── TAB 2: LOCATION ── --}}
                <div class="tab-pane fade" id="tab-location" role="tabpanel">
                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Street Address <span class="text-danger">*</span></label>
                            <input type="text" name="address"
                                   value="{{ old('address', $hotel->address ?? '') }}"
                                   class="form-control @error('address') is-invalid @enderror"
                                   placeholder="e.g. Al Fahidi Street, Bur Dubai">
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">City <span class="text-danger">*</span></label>
                            <input type="text" name="city"
                                   value="{{ old('city', $hotel->city ?? '') }}"
                                   class="form-control @error('city') is-invalid @enderror"
                                   placeholder="e.g. Dubai">
                            @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">State / Region</label>
                            <input type="text" name="state"
                                   value="{{ old('state', $hotel->state ?? '') }}"
                                   class="form-control" placeholder="e.g. Dubai">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Country <span class="text-danger">*</span></label>
                            <select name="country" class="form-select @error('country') is-invalid @enderror">
                                <option value="">— Select Country —</option>
                                @foreach ($countries as $code => $name)
                                    <option value="{{ $code }}" {{ old('country', $hotel->country ?? '') === $code ? 'selected' : '' }}>
                                        {{ $name }} ({{ $code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Postal / ZIP Code</label>
                            <input type="text" name="postal_code"
                                   value="{{ old('postal_code', $hotel->postal_code ?? '') }}"
                                   class="form-control" placeholder="e.g. 12345">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Latitude</label>
                            <input type="number" name="latitude" step="any"
                                   value="{{ old('latitude', $hotel->latitude ?? '') }}"
                                   class="form-control @error('latitude') is-invalid @enderror"
                                   placeholder="e.g. 25.2048">
                            @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Longitude</label>
                            <input type="number" name="longitude" step="any"
                                   value="{{ old('longitude', $hotel->longitude ?? '') }}"
                                   class="form-control @error('longitude') is-invalid @enderror"
                                   placeholder="e.g. 55.2708">
                            @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                    </div>
                </div>

                {{-- ── TAB 3: CONTACT & POLICIES ── --}}
                <div class="tab-pane fade" id="tab-contact" role="tabpanel">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Hotel Email</label>
                            <input type="email" name="email"
                                   value="{{ old('email', $hotel->email ?? '') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="info@hotel.com">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Phone</label>
                            <input type="text" name="phone"
                                   value="{{ old('phone', $hotel->phone ?? '') }}"
                                   class="form-control" placeholder="+971 4 000 0000">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Website</label>
                            <input type="url" name="website"
                                   value="{{ old('website', $hotel->website ?? '') }}"
                                   class="form-control @error('website') is-invalid @enderror"
                                   placeholder="https://www.hotel.com">
                            @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Check-in Time</label>
                            <input type="time" name="check_in_time"
                                   value="{{ old('check_in_time', $hotel->check_in_time ?? '14:00') }}"
                                   class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Check-out Time</label>
                            <input type="time" name="check_out_time"
                                   value="{{ old('check_out_time', $hotel->check_out_time ?? '11:00') }}"
                                   class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold d-block mb-2" style="font-size:13px;">Payment Methods</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach ($paymentOptions as $val => $label)
                                    @php $checked = in_array($val, old('payment_methods', $hotel->payment_methods ?? [])); @endphp
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="payment_methods[]" value="{{ $val }}"
                                               id="pm_{{ $val }}" {{ $checked ? 'checked' : '' }}>
                                        <label class="form-check-label" for="pm_{{ $val }}" style="font-size:13px;">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold d-block mb-2" style="font-size:13px;">Languages Spoken</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach ($languageOptions as $val => $label)
                                    @php $checked = in_array($val, old('languages_spoken', $hotel->languages_spoken ?? [])); @endphp
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="languages_spoken[]" value="{{ $val }}"
                                               id="lang_{{ $val }}" {{ $checked ? 'checked' : '' }}>
                                        <label class="form-check-label" for="lang_{{ $val }}" style="font-size:13px;">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ── TAB 4: AMENITIES ── --}}
                <div class="tab-pane fade" id="tab-amenities" role="tabpanel">
                    @foreach ($amenitiesByCategory as $category => $items)
                        <p class="text-uppercase fw-bold mb-2 mt-3" style="font-size:11px;color:var(--clr-primary);letter-spacing:.05em;">
                            {{ ucwords(str_replace('_', ' ', $category)) }}
                        </p>
                        <div class="row g-2 mb-2">
                            @foreach ($items as $amenity)
                                <div class="col-md-4 col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="amenity_ids[]" value="{{ $amenity->id }}"
                                               id="am_{{ $amenity->id }}"
                                               {{ in_array($amenity->id, $selectedAmenities) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="am_{{ $amenity->id }}" style="font-size:13px;">
                                            {{ $amenity->name }}
                                            <span class="badge bg-light text-muted border ms-1" style="font-size:10px;">{{ $amenity->applies_to }}</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                {{-- ── TAB 5: SERVICES ── --}}
                <div class="tab-pane fade" id="tab-services" role="tabpanel">
                    @foreach ($servicesByCategory as $category => $items)
                        <p class="text-uppercase fw-bold mb-2 mt-3" style="font-size:11px;color:var(--clr-primary);letter-spacing:.05em;">
                            {{ ucwords(str_replace('_', ' ', $category)) }}
                        </p>
                        <div class="row g-2 mb-2">
                            @foreach ($items as $service)
                                <div class="col-md-4 col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="service_ids[]" value="{{ $service->id }}"
                                               id="sv_{{ $service->id }}"
                                               {{ in_array($service->id, $selectedServices) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sv_{{ $service->id }}" style="font-size:13px;">
                                            {{ $service->name }}
                                            @if ($service->is_chargeable)
                                                <span class="badge bg-warning-subtle text-warning border ms-1" style="font-size:10px;">Paid</span>
                                            @else
                                                <span class="badge bg-success-subtle text-success border ms-1" style="font-size:10px;">Free</span>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

            </div>{{-- end tab-content --}}
        </div>{{-- end col-lg-8 --}}

        {{-- RIGHT: Publish Panel --}}
        <div class="col-lg-4">

            {{-- Publish Box --}}
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-header bg-white border-bottom fw-semibold py-3" style="font-size:13px;">
                    Publish Settings
                </div>
                <div class="card-body">

                    <label class="form-label fw-semibold" style="font-size:13px;">Status <span class="text-danger">*</span></label>
                    <div class="d-flex flex-column gap-2 mb-3">
                        @foreach (['active' => 'Active', 'draft' => 'Draft', 'inactive' => 'Inactive', 'suspended' => 'Suspended'] as $val => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" value="{{ $val }}"
                                       id="status_{{ $val }}"
                                       {{ old('status', $hotel->status ?? 'active') === $val ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_{{ $val }}" style="font-size:13px;">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>

                    <hr class="my-3">

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                               id="isFeatured"
                               {{ old('is_featured', $hotel->is_featured ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="isFeatured" style="font-size:13px;">
                            Featured Hotel
                        </label>
                        <div class="form-text">Show in featured/home section.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Sort Order</label>
                        <input type="number" name="sort_order" min="0"
                               value="{{ old('sort_order', $hotel->sort_order ?? 0) }}"
                               class="form-control form-control-sm">
                        <div class="form-text">Lower = appears first.</div>
                    </div>

                </div>
                <div class="card-footer bg-white border-top d-flex gap-2">
                    <button type="submit" class="btn btn-sm flex-fill text-white fw-semibold"
                            style="background:var(--clr-primary);border-radius:var(--radius-btn);">
                        {{ $isEdit ? 'Save Changes' : 'Create Hotel' }}
                    </button>
                    <a href="{{ route('admin.hotels.index') }}" class="btn btn-sm btn-outline-secondary">
                        Cancel
                    </a>
                </div>
            </div>

            {{-- Validation errors summary --}}
            @if ($errors->any())
                <div class="alert alert-danger rounded-3" style="font-size:13px;">
                    <p class="fw-semibold mb-1">Please fix the following errors:</p>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>{{-- end col-lg-4 --}}
    </div>{{-- end row --}}
</form>

@push('scripts')
<script>
    // Auto-generate slug from name
    const nameInput = document.getElementById('hotelName');
    const slugInput = document.getElementById('hotelSlug');

    nameInput?.addEventListener('input', function () {
        if (!slugInput.dataset.manual) {
            slugInput.value = this.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }
    });

    slugInput?.addEventListener('input', function () {
        this.dataset.manual = 'true';
    });
</script>
@endpush
