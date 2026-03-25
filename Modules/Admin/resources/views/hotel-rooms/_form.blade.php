@php
    $isEdit = isset($hotelRoom);
    $action = $isEdit ? route('admin.hotel-rooms.update', $hotelRoom->id) : route('admin.hotel-rooms.store');
    $lockedHotelId = $lockedHotelId ?? null;
    $selectedHotelValue = old('hotel_id', $selectedHotelId ?? '');
    $roomImageId = old('image_id', $hotelRoom->image_id ?? '');
    $roomGalleryValue = (string) old('gallery', $hotelRoom->gallery ?? '');
    $resolveMediaUrl = function ($mediaId) {
        $mediaId = (int) $mediaId;
        if ($mediaId <= 0) {
            return null;
        }
        $media = \Modules\Admin\Models\MediaFile::find($mediaId);

        return $media?->url;
    };
    $roomImage = $resolveMediaUrl($roomImageId);
    $roomGalleryIds = collect(explode(',', $roomGalleryValue))
        ->map(fn ($id) => (int) trim($id))
        ->filter()
        ->values();
    $roomGalleryMedia = $roomGalleryIds->isNotEmpty()
        ? \Modules\Admin\Models\MediaFile::whereIn('id', $roomGalleryIds->all())->get()->keyBy('id')
        : collect();
    $roomGalleryImages = $roomGalleryIds->map(fn ($id) => optional($roomGalleryMedia->get($id))->url)
        ->filter()
        ->values()
        ->all();
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Room Details</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hotel</label>
                            @if ($lockedHotelId)
                                <input type="hidden" name="hotel_id" value="{{ $selectedHotelValue }}">
                            @endif
                            <select name="{{ $lockedHotelId ? 'hotel_id_disabled' : 'hotel_id' }}" class="form-select @error('hotel_id') is-invalid @enderror" {{ $lockedHotelId ? 'disabled' : '' }}>
                                <option value="">Select Hotel</option>
                                @foreach ($hotels as $hotel)
                                    <option value="{{ $hotel->id }}" {{ (string) $selectedHotelValue === (string) $hotel->id ? 'selected' : '' }}>
                                        {{ $hotel->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($lockedHotelId)
                                <div class="form-text">This room is locked to the hotel you clicked from.</div>
                            @endif
                            @error('hotel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Room Type</label>
                            <select name="room_type_id" class="form-select @error('room_type_id') is-invalid @enderror">
                                <option value="">- Select Room Type -</option>
                                @foreach ($roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}" {{ old('room_type_id', $hotelRoom->room_type_id ?? '') == $roomType->id ? 'selected' : '' }}>{{ $roomType->name }}</option>
                                @endforeach
                            </select>
                            @error('room_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Room Name / Number</label>
                            <input type="text" name="room_name" value="{{ old('room_name', $hotelRoom->room_name ?? '') }}" class="form-control @error('room_name') is-invalid @enderror" placeholder="e.g. 101, 201-A">
                            @error('room_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Featured Image</label>
                            <input type="hidden" name="image_id" id="room-image-id" value="{{ $roomImageId }}">
                            <div class="media-picker-card" data-open-media-browser data-media-target="room-featured" data-media-multiple="false">
                                <div class="media-picker-preview" id="room-image-preview">
                                    @if ($roomImage)
                                        <img src="{{ $roomImage }}" alt="Room Image" class="img-fluid rounded border" style="max-height:180px;">
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
                        <div class="col-12">
                            <label class="form-label fw-semibold">Gallery Images</label>
                            <div class="d-flex gap-2 mb-2">
                                <button type="button" class="btn btn-info btn-sm" data-open-media-browser data-media-target="room-gallery" data-media-multiple="true">
                                    Select images
                                </button>
                            </div>
                            <input type="hidden" name="gallery" id="room-gallery-image-ids" value="{{ $roomGalleryValue }}">
                            <div class="d-flex flex-wrap gap-2 mt-2" id="room-gallery-images-preview">
                                @foreach ($roomGalleryImages as $image)
                                    <img src="{{ $image }}" alt="Room Gallery Image" class="rounded border" style="width:110px;height:90px;object-fit:cover;">
                                @endforeach
                            </div>
                            @error('gallery') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Floor</label>
                            <input type="text" name="floor" value="{{ old('floor', $hotelRoom->floor ?? '') }}" class="form-control @error('floor') is-invalid @enderror">
                            @error('floor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Settings</h6>
                    <p class="text-muted mb-3" style="font-size:11px;">Pricing is managed on the Room Type.</p>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $hotelRoom->sort_order ?? 0) }}" class="form-control @error('sort_order') is-invalid @enderror">
                            @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="room_is_active" {{ old('is_active', $hotelRoom->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="room_is_active">Active Room</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top d-flex gap-2">
                    <button type="submit" class="btn btn-sm flex-fill text-white fw-semibold" style="background:var(--clr-primary);border-radius:var(--radius-btn);">
                        {{ $isEdit ? 'Save Changes' : 'Create Room' }}
                    </button>
                    <a href="{{ route('admin.hotel-rooms.index', ['hotel_id' => old('hotel_id', $selectedHotelId ?? null)]) }}" class="btn btn-sm btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>

@include('admin::media.partials.browser-modal')

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
    (() => {
        let activeRoomMediaTarget = null;

        function emptyRoomMediaCard() {
            return `
                <div class="media-picker-empty">
                    <div class="media-picker-icon">&#128247;</div>
                    <span class="btn btn-primary btn-sm">Upload image</span>
                </div>
            `;
        }

        function selectedModalFiles() {
            return Array.from(document.querySelectorAll('#media-browser-grid [data-media-file]'))
                .filter((item) => item.querySelector('.media-browser-card')?.classList.contains('active'))
                .map((item) => ({
                    id: Number(item.dataset.mediaId || 0),
                    url: item.querySelector('img')?.src || '',
                }))
                .filter((file) => file.id > 0 && file.url);
        }

        document.addEventListener('click', (event) => {
            const trigger = event.target.closest('[data-open-media-browser][data-media-target^="room-"]');
            if (!trigger) return;

            activeRoomMediaTarget = trigger.dataset.mediaTarget || null;
        });

        document.getElementById('media-use-selected-btn')?.addEventListener('click', () => {
            if (!activeRoomMediaTarget) return;

            const files = selectedModalFiles();

            if (activeRoomMediaTarget === 'room-featured') {
                const hidden = document.getElementById('room-image-id');
                const preview = document.getElementById('room-image-preview');

                if (hidden && preview) {
                    hidden.value = files[0]?.id || '';
                    preview.innerHTML = files[0]
                        ? `<img src="${files[0].url}" alt="Room Image" class="img-fluid rounded border" style="max-height:180px;">`
                        : emptyRoomMediaCard();
                }
            }

            if (activeRoomMediaTarget === 'room-gallery') {
                const hidden = document.getElementById('room-gallery-image-ids');
                const preview = document.getElementById('room-gallery-images-preview');

                if (hidden && preview) {
                    hidden.value = files.map((file) => file.id).join(',');
                    preview.innerHTML = files.map((file) => (
                        `<img src="${file.url}" alt="Room Gallery Image" class="rounded border" style="width:110px;height:90px;object-fit:cover;">`
                    )).join('');
                }
            }
        });

        document.getElementById('mediaBrowserModal')?.addEventListener('hidden.bs.modal', () => {
            activeRoomMediaTarget = null;
        });
    })();
</script>
@endpush


