@php
    $isEdit = isset($hotel);
    $action = $isEdit ? route('admin.hotels.update', $hotel->id) : route('admin.hotels.store');

    $paymentOptions = [
        'cash' => 'Cash',
        'card' => 'Credit/Debit Card',
        'bank_transfer' => 'Bank Transfer',
        'crypto' => 'Crypto',
    ];
    $languageOptions = [
        'en' => 'English',
        'ar' => 'Arabic',
        'fr' => 'French',
        'de' => 'German',
        'zh' => 'Chinese',
        'ru' => 'Russian',
        'es' => 'Spanish',
        'tr' => 'Turkish',
    ];
    $countries = [
        'AE' => 'UAE',
        'SA' => 'Saudi Arabia',
        'QA' => 'Qatar',
        'KW' => 'Kuwait',
        'BH' => 'Bahrain',
        'OM' => 'Oman',
        'EG' => 'Egypt',
        'GB' => 'United Kingdom',
        'US' => 'United States',
        'FR' => 'France',
        'DE' => 'Germany',
        'TR' => 'Turkey',
        'TH' => 'Thailand',
        'MY' => 'Malaysia',
        'SG' => 'Singapore',
        'IN' => 'India',
        'PK' => 'Pakistan',
        'MA' => 'Morocco',
        'TN' => 'Tunisia',
        'JO' => 'Jordan',
        'LB' => 'Lebanon',
    ];

    $amenitiesByCategory = $amenities->groupBy('category');
    $servicesByCategory = $services->groupBy('category');
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
    $selectedAmenities = $normalizeMultiSelect(old('amenity_ids', $isEdit ? $hotel->amenities->pluck('id')->all() : []));
    $selectedServices = $normalizeMultiSelect(old('service_ids', $isEdit ? $hotel->services->pluck('id')->all() : []));
    $selectedPaymentMethods = $normalizeMultiSelect(old('payment_methods', $hotel->payment_methods ?? []));
    $selectedLanguages = $normalizeMultiSelect(old('languages_spoken', $hotel->languages_spoken ?? []));

    $featuredImageId = old('image_id', $hotel->image_id ?? '');
    $bannerImageId = old('banner_image_id', $hotel->banner_image_id ?? '');
    $galleryValue = (string) old('gallery', $hotel->gallery ?? '');
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
    $featuredImage = $resolveMediaUrl($featuredImageId, $hotel->featured_image_url ?? null);
    $bannerImage = $resolveMediaUrl($bannerImageId, $hotel->banner_image_url ?? null);
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
        $galleryImages = collect($hotel->gallery_urls ?? [])->map(fn ($path) => asset($path))->all();
    }
    $policies = old('policies', $hotel->policies ?? []);
    $nearbyPlaces = old('nearby_places', $hotel->nearby_places ?? []);
    $extraPrices = old('extra_prices', $hotel->extra_prices ?? []);

    if (empty($policies)) {
        $policies = [['title' => '', 'content' => '']];
    }
    if (empty($nearbyPlaces)) {
        $nearbyPlaces = [['name' => '', 'content' => '', 'value' => '', 'type' => 'm']];
    }
    if (empty($extraPrices)) {
        $extraPrices = [['name' => '', 'price' => '', 'type' => 'one_time', 'per_person' => false]];
    }
@endphp

<form method="POST" action="{{ $action }}" id="hotelForm" enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <ul class="nav nav-tabs border-0 mb-0" id="hotelTabs" role="tablist" style="border-bottom: 2px solid #dee2e6 !important;">
                @foreach ([
                    'basic' => 'Basic Info',
                    'media' => 'Media',
                    'location' => 'Location',
                    'pricing' => 'Pricing',
                    'policies' => 'Policies & Nearby',
                    'amenities' => 'Amenities',
                    'services' => 'Services',
                ] as $tab => $label)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }} border-0 pb-2" id="{{ $tab }}-tab" data-bs-toggle="tab" data-bs-target="#tab-{{ $tab }}" type="button" role="tab" style="font-size:13px;font-weight:500;">
                            {{ $label }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content bg-white border border-top-0 rounded-bottom-3 rounded-end-3 p-4" id="hotelTabContent">
                <div class="tab-pane fade show active" id="tab-basic" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Hotel Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="hotelName" value="{{ old('name', $hotel->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Arabian Courtyard Hotel & Spa">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold" style="font-size:13px;">Slug</label>
                            <input type="text" name="slug" id="hotelSlug" value="{{ old('slug', $hotel->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror" placeholder="auto-generated from name">
                            <div class="form-text">Leave empty to auto-generate.</div>
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:13px;">Star Rating</label>
                            <select name="star_rating" class="form-select @error('star_rating') is-invalid @enderror">
                                <option value="">- Select -</option>
                                @for ($s = 1; $s <= 5; $s++)
                                    <option value="{{ $s }}" {{ old('star_rating', $hotel->star_rating ?? '') == $s ? 'selected' : '' }}>{{ $s }} Star{{ $s > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                            @error('star_rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Short Description</label>
                            <input type="text" name="short_description" value="{{ old('short_description', $hotel->short_description ?? '') }}" class="form-control @error('short_description') is-invalid @enderror" placeholder="One-line summary shown in search results" maxlength="500">
                            @error('short_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Full Description</label>
                            <textarea name="description" rows="6" class="form-control @error('description') is-invalid @enderror" placeholder="Detailed hotel description...">{{ old('description', $hotel->description ?? '') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-media" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Featured Image</label>
                            <p class="text-muted mb-2" style="font-size:12px;">Main featured image for your hotel (recommended size: 800x600px)</p>
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
                            <p class="text-muted mb-2" style="font-size:12px;">Upload multiple images showcasing your hotel rooms, facilities, and amenities</p>
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
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Video URL</label>
                            <input type="url" name="video_url" value="{{ old('video_url', $hotel->video_url ?? '') }}" class="form-control @error('video_url') is-invalid @enderror" placeholder="https://www.youtube.com/watch?v=...">
                            @error('video_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-location" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Street Address <span class="text-danger">*</span></label>
                            <input type="text" name="address" value="{{ old('address', $hotel->address ?? '') }}" class="form-control @error('address') is-invalid @enderror" placeholder="e.g. Al Fahidi Street, Bur Dubai">
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" value="{{ old('city', $hotel->city ?? '') }}" class="form-control @error('city') is-invalid @enderror" placeholder="e.g. Dubai">
                            @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">State / Region</label>
                            <input type="text" name="state" value="{{ old('state', $hotel->state ?? '') }}" class="form-control" placeholder="e.g. Dubai">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Country <span class="text-danger">*</span></label>
                            <select name="country" class="form-select @error('country') is-invalid @enderror">
                                <option value="">- Select Country -</option>
                                @foreach ($countries as $code => $name)
                                    <option value="{{ $code }}" {{ old('country', $hotel->country ?? '') === $code ? 'selected' : '' }}>{{ $name }} ({{ $code }})</option>
                                @endforeach
                            </select>
                            @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Postal / ZIP Code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $hotel->postal_code ?? '') }}" class="form-control" placeholder="e.g. 12345">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Latitude</label>
                            <input type="number" name="latitude" step="any" value="{{ old('latitude', $hotel->latitude ?? '') }}" class="form-control @error('latitude') is-invalid @enderror" placeholder="e.g. 25.2048">
                            @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Longitude</label>
                            <input type="number" name="longitude" step="any" value="{{ old('longitude', $hotel->longitude ?? '') }}" class="form-control @error('longitude') is-invalid @enderror" placeholder="e.g. 55.2708">
                            @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-pricing" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Base Price</label>
                            <input type="number" step="0.01" min="0" name="base_price" value="{{ old('base_price', $hotel->base_price ?? '') }}" class="form-control @error('base_price') is-invalid @enderror" placeholder="e.g. 250">
                            @error('base_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Sale Price</label>
                            <input type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price', $hotel->sale_price ?? '') }}" class="form-control @error('sale_price') is-invalid @enderror" placeholder="e.g. 225">
                            @error('sale_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Check-in Time</label>
                            <input type="time" name="check_in_time" value="{{ old('check_in_time', $hotel->check_in_time ?? '14:00') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Check-out Time</label>
                            <input type="time" name="check_out_time" value="{{ old('check_out_time', $hotel->check_out_time ?? '11:00') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Minimum Advance Reservation Days</label>
                            <input type="number" min="0" name="min_day_before_booking" value="{{ old('min_day_before_booking', $hotel->min_day_before_booking ?? '') }}" class="form-control @error('min_day_before_booking') is-invalid @enderror" placeholder="e.g. 3">
                            @error('min_day_before_booking') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Minimum Stay Days</label>
                            <input type="number" min="0" name="min_day_stays" value="{{ old('min_day_stays', $hotel->min_day_stays ?? '') }}" class="form-control @error('min_day_stays') is-invalid @enderror" placeholder="e.g. 2">
                            @error('min_day_stays') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

                <div class="tab-pane fade" id="tab-policies" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Hotel Email</label>
                            <input type="email" name="email" value="{{ old('email', $hotel->email ?? '') }}" class="form-control @error('email') is-invalid @enderror" placeholder="info@hotel.com">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $hotel->phone ?? '') }}" class="form-control" placeholder="+971 4 000 0000">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Website</label>
                            <input type="url" name="website" value="{{ old('website', $hotel->website ?? '') }}" class="form-control @error('website') is-invalid @enderror" placeholder="https://www.hotel.com">
                            @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                                <label class="form-label fw-semibold d-block mb-2" style="font-size:13px;">Payment Methods</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach ($paymentOptions as $value => $label)
                                    @php $checked = in_array($value, $selectedPaymentMethods, true); @endphp
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="payment_methods[]" value="{{ $value }}" id="pm_{{ $value }}" {{ $checked ? 'checked' : '' }}>
                                        <label class="form-check-label" for="pm_{{ $value }}" style="font-size:13px;">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold d-block mb-2" style="font-size:13px;">Languages Spoken</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach ($languageOptions as $value => $label)
                                    @php $checked = in_array($value, $selectedLanguages, true); @endphp
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="languages_spoken[]" value="{{ $value }}" id="lang_{{ $value }}" {{ $checked ? 'checked' : '' }}>
                                        <label class="form-check-label" for="lang_{{ $value }}" style="font-size:13px;">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12 pt-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="fw-semibold mb-0" style="font-size:13px;">Policies & Rules</h6>
                                    <div class="form-text">Add check-in rules, child policy, pet policy, and other guest instructions.</div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-add-repeater="policies">Add Policy</button>
                            </div>
                            <div id="policies-list" class="d-grid gap-3">
                                @foreach ($policies as $index => $item)
                                    <div class="border rounded-3 p-3 repeater-item" data-repeater-item>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold" style="font-size:13px;">Title</label>
                                                <input type="text" name="policies[{{ $index }}][title]" value="{{ $item['title'] ?? '' }}" class="form-control" placeholder="e.g. Check-in Time">
                                            </div>
                                            <div class="col-md-7">
                                                <label class="form-label fw-semibold" style="font-size:13px;">Description</label>
                                                <textarea name="policies[{{ $index }}][content]" rows="3" class="form-control" placeholder="e.g. Check-in from 2:00 PM, early check-in on request">{{ $item['content'] ?? '' }}</textarea>
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger w-100" data-remove-repeater>x</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12 pt-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="fw-semibold mb-0" style="font-size:13px;">Nearby Places</h6>
                                    <div class="form-text">List airports, malls, landmarks, metro stations, or attractions near the hotel.</div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-add-repeater="nearby">Add Nearby Place</button>
                            </div>
                            <div id="nearby-list" class="d-grid gap-3">
                                @foreach ($nearbyPlaces as $index => $item)
                                    <div class="border rounded-3 p-3 repeater-item" data-repeater-item>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold" style="font-size:13px;">Name</label>
                                                <input type="text" name="nearby_places[{{ $index }}][name]" value="{{ $item['name'] ?? '' }}" class="form-control" placeholder="e.g. Dubai Mall">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold" style="font-size:13px;">Description</label>
                                                <input type="text" name="nearby_places[{{ $index }}][content]" value="{{ $item['content'] ?? '' }}" class="form-control" placeholder="e.g. Popular shopping destination">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold" style="font-size:13px;">Distance</label>
                                                <input type="number" min="0" step="0.01" name="nearby_places[{{ $index }}][value]" value="{{ $item['value'] ?? '' }}" class="form-control" placeholder="500">
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label fw-semibold" style="font-size:13px;">Unit</label>
                                                <select name="nearby_places[{{ $index }}][type]" class="form-select">
                                                    <option value="m" {{ ($item['type'] ?? 'm') === 'm' ? 'selected' : '' }}>m</option>
                                                    <option value="km" {{ ($item['type'] ?? '') === 'km' ? 'selected' : '' }}>km</option>
                                                </select>
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger w-100" data-remove-repeater>x</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Related Hotel IDs</label>
                            <input type="text" name="related_hotel_ids" value="{{ old('related_hotel_ids', $hotel->related_hotel_ids ?? '') }}" class="form-control @error('related_hotel_ids') is-invalid @enderror" placeholder="e.g. 15,18,24">
                            <div class="form-text">Enter hotel IDs separated by commas.</div>
                            @error('related_hotel_ids') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-amenities" role="tabpanel">
                    @foreach ($amenitiesByCategory as $category => $items)
                        <p class="text-uppercase fw-bold mb-2 mt-3" style="font-size:11px;color:var(--clr-primary);letter-spacing:.05em;">{{ ucwords(str_replace('_', ' ', $category)) }}</p>
                        <div class="row g-2 mb-2">
                            @foreach ($items as $amenity)
                                <div class="col-md-4 col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="amenity_ids[]" value="{{ $amenity->id }}" id="am_{{ $amenity->id }}" {{ in_array($amenity->id, $selectedAmenities) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="am_{{ $amenity->id }}" style="font-size:13px;">
                                            {{ $amenity->name }}
                                            <span class="badge bg-light text-dark border ms-1" style="font-size:10px;">{{ strtoupper($amenity->applies_to) }}</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <div class="tab-pane fade" id="tab-services" role="tabpanel">
                    @foreach ($servicesByCategory as $category => $items)
                        <p class="text-uppercase fw-bold mb-2 mt-3" style="font-size:11px;color:var(--clr-primary);letter-spacing:.05em;">{{ ucwords(str_replace('_', ' ', $category)) }}</p>
                        <div class="row g-2 mb-2">
                            @foreach ($items as $service)
                                <div class="col-md-4 col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="service_ids[]" value="{{ $service->id }}" id="sv_{{ $service->id }}" {{ in_array($service->id, $selectedServices) ? 'checked' : '' }}>
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
                                <option value="{{ $code }}" {{ old('currency', $hotel->currency ?? config('currency.default')) === $code ? 'selected' : '' }}>{{ $code }} - {{ $cur['name'] }}</option>
                            @endforeach
                        </select>
                        @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <hr class="my-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Status <span class="text-danger">*</span></label>
                    <div class="d-flex flex-column gap-2 mb-3">
                        @foreach (['active' => 'Active', 'draft' => 'Draft', 'inactive' => 'Inactive', 'suspended' => 'Suspended'] as $value => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" value="{{ $value }}" id="status_{{ $value }}" {{ old('status', $hotel->status ?? 'active') === $value ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_{{ $value }}" style="font-size:13px;">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                    <hr class="my-3">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured" {{ old('is_featured', $hotel->is_featured ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="isFeatured" style="font-size:13px;">Featured Hotel</label>
                        <div class="form-text">Show in featured or home sections.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Sort Order</label>
                        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $hotel->sort_order ?? 0) }}" class="form-control form-control-sm">
                        <div class="form-text">Lower values appear first.</div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top d-flex gap-2">
                    <button type="submit" class="btn btn-sm flex-fill text-white fw-semibold" style="background:var(--clr-primary);border-radius:var(--radius-btn);">{{ $isEdit ? 'Save Changes' : 'Create Hotel' }}</button>
                    <a href="{{ route('admin.hotels.index') }}" class="btn btn-sm btn-outline-secondary">Cancel</a>
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

<template id="policy-template">
    <div class="border rounded-3 p-3 repeater-item" data-repeater-item>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold" style="font-size:13px;">Title</label>
                <input type="text" name="policies[__INDEX__][title]" class="form-control" placeholder="e.g. Check-in Time">
            </div>
            <div class="col-md-7">
                <label class="form-label fw-semibold" style="font-size:13px;">Description</label>
                <textarea name="policies[__INDEX__][content]" rows="3" class="form-control" placeholder="e.g. Check-in from 2:00 PM, early check-in on request"></textarea>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-sm btn-outline-danger w-100" data-remove-repeater>x</button>
            </div>
        </div>
    </div>
</template>

<template id="nearby-template">
    <div class="border rounded-3 p-3 repeater-item" data-repeater-item>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold" style="font-size:13px;">Name</label>
                <input type="text" name="nearby_places[__INDEX__][name]" class="form-control" placeholder="e.g. Dubai Mall">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold" style="font-size:13px;">Description</label>
                <input type="text" name="nearby_places[__INDEX__][content]" class="form-control" placeholder="e.g. Popular shopping destination">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold" style="font-size:13px;">Distance</label>
                <input type="number" min="0" step="0.01" name="nearby_places[__INDEX__][value]" class="form-control" placeholder="500">
            </div>
            <div class="col-md-1">
                <label class="form-label fw-semibold" style="font-size:13px;">Unit</label>
                <select name="nearby_places[__INDEX__][type]" class="form-select">
                    <option value="m">m</option>
                    <option value="km">km</option>
                </select>
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

    const repeaterConfig = {
        policies: { list: 'policies-list', template: 'policy-template' },
        nearby: { list: 'nearby-list', template: 'nearby-template' },
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
