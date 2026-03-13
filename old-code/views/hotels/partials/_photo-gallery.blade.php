{{-- Photo Gallery --}}
@php
    $mainImage  = $hotel->image_url;
    $gallery    = $hotel->gallery_urls ?? [];
    $allImages  = array_merge([$mainImage], $gallery);
    $extraCount = max(0, count($allImages) - 3);
@endphp

<div class="hotel-gallery">
    <div class="hotel-gallery__grid">

        {{-- Main large image --}}
        <div class="hotel-gallery__main">
            <img src="{{ $allImages[0] }}" alt="{{ $hotel->title }}" loading="lazy">
        </div>

        {{-- Side thumbnails --}}
        <div class="hotel-gallery__side">
            <div class="hotel-gallery__thumb">
                <img src="{{ $allImages[1] ?? $allImages[0] }}" alt="{{ $hotel->title }}" loading="lazy">
            </div>
            <div class="hotel-gallery__thumb hotel-gallery__thumb--last">
                <img src="{{ $allImages[2] ?? $allImages[0] }}" alt="{{ $hotel->title }}" loading="lazy">
                @if($extraCount > 0)
                    <button class="hotel-gallery__more-btn" data-bs-toggle="modal" data-bs-target="#galleryModal">
                        +{{ $extraCount }} Photos
                    </button>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- Gallery Modal --}}
@if(count($allImages) > 3)
<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0">
                <h6 class="modal-title text-white">All Photos</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    @foreach($allImages as $img)
                    <div class="col-6 col-md-4">
                        <img src="{{ $img }}" alt="{{ $hotel->title }}" class="img-fluid rounded" loading="lazy">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif
