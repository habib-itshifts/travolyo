@php
    $isEdit = isset($space);
    $action = $isEdit
        ? (request()->routeIs('admin.*') ? route('admin.spaces.update', $space->id) : route('vendor.spaces.update', $space->id))
        : (request()->routeIs('admin.*') ? route('admin.spaces.store') : route('vendor.spaces.store'));

    $countries = [
        'AE' => 'UAE', 'SA' => 'Saudi Arabia', 'QA' => 'Qatar', 'KW' => 'Kuwait',
        'BH' => 'Bahrain', 'OM' => 'Oman', 'EG' => 'Egypt', 'GB' => 'United Kingdom',
        'US' => 'United States', 'FR' => 'France', 'DE' => 'Germany', 'TR' => 'Turkey',
        'TH' => 'Thailand', 'MY' => 'Malaysia', 'SG' => 'Singapore', 'IN' => 'India',
        'PK' => 'Pakistan', 'MA' => 'Morocco', 'TN' => 'Tunisia', 'JO' => 'Jordan', 'LB' => 'Lebanon',
    ];

    $spaceTypes = ['apartment', 'room', 'studio', 'villa', 'house'];

    $amenitiesByCategory = $amenities->groupBy('category');
    $normalizeMultiSelect = function ($value): array {
        if (is_array($value)) {
            return array_values(array_filter($value, fn ($item) => $item !== null && $item !== ''));
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return array_values(array_filter($decoded, fn ($item) => $item !== null && $item !== ''));
            }
            $trimmed = trim($value);
            if ($trimmed === '') {
                return [];
            }
            return array_values(array_filter(array_map('trim', explode(',', $value)), fn ($item) => $item !== ''));
        }
        if ($value instanceof \Illuminate\Support\Collection) {
            return $value->filter(fn ($item) => $item !== null && $item !== '')->values()->all();
        }
        return [];
    };
    $selectedAmenities = $normalizeMultiSelect(old('amenity_ids', $isEdit ? $space->amenities->pluck('id')->all() : []));

    $featuredImageId = old('image_id', $space->image_id ?? '');
    $bannerImageId = old('banner_image_id', $space->banner_image_id ?? '');
    $galleryValue = (string) old('gallery', $space->gallery ?? '');
    $resolveMediaUrl = function ($mediaId, $fallbackPath = null) {
        $mediaId = (int) $mediaId;
        if ($mediaId > 0) {
            $media = \Modules\Admin\Models\MediaFile::find($mediaId);
            if ($media) {
                return $media->url;
            }
        }
        return $fallbackPath ? asset($fallbackPath) : null;
    };
    $featuredImage = $resolveMediaUrl($featuredImageId, $space->featured_image_url ?? null);
    $bannerImage = $resolveMediaUrl($bannerImageId, $space->banner_image_url ?? null);
    $galleryIds = collect(explode(',', $galleryValue))
        ->map(fn ($id) => (int) trim($id))
        ->filter()
        ->values();
    $galleryMedia = $galleryIds->isNotEmpty()
        ? \Modules\Admin\Models\MediaFile::whereIn('id', $galleryIds->all())->get()->keyBy('id')
        : collect();
    $galleryImages = $galleryIds->map(fn ($id) => optional($galleryMedia->get($id))->url)
        ->filter()
        ->values()
        ->all();
    if (empty($galleryImages) && $isEdit) {
        $galleryImages = collect($space->gallery_urls ?? [])->map(fn ($path) => asset($path))->all();
    }

    $houseRules = old('house_rules', $space->house_rules ?? []);
    $extraPrices = old('extra_prices', $space->extra_prices ?? []);

    if (empty($houseRules)) {
        $houseRules = [['title' => '', 'content' => '']];
    }
    if (empty($extraPrices)) {
        $extraPrices = [['name' => '', 'price' => '', 'type' => 'one_time', 'per_person' => false]];
    }
@endphp

<form method="POST" action="{{ $action }}" id="spaceForm" enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <ul class="nav nav-tabs border-0 mb-0" id="spaceTabs" role="tablist" style="border-bottom: 2px solid #dee2e6 !important;">
                @foreach ([
                    'basic' => 'Basic Info',
                    'capacity' => 'Capacity',
                    'media' => 'Media',
                    'location' => 'Location',
                    'pricing' => 'Pricing',
                    'policies' => 'Policies & Rules',
                    'amenities' => 'Amenities',
                ] as $tab => $label)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }} border-0 pb-2" id="{{ $tab }}-tab" data-bs-toggle="tab" data-bs-target="#tab-{{ $tab }}" type="button" role="tab" style="font-size:13px;font-weight:500;">
                            {{ $label }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content bg-white border border-top-0 rounded-bottom-3 rounded-end-3 p-4" id="spaceTabContent">
                {{-- Basic Info Tab --}}
                <div class="tab-pane fade show active" id="tab-basic" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Space Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="spaceName" value="{{ old('name', $space->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Luxury Downtown Apartment">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Slug</label>
                            <input type="text" name="slug" id="spaceSlug" value="{{ old('slug', $space->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror" placeholder="auto-generated from name">
                            <div class="form-text">Leave empty to auto-generate.</div>
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror">
                                <option value="">- Select Type -</option>
                                @foreach ($spaceTypes as $type)
                                    <option value="{{ $type }}" {{ old('type', $space->type ?? '') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Short Description</label>
                            <input type="text" name="short_description" value="{{ old('short_description', $space->short_description ?? '') }}" class="form-control @error('short_description') is-invalid @enderror" placeholder="One-line summary shown in search results" maxlength="500">
                            @error('short_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Full Description</label>
                            <textarea name="description" rows="6" class="form-control @error('description') is-invalid @enderror" placeholder="Detailed space description...">{{ old('description', $space->description ?? '') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Capacity Tab --}}
                <div class="tab-pane fade" id="tab-capacity" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Max Guests <span class="text-danger">*</span></label>
                            <input type="number" name="max_guests" min="1" value="{{ old('max_guests', $space->max_guests ?? 1) }}" class="form-control @error('max_guests') is-invalid @enderror" placeholder="e.g. 4">
                            @error('max_guests') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Bedrooms</label>
                            <input type="number" name="bedrooms" min="0" value="{{ old('bedrooms', $space->bedrooms ?? 1) }}" class="form-control @error('bedrooms') is-invalid @enderror" placeholder="e.g. 2">
                            @error('bedrooms') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Bathrooms</label>
                            <input type="number" name="bathrooms" min="0" value="{{ old('bathrooms', $space->bathrooms ?? 1) }}" class="form-control @error('bathrooms') is-invalid @enderror" placeholder="e.g. 1">
                            @error('bathrooms') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Beds</label>
                            <input type="number" name="beds" min="0" value="{{ old('beds', $space->beds ?? 1) }}" class="form-control @error('beds') is-invalid @enderror" placeholder="e.g. 2">
                            @error('beds') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Media Tab --}}
                <div class="tab-pane fade" id="tab-media" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Featured Image</label>
                            <p class="text-muted mb-2" style="font-size:12px;">Main featured image for your space (recommended size: 800x600px)</p>
                            <input type="hidden" name="image_id" id="featured-image-id" value="{{ $featuredImageId }}">
                            <div class="media-picker-card" data-open-media-browser data-media-target="featured" data-media-multiple="false">
                                <div class="media-picker-preview" id="featured-image-preview">
                                    @if ($featuredImage)
                                        <img src="{{ $featuredImage }}" alt="Featured Image" class="img-fluid rounded border" style="max-height: 180px;">
                                    @else
                                        <div class="media-picker-empty">
                                            <div class="media-picker-icon">&#128247;</div>
                                            <span class="btn btn-primary btn-sm">Upload image</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @error('image_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Banner Image</label>
                            <p class="text-muted mb-2" style="font-size:12px;">Upload a high-quality banner image (1920x600px recommended)</p>
                            <input type="hidden" name="banner_image_id" id="banner-image-id" value="{{ $bannerImageId }}">
                            <div class="media-picker-card" data-open-media-browser data-media-target="banner" data-media-multiple="false">
                                <div class="media-picker-preview" id="banner-image-preview">
                                    @if ($bannerImage)
                                        <img src="{{ $bannerImage }}" alt="Banner Image" class="img-fluid rounded border" style="max-height: 180px;">
                                    @else
                                        <div class="media-picker-empty">
                                            <div class="media-picker-icon">&#128247;</div>
                                            <span class="btn btn-primary btn-sm">Upload image</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @error('banner_image_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Gallery Images</label>
                            <p class="text-muted mb-2" style="font-size:12px;">Upload multiple images showcasing your space rooms, facilities, and amenities</p>
                            <div class="d-flex gap-2 mb-2">
                                <button type="button" class="btn btn-info btn-sm" data-open-media-browser data-media-target="gallery" data-media-multiple="true">
                                    Select images
                                </button>
                            </div>
                            <input type="hidden" name="gallery" id="gallery-image-ids" value="{{ $galleryValue }}">
                            <div class="d-flex flex-wrap gap-2 mt-2" id="gallery-images-preview">
                                @if (!empty($galleryImages))
                                    @foreach ($galleryImages as $image)
                                        <img src="{{ $image }}" alt="Gallery Image" class="rounded border" style="width:110px;height:90px;object-fit:cover;">
                                    @endforeach
                                @endif
                            </div>
                            @error('gallery') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Location Tab --}}
                <div class="tab-pane fade" id="tab-location" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Street Address <span class="text-danger">*</span></label>
                            <input type="text" name="address" value="{{ old('address', $space->address ?? '') }}" class="form-control @error('address') is-invalid @enderror" placeholder="e.g. Al Fahidi Street, Bur Dubai">
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" value="{{ old('city', $space->city ?? '') }}" class="form-control @error('city') is-invalid @enderror" placeholder="e.g. Dubai">
                            @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">State / Region</label>
                            <input type="text" name="state" value="{{ old('state', $space->state ?? '') }}" class="form-control" placeholder="e.g. Dubai">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Country <span class="text-danger">*</span></label>
                            <select name="country" class="form-select @error('country') is-invalid @enderror">
                                <option value="">- Select Country -</option>
                                @foreach ($countries as $code => $name)
                                    <option value="{{ $code }}" {{ old('country', $space->country ?? '') === $code ? 'selected' : '' }}>{{ $name }} ({{ $code }})</option>
                                @endforeach
                            </select>
                            @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Postal / ZIP Code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $space->postal_code ?? '') }}" class="form-control" placeholder="e.g. 12345">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Latitude</label>
                            <input type="number" name="latitude" step="any" value="{{ old('latitude', $space->latitude ?? '') }}" class="form-control @error('latitude') is-invalid @enderror" placeholder="e.g. 25.2048">
                            @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Longitude</label>
                            <input type="number" name="longitude" step="any" value="{{ old('longitude', $space->longitude ?? '') }}" class="form-control @error('longitude') is-invalid @enderror" placeholder="e.g. 55.2708">
                            @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Contact Email</label>
                            <input type="email" name="email" value="{{ old('email', $space->email ?? '') }}" class="form-control @error('email') is-invalid @enderror" placeholder="info@space.com">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $space->phone ?? '') }}" class="form-control" placeholder="+971 4 000 0000">
                        </div>
                    </div>
                </div>

                {{-- Pricing Tab --}}
                <div class="tab-pane fade" id="tab-pricing" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Price Per Night <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="price_per_night" value="{{ old('price_per_night', $space->price_per_night ?? '') }}" class="form-control @error('price_per_night') is-invalid @enderror" placeholder="e.g. 250">
                            @error('price_per_night') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Sale Price</label>
                            <input type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price', $space->sale_price ?? '') }}" class="form-control @error('sale_price') is-invalid @enderror" placeholder="e.g. 225">
                            @error('sale_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Cleaning Fee</label>
                            <input type="number" step="0.01" min="0" name="cleaning_fee" value="{{ old('cleaning_fee', $space->cleaning_fee ?? '') }}" class="form-control @error('cleaning_fee') is-invalid @enderror" placeholder="e.g. 50">
                            @error('cleaning_fee') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Service Fee</label>
                            <input type="number" step="0.01" min="0" name="service_fee" value="{{ old('service_fee', $space->service_fee ?? '') }}" class="form-control @error('service_fee') is-invalid @enderror" placeholder="e.g. 25">
                            @error('service_fee') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 pt-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="fw-semibold mb-0" style="font-size:13px;">Extra Prices</h6>
                                    <div class="form-text">Optional add-ons like breakfast, airport pickup, or extra bed.</div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-add-repeater="extra-prices">Add Extra Price</button>
                            </div>
                            <div id="extra-prices-list" class="d-grid gap-3">
                                @foreach ($extraPrices as $index => $item)
                                    <div class="border rounded-3 p-3 repeater-item" data-repeater-item>
                                        <div class="row g-3">
                                            <div class="col-md-5">
                                                <label class="form-label fw-semibold" style="font-size:13px;">Service Name</label>
                                                <input type="text" name="extra_prices[{{ $index }}][name]" value="{{ $item['name'] ?? '' }}" class="form-control" placeholder="e.g. Breakfast">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold" style="font-size:13px;">Price</label>
                                                <input type="number" min="0" step="0.01" name="extra_prices[{{ $index }}][price]" value="{{ $item['price'] ?? '' }}" class="form-control" placeholder="e.g. 15">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold" style="font-size:13px;">Type</label>
                                                <select name="extra_prices[{{ $index }}][type]" class="form-select">
                                                    <option value="one_time" {{ ($item['type'] ?? 'one_time') === 'one_time' ? 'selected' : '' }}>One-time</option>
                                                    <option value="per_day" {{ ($item['type'] ?? '') === 'per_day' ? 'selected' : '' }}>Per day</option>
                                                </select>
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger w-100" data-remove-repeater>x</button>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="extra_prices[{{ $index }}][per_person]" value="1" id="extra_price_person_{{ $index }}" {{ !empty($item['per_person']) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="extra_price_person_{{ $index }}" style="font-size:13px;">Price per person</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Policies & Rules Tab --}}
                <div class="tab-pane fade" id="tab-policies" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Check-in Time</label>
                            <input type="time" name="check_in_time" value="{{ old('check_in_time', $space->check_in_time ?? '14:00') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Check-out Time</label>
                            <input type="time" name="check_out_time" value="{{ old('check_out_time', $space->check_out_time ?? '11:00') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Minimum Stay (Nights)</label>
                            <input type="number" min="1" name="min_stay_nights" value="{{ old('min_stay_nights', $space->min_stay_nights ?? 1) }}" class="form-control @error('min_stay_nights') is-invalid @enderror" placeholder="e.g. 1">
                            @error('min_stay_nights') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Maximum Stay (Nights)</label>
                            <input type="number" min="1" name="max_stay_nights" value="{{ old('max_stay_nights', $space->max_stay_nights ?? '') }}" class="form-control @error('max_stay_nights') is-invalid @enderror" placeholder="e.g. 30">
                            @error('max_stay_nights') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Minimum Days Before Booking</label>
                            <input type="number" min="0" name="min_day_before_booking" value="{{ old('min_day_before_booking', $space->min_day_before_booking ?? '') }}" class="form-control @error('min_day_before_booking') is-invalid @enderror" placeholder="e.g. 3">
                            @error('min_day_before_booking') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Cancellation Policy</label>
                            <select name="cancellation_policy" class="form-select @error('cancellation_policy') is-invalid @enderror">
                                <option value="">- Select -</option>
                                @foreach (['flexible' => 'Flexible', 'moderate' => 'Moderate', 'strict' => 'Strict', 'non_refundable' => 'Non-refundable'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('cancellation_policy', $space->cancellation_policy ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('cancellation_policy') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 pt-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="fw-semibold mb-0" style="font-size:13px;">House Rules</h6>
                                    <div class="form-text">Add rules like no smoking, no pets, quiet hours, etc.</div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-add-repeater="house-rules">Add Rule</button>
                            </div>
                            <div id="house-rules-list" class="d-grid gap-3">
                                @foreach ($houseRules as $index => $item)
                                    <div class="border rounded-3 p-3 repeater-item" data-repeater-item>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold" style="font-size:13px;">Title</label>
                                                <input type="text" name="house_rules[{{ $index }}][title]" value="{{ $item['title'] ?? '' }}" class="form-control" placeholder="e.g. No Smoking">
                                            </div>
                                            <div class="col-md-7">
                                                <label class="form-label fw-semibold" style="font-size:13px;">Description</label>
                                                <textarea name="house_rules[{{ $index }}][content]" rows="3" class="form-control" placeholder="e.g. Smoking is not allowed anywhere inside the property">{{ $item['content'] ?? '' }}</textarea>
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger w-100" data-remove-repeater>x</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Amenities Tab --}}
                <div class="tab-pane fade" id="tab-amenities" role="tabpanel">
                    @forelse ($amenitiesByCategory as $category => $items)
                        <p class="text-uppercase fw-bold mb-2 mt-3" style="font-size:11px;color:var(--clr-primary);letter-spacing:.05em;">{{ ucwords(str_replace('_', ' ', $category)) }}</p>
                        <div class="row g-2 mb-2">
                            @foreach ($items as $amenity)
                                <div class="col-md-4 col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="amenity_ids[]" value="{{ $amenity->id }}" id="am_{{ $amenity->id }}" {{ in_array($amenity->id, $selectedAmenities) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="am_{{ $amenity->id }}" style="font-size:13px;">
                                            {{ $amenity->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <p class="text-muted">No amenities available. Add amenities in the Amenities management section first.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-header bg-white border-bottom fw-semibold py-3" style="font-size:13px;">Publish Settings</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Currency</label>
                        <select name="currency" class="form-select form-select-sm @error('currency') is-invalid @enderror">
                            @foreach (\App\Models\Currency::supported() as $code => $cur)
                                <option value="{{ $code }}" {{ old('currency', $space->currency ?? config('currency.default')) === $code ? 'selected' : '' }}>{{ $code }} - {{ $cur['name'] }}</option>
                            @endforeach
                        </select>
                        @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @if(request()->routeIs('admin.*'))
                    <hr class="my-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Status <span class="text-danger">*</span></label>
                    <div class="d-flex flex-column gap-2 mb-3">
                        @foreach (['active' => 'Active', 'draft' => 'Draft', 'inactive' => 'Inactive', 'suspended' => 'Suspended'] as $value => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" value="{{ $value }}" id="status_{{ $value }}" {{ old('status', $space->status ?? 'active') === $value ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_{{ $value }}" style="font-size:13px;">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                    <hr class="my-3">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured" {{ old('is_featured', $space->is_featured ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="isFeatured" style="font-size:13px;">Featured Space</label>
                        <div class="form-text">Show in featured or home sections.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Sort Order</label>
                        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $space->sort_order ?? 0) }}" class="form-control form-control-sm">
                        <div class="form-text">Lower values appear first.</div>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-white border-top d-flex gap-2">
                    <button type="submit" class="btn btn-sm flex-fill text-white fw-semibold" style="background:var(--clr-primary);border-radius:var(--radius-btn);">{{ $isEdit ? 'Save Changes' : 'Create Space' }}</button>
                    <a href="{{ request()->routeIs('admin.*') ? route('admin.spaces.index') : route('vendor.spaces.index') }}" class="btn btn-sm btn-outline-secondary">Cancel</a>
                </div>
            </div>

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
        </div>
    </div>
</form>

@include('admin::media.partials.browser-modal')

<template id="house-rules-template">
    <div class="border rounded-3 p-3 repeater-item" data-repeater-item>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold" style="font-size:13px;">Title</label>
                <input type="text" name="house_rules[__INDEX__][title]" class="form-control" placeholder="e.g. No Smoking">
            </div>
            <div class="col-md-7">
                <label class="form-label fw-semibold" style="font-size:13px;">Description</label>
                <textarea name="house_rules[__INDEX__][content]" rows="3" class="form-control" placeholder="e.g. Smoking is not allowed anywhere inside the property"></textarea>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-sm btn-outline-danger w-100" data-remove-repeater>x</button>
            </div>
        </div>
    </div>
</template>

<template id="extra-prices-template">
    <div class="border rounded-3 p-3 repeater-item" data-repeater-item>
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label fw-semibold" style="font-size:13px;">Service Name</label>
                <input type="text" name="extra_prices[__INDEX__][name]" class="form-control" placeholder="e.g. Breakfast">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold" style="font-size:13px;">Price</label>
                <input type="number" min="0" step="0.01" name="extra_prices[__INDEX__][price]" class="form-control" placeholder="e.g. 15">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold" style="font-size:13px;">Type</label>
                <select name="extra_prices[__INDEX__][type]" class="form-select">
                    <option value="one_time">One-time</option>
                    <option value="per_day">Per day</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-sm btn-outline-danger w-100" data-remove-repeater>x</button>
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="extra_prices[__INDEX__][per_person]" value="1" id="extra_price_person___INDEX__">
                    <label class="form-check-label" for="extra_price_person___INDEX__" style="font-size:13px;">Price per person</label>
                </div>
            </div>
        </div>
    </div>
</template>

@push('styles')
<style>
    .media-picker-card {
        border: 1px solid #dbe3ef;
        border-radius: 12px;
        background: #f8fafc;
        min-height: 210px;
        padding: 18px;
        cursor: pointer;
        transition: .2s ease;
    }
    .media-picker-card:hover {
        border-color: var(--clr-primary);
        box-shadow: 0 0 0 3px rgba(58, 181, 212, 0.12);
    }
    .media-picker-preview {
        min-height: 170px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .media-picker-preview img {
        max-width: 100%;
        max-height: 170px;
        object-fit: cover;
    }
    .media-picker-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        width: 100%;
        color: #94a3b8;
    }
    .media-picker-icon {
        font-size: 54px;
        line-height: 1;
    }
</style>
@endpush

@push('scripts')
<script>
    const nameInput = document.getElementById('spaceName');
    const slugInput = document.getElementById('spaceSlug');

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

    const repeaterConfig = {
        'house-rules': { list: 'house-rules-list', template: 'house-rules-template' },
        'extra-prices': { list: 'extra-prices-list', template: 'extra-prices-template' },
    };

    document.querySelectorAll('[data-add-repeater]').forEach((button) => {
        button.addEventListener('click', function () {
            const key = this.dataset.addRepeater;
            const config = repeaterConfig[key];
            if (!config) return;
            const list = document.getElementById(config.list);
            const template = document.getElementById(config.template);
            if (!list || !template) return;
            const index = list.querySelectorAll('[data-repeater-item]').length;
            list.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', index));
        });
    });

    document.addEventListener('click', function (event) {
        const button = event.target.closest('[data-remove-repeater]');
        if (!button) return;
        const item = button.closest('[data-repeater-item]');
        const container = item?.parentElement;
        if (!item || !container) return;

        if (container.querySelectorAll('[data-repeater-item]').length === 1) {
            item.querySelectorAll('input, textarea, select').forEach((field) => {
                if (field.type === 'checkbox' || field.type === 'radio') {
                    field.checked = false;
                } else {
                    field.value = '';
                }
            });
            return;
        }

        item.remove();
    });
</script>
@endpush
