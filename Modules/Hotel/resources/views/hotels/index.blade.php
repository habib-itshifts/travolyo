@extends('layouts.master')

@section('title', 'Hotel Search – Travolyo')

@push('styles')
<style>
/* ── Hotel listing page ─────────────────────────────── */
.hotel-page-bg { background: #f4f6fb; min-height: 60vh; }
:root {
    --hotel-theme: var(--primary, #17c3ce);
    --hotel-theme-dark: var(--primary-dark, #13afba);
    --hotel-theme-soft: rgba(23, 195, 206, .18);
}

/* Filter card */
.filter-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 6px rgba(0,0,0,.07); }
.filter-card .filter-title { font-size: .7rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d; margin-bottom: 12px; }
.filter-divider { border-top: 1px solid #eef0f4; margin: 16px 0; }

/* Price slider */
.price-slider-wrap input[type=range] {
    -webkit-appearance: none; width: 100%; height: 4px;
    border-radius: 2px; outline: none; cursor: pointer;
    background: linear-gradient(to right, var(--hotel-theme) 100%, #e5e7eb 100%);
}
.price-slider-wrap input[type=range]::-webkit-slider-thumb {
    -webkit-appearance: none; width: 16px; height: 16px;
    border-radius: 50%; background: var(--hotel-theme); cursor: pointer;
    box-shadow: 0 0 0 3px var(--hotel-theme-soft);
}
.price-slider-wrap input[type=range]::-moz-range-thumb {
    border: 0; border-radius: 50%; background: var(--hotel-theme); cursor: pointer;
    box-shadow: 0 0 0 3px var(--hotel-theme-soft);
    height: 16px; width: 16px;
}
.filter-pill { display: flex; align-items: center; gap: 8px; padding: 7px 10px; border-radius: 8px; cursor: pointer; transition: background .15s, color .15s; font-size: .875rem; }
.filter-pill:hover { background: #f0f4ff; }
.filter-pill input { accent-color: var(--hotel-theme); }

/* Results header */
.results-header { border-bottom: 1px solid #eef0f4; padding-bottom: 12px; margin-bottom: 16px; }
.sort-select { border: 1px solid #dee2e6; border-radius: 6px; padding: 4px 10px; font-size: .85rem; color: #495057; background: #fff; cursor: pointer; }

/* Hotel card */
.hotel-card {
    background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(0,0,0,.07);
    margin-bottom: 12px; overflow: hidden; transition: box-shadow .2s, transform .2s;
    display: flex;
}
.hotel-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,.12); transform: translateY(-1px); }
.hotel-card__img {
    width: 220px; min-width: 220px; height: 165px;
    object-fit: cover; flex-shrink: 0;
}
.hotel-card__img-placeholder {
    width: 220px; min-width: 220px; height: 165px;
    background: #f0f4ff; display: flex; align-items: center; justify-content: center;
    color: #a0aec0; font-size: 2rem; flex-shrink: 0;
}
.hotel-card__body { padding: 16px 20px; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
.hotel-card__name { font-size: 1.05rem; font-weight: 700; color: #1a2942; margin-bottom: 4px; }
.hotel-card__addr { font-size: .8rem; color: #6c757d; margin-bottom: 8px; }
.hotel-card__stars { color: #f59e0b; font-size: .85rem; }
.hotel-card__amenities { font-size: .75rem; color: #6c757d; }
.hotel-card__price { font-size: 1.3rem; font-weight: 700; color: var(--hotel-theme); }
.hotel-card__night { font-size: .75rem; color: #6c757d; }
.hotel-card__badge { font-size: .65rem; font-weight: 600; padding: 2px 8px; border-radius: 20px; }

/* Pagination */
.hotel-pagination-wrap { display: flex; justify-content: center; margin-top: 24px; }
.hotel-pagination {
    align-items: center;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 8px 24px rgba(18, 38, 63, .08);
    display: inline-flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
    padding: 10px 12px;
}
.hotel-pagination__btn {
    background: #f8fafc;
    border: 1px solid #dbe4ef;
    border-radius: 12px;
    color: #334155;
    font-size: .9rem;
    font-weight: 600;
    min-width: 42px;
    padding: 9px 12px;
    transition: all .15s ease;
}
.hotel-pagination__btn:hover:not(:disabled) {
    border-color: var(--hotel-theme);
    color: var(--hotel-theme);
    transform: translateY(-1px);
}
.hotel-pagination__btn.is-active {
    background: var(--hotel-theme);
    border-color: var(--hotel-theme);
    box-shadow: 0 10px 20px var(--hotel-theme-soft);
    color: #fff;
}
.hotel-pagination__btn:disabled {
    cursor: not-allowed;
    opacity: .45;
}
.hotel-pagination__ellipsis,
.hotel-pagination__summary {
    color: #64748b;
    font-size: .85rem;
    font-weight: 600;
    padding: 0 6px;
}

/* Skeleton */
.skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 8px; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* Modal — Hotel detail */
#hotelModal .modal-dialog { max-width: 860px; }
#hotelModal .modal-header { background: linear-gradient(135deg, var(--hotel-theme) 0%, #0ea5b5 100%); color: #fff; }
#hotelModal .modal-header .btn-close { filter: invert(1); }
.hotel-gallery { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; border-radius: 8px; overflow: hidden; max-height: 240px; margin-bottom: 20px; }
.hotel-gallery img { width: 100%; height: 120px; object-fit: cover; }
.hotel-gallery img:first-child { grid-row: 1/3; height: 100%; }
.room-card { border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 16px; margin-bottom: 12px; transition: border-color .15s; }
.room-card:hover { border-color: var(--hotel-theme); }
.room-card__name { font-weight: 700; font-size: .95rem; color: #1a2942; }
.room-card__meta { font-size: .8rem; color: #6c757d; margin: 6px 0; }
.room-card__price { font-size: 1.15rem; font-weight: 700; color: var(--hotel-theme); }
.btn-select-room { padding: 6px 20px; font-size: .85rem; border-radius: 8px; font-weight: 600; }

@media (max-width: 576px) {
    .hotel-card { flex-direction: column; }
    .hotel-card__img, .hotel-card__img-placeholder { width: 100%; min-width: unset; height: 160px; }
}
</style>
@endpush

@section('content')

{{-- Search widget --}}
<div class="bg-white border-bottom py-3 shadow-sm">
    <div class="container">
        @include('website.partials._search-widget', ['activeTab' => 'hotels'])
    </div>
</div>

<div class="hotel-page-bg py-4">
<div class="container">
<div class="row g-4">

    {{-- ── Filters sidebar ───────────────────────────── --}}
    <div class="col-12 col-lg-3" id="filters-col">
        <div class="filter-card">

            <div class="filter-title">Price per Night</div>
            <div class="price-slider-wrap">
                <input type="range" id="priceRange" min="0" max="9999" value="9999" step="1">
            </div>
            <div class="d-flex justify-content-between mt-2 small text-muted">
                <span id="priceRangeMin">—</span>
                <span>Up to <strong id="priceRangeVal">—</strong></span>
            </div>

            <div class="filter-divider"></div>
            <div class="filter-title">Star Rating</div>
            <label class="filter-pill">
                <input type="radio" name="starFilter" class="star-filter" value="all" checked> Any
            </label>
            <label class="filter-pill">
                <input type="radio" name="starFilter" class="star-filter" value="5">
                <span>&#9733;&#9733;&#9733;&#9733;&#9733; 5 Stars</span>
            </label>
            <label class="filter-pill">
                <input type="radio" name="starFilter" class="star-filter" value="4">
                <span>&#9733;&#9733;&#9733;&#9733; 4+ Stars</span>
            </label>
            <label class="filter-pill">
                <input type="radio" name="starFilter" class="star-filter" value="3">
                <span>&#9733;&#9733;&#9733; 3+ Stars</span>
            </label>

            <div class="filter-divider"></div>
            <div class="filter-title">Provider</div>
            <label class="filter-pill">
                <input type="radio" name="providerFilter" class="provider-filter" value="all" checked> All
            </label>
            <label class="filter-pill">
                <input type="radio" name="providerFilter" class="provider-filter" value="local"> Local
            </label>
            <label class="filter-pill">
                <input type="radio" name="providerFilter" class="provider-filter" value="travolyo_b2b_local"> B2B Local
            </label>
            <label class="filter-pill">
                <input type="radio" name="providerFilter" class="provider-filter" value="travolyo_b2b_net_streaming"> B2B Streaming
            </label>

        </div>
    </div>

    {{-- ── Results column ─────────────────────────────── --}}
    <div class="col-12 col-lg-9">

        @if(empty($params['city']))
            <div class="filter-card text-center py-5">
                <i class="bi bi-building text-muted" style="font-size:3rem"></i>
                <p class="text-muted mt-3 mb-0">Enter a destination above and click <strong>Search Hotels</strong>.</p>
            </div>
        @else

            {{-- Results header --}}
            <div class="results-header d-none d-flex justify-content-between align-items-center" id="results-header">
                <div>
                    <h6 class="mb-0 fw-semibold">Available Hotels</h6>
                    <p class="text-muted small mb-0" id="results-count"></p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-down-up text-muted small"></i>
                    <span class="text-muted small">Sort:</span>
                    <select class="sort-select" id="sortSelect">
                        <option value="price">Price</option>
                        <option value="stars">Stars</option>
                        <option value="name">Name</option>
                    </select>
                </div>
            </div>

            {{-- Skeleton loader --}}
            <div id="hotel-loading">
                @for($i = 0; $i < 4; $i++)
                <div class="hotel-card mb-3" style="border-radius:12px;overflow:hidden;">
                    <div class="skeleton" style="width:220px;min-width:220px;height:165px;border-radius:0"></div>
                    <div class="p-3 flex-fill">
                        <div class="skeleton mb-2" style="height:18px;width:65%"></div>
                        <div class="skeleton mb-2" style="height:13px;width:40%"></div>
                        <div class="skeleton mb-3" style="height:13px;width:55%"></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="skeleton" style="height:22px;width:80px"></div>
                            <div class="skeleton" style="height:32px;width:100px;border-radius:8px"></div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>

            {{-- Hotel cards rendered by JS --}}
            <div id="hotel-offers" class="d-none"></div>
            <div id="hotel-pagination" class="hotel-pagination-wrap d-none"></div>
            <div id="hotel-error"  class="d-none alert alert-danger rounded-3"></div>

        @endif

    </div>
</div>
</div>
</div>

{{-- ── Hotel Detail Modal ───────────────────────────── --}}
<div class="modal fade" id="hotelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalHotelName">Hotel Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                {{-- Filled by JS --}}
            </div>
        </div>
    </div>
</div>

@endsection

@if(!empty($params['city']))
@push('scripts')
<script>
(function () {

    const params    = @json($params);
    const searchUrl = '{{ route('api.hotels.search') }}';
    const prebookUrl= '{{ route('api.hotels.prebook') }}';
    const loading   = document.getElementById('hotel-loading');
    const offersEl  = document.getElementById('hotel-offers');
    const errorEl   = document.getElementById('hotel-error');
    const header    = document.getElementById('results-header');
    const countEl   = document.getElementById('results-count');
    const paginationEl = document.getElementById('hotel-pagination');
    const pageSize = 10;

    let allHotels = [];
    let currentPage = 1;

    // ── Stars html ──────────────────────────────────────
    function stars(n) {
        let h = '';
        for (let i = 1; i <= 5; i++) h += i <= n ? '&#9733;' : '&#9734;';
        return h;
    }

    // ── Provider badge ───────────────────────────────────
    function providerBadge(p) {
        const map = {
            'local':                        { label: 'Local',     bg: '#e0f2fe', color: '#0369a1' },
            'travolyo_b2b_local':           { label: 'B2B Local', bg: '#ecfdf5', color: '#059669' },
            'travolyo_b2b_net_streaming':   { label: 'B2B',       bg: '#f3e8ff', color: '#7c3aed' },
        };
        const m = map[p] ?? { label: p, bg: '#f3f4f6', color: '#6b7280' };
        return `<span class="hotel-card__badge" style="background:${m.bg};color:${m.color}">${m.label}</span>`;
    }

    function normalizePrice(value) {
        if (typeof value === 'number') {
            return Number.isFinite(value) ? value : 0;
        }

        const normalized = String(value ?? '')
            .replace(/,/g, '')
            .replace(/[^\d.]/g, '');

        const parsed = parseFloat(normalized);
        return Number.isFinite(parsed) ? parsed : 0;
    }

    function listingCurrency() {
        return String(allHotels.find((hotel) => hotel?.currency)?.currency || 'USD').trim() || 'USD';
    }

    function formatMoney(value, currency = listingCurrency()) {
        return `${currency} ${Math.round(normalizePrice(value)).toLocaleString('en-US')}`;
    }

    // ── Render one hotel card ────────────────────────────
    function renderCard(h) {
        const img = h.images?.[0]
            ? `<img class="hotel-card__img" src="${h.images[0]}" alt="${h.name}" loading="lazy">`
            : `<div class="hotel-card__img-placeholder"><i class="bi bi-building"></i></div>`;

        const amenities = (h.amenities ?? []).slice(0, 4).join(' · ');
        const badgeHtml = h.badge
            ? `<span class="hotel-card__badge bg-success-subtle text-success me-1">${h.badge.replace('_',' ')}</span>`
            : '';

        return `
        <div class="hotel-card js-hotel-card"
             data-price="${normalizePrice(h.lowest_price)}"
             data-stars="${h.star_rating}"
             data-name="${(h.name ?? '').toLowerCase()}"
             data-provider="${h.provider}">
            ${img}
            <div class="hotel-card__body">
                <div>
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div class="hotel-card__name">${h.name}</div>
                        <div class="text-end flex-shrink-0">
                            ${badgeHtml}
                            ${providerBadge(h.provider)}
                        </div>
                    </div>
                    <div class="hotel-card__addr">
                        <i class="bi bi-geo-alt me-1"></i>${h.address ?? h.city}${h.country ? ', ' + h.country : ''}
                    </div>
                    <div class="hotel-card__stars mb-1">${stars(h.star_rating ?? 0)}</div>
                    ${amenities ? `<div class="hotel-card__amenities"><i class="bi bi-check2-circle me-1 text-success"></i>${amenities}</div>` : ''}
                </div>
                <div class="d-flex align-items-center justify-content-between mt-3">
                    <div>
                        <div class="hotel-card__price">${formatMoney(h.lowest_price, h.currency)}</div>
                        <div class="hotel-card__night">per night</div>
                    </div>
                    <button class="btn btn-primary btn-select-room js-view-deal"
                        data-hotel='${JSON.stringify(h)}'>
                        View Deal
                    </button>
                </div>
            </div>
        </div>`;
    }

    // ── Render modal content ─────────────────────────────
    function renderModal(h) {
        document.getElementById('modalHotelName').textContent = h.name;

        const galleryHtml = (h.images ?? []).length
            ? `<div class="hotel-gallery mb-3">
                ${(h.images.slice(0,3)).map(url => `<img src="${url}" alt="" loading="lazy">`).join('')}
               </div>`
            : '';

        const amenitiesHtml = (h.amenities ?? []).length
            ? `<div class="mb-3">
                <div class="fw-semibold mb-2" style="font-size:.85rem;">Amenities</div>
                <div class="d-flex flex-wrap gap-2">
                    ${h.amenities.map(a => `<span class="badge bg-light text-dark border"><i class="bi bi-check2 me-1 text-success"></i>${a}</span>`).join('')}
                </div>
               </div>` : '';

        const checkInOut = (h.check_in_time || h.check_out_time)
            ? `<div class="d-flex gap-3 mb-3 small text-muted">
                ${h.check_in_time ? `<span><i class="bi bi-door-open me-1"></i>Check-in: <strong>${h.check_in_time}</strong></span>` : ''}
                ${h.check_out_time ? `<span><i class="bi bi-door-closed me-1"></i>Check-out: <strong>${h.check_out_time}</strong></span>` : ''}
               </div>` : '';

        const rooms = (h.rooms ?? []).filter(r => r.is_available !== false);
        const roomsHtml = rooms.length
            ? rooms.map(r => `
                <div class="room-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="room-card__name">${r.name}</div>
                            <div class="room-card__meta">
                                ${r.bed_configuration ? `<i class="bi bi-moon me-1"></i>${r.bed_configuration} &nbsp;` : ''}
                                ${r.max_adults ? `<i class="bi bi-person me-1"></i>${r.max_adults} Adults` : ''}
                                ${r.size_sqm ? ` &nbsp;<i class="bi bi-aspect-ratio me-1"></i>${r.size_sqm} m²` : ''}
                            </div>
                            ${(r.amenities ?? []).length ? `<div class="d-flex flex-wrap gap-1 mt-1">
                                ${r.amenities.slice(0,5).map(a => `<span class="badge bg-light text-dark border" style="font-size:.7rem">${a}</span>`).join('')}
                            </div>` : ''}
                        </div>
                        <div class="text-end ms-3">
                            <div class="room-card__price">${formatMoney(r.base_price ?? r.total_price ?? 0, r.currency ?? h.currency)}</div>
                            <div style="font-size:.75rem;color:#6c757d;">${r.nights ? `${r.nights} nights total: ${formatMoney(r.total_price ?? 0, r.currency ?? h.currency)}` : 'per night'}</div>
                            <button class="btn btn-primary btn-select-room mt-2 js-select-room"
                                data-offer-id="${h.id}"
                                data-room-id="${r.id}"
                                data-provider="${h.provider}"
                                data-hotel-name="${h.name}"
                                data-room-name="${r.name}"
                                data-city="${h.city}"
                                data-country="${h.country ?? ''}"
                                data-check-in="${params.check_in ?? ''}"
                                data-check-out="${params.check_out ?? ''}"
                                data-adults="${params.adults ?? 1}"
                                data-children="${params.children ?? 0}">
                                Select Room
                            </button>
                        </div>
                    </div>
                </div>`).join('')
            : `<div class="alert alert-warning">No rooms available for the selected dates.</div>`;

        document.getElementById('modalBody').innerHTML = `
            ${galleryHtml}
            <div class="mb-2">
                <span class="hotel-card__stars">${stars(h.star_rating ?? 0)}</span>
                <span class="ms-2 text-muted small">${h.city}${h.country ? ', ' + h.country : ''}</span>
            </div>
            ${checkInOut}
            ${h.description ? `<p class="text-muted small mb-3">${h.description}</p>` : ''}
            ${amenitiesHtml}
            <hr>
            <h6 class="fw-bold mb-3">Available Rooms</h6>
            ${roomsHtml}
        `;
    }

    // ── Fetch hotels ─────────────────────────────────────
    async function loadHotels() {
        try {
            const res  = await fetch(searchUrl, {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify({
                    city:       params.city,
                    check_in:   params.check_in,
                    check_out:  params.check_out,
                    adults:     parseInt(params.adults ?? 1, 10),
                    children:   parseInt(params.children ?? 0, 10),
                    per_page:   100,
                }),
            });

            const data = await res.json();
            loading.classList.add('d-none');

            if (!data.success || !data.data?.length) {
                errorEl.textContent = data.message ?? 'No hotels found for your search.';
                errorEl.classList.remove('d-none');
                paginationEl?.classList.add('d-none');
                return;
            }

            allHotels = data.data;
            offersEl.innerHTML = allHotels.map(h => renderCard(h)).join('');
            offersEl.classList.remove('d-none');

            header.classList.remove('d-none');
            header.classList.add('d-flex');

            initPriceSlider();
            filterCards(true);

        } catch (err) {
            loading.classList.add('d-none');
            errorEl.textContent = 'Failed to load hotels. Please try again.';
            errorEl.classList.remove('d-none');
            paginationEl?.classList.add('d-none');
            console.error(err);
        }
    }

    function filteredCards() {
        return [...offersEl.querySelectorAll('.js-hotel-card')]
            .filter((card) => card.dataset.filteredOut !== '1');
    }

    function updateCount(totalVisible = filteredCards().length) {
        if (!countEl) return;

        if (!totalVisible) {
            countEl.textContent = `No hotels found in ${params.city ?? ''}`;
            return;
        }

        const start = ((currentPage - 1) * pageSize) + 1;
        const end = Math.min(totalVisible, currentPage * pageSize);
        countEl.textContent = `Showing ${start}-${end} of ${totalVisible} hotel${totalVisible !== 1 ? 's' : ''} in ${params.city ?? ''}`;
    }

    function pageWindow(totalPages) {
        if (totalPages <= 7) {
            return Array.from({ length: totalPages }, (_, index) => index + 1);
        }

        const pages = [1];
        const start = Math.max(2, currentPage - 1);
        const end = Math.min(totalPages - 1, currentPage + 1);

        if (start > 2) pages.push('start-ellipsis');
        for (let page = start; page <= end; page += 1) pages.push(page);
        if (end < totalPages - 1) pages.push('end-ellipsis');
        pages.push(totalPages);

        return pages;
    }

    function renderPagination() {
        if (!paginationEl) return;

        const cards = filteredCards();
        const totalVisible = cards.length;
        const totalPages = Math.max(1, Math.ceil(totalVisible / pageSize));

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        [...offersEl.querySelectorAll('.js-hotel-card')].forEach((card) => {
            card.style.display = 'none';
        });

        if (!totalVisible) {
            paginationEl.innerHTML = '';
            paginationEl.classList.add('d-none');
            updateCount(0);
            return;
        }

        cards
            .slice((currentPage - 1) * pageSize, currentPage * pageSize)
            .forEach((card) => {
                card.style.display = '';
            });

        updateCount(totalVisible);

        if (totalPages <= 1) {
            paginationEl.innerHTML = '';
            paginationEl.classList.add('d-none');
            return;
        }

        const pageButtons = pageWindow(totalPages).map((item) => {
            if (typeof item !== 'number') {
                return '<span class="hotel-pagination__ellipsis">...</span>';
            }

            return `
                <button type="button"
                        class="hotel-pagination__btn ${item === currentPage ? 'is-active' : ''}"
                        data-page="${item}">
                    ${item}
                </button>
            `;
        }).join('');

        paginationEl.innerHTML = `
            <div class="hotel-pagination">
                <button type="button" class="hotel-pagination__btn" data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}>
                    <i class="bi bi-chevron-left"></i>
                </button>
                ${pageButtons}
                <button type="button" class="hotel-pagination__btn" data-page="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''}>
                    <i class="bi bi-chevron-right"></i>
                </button>
                <span class="hotel-pagination__summary">Page ${currentPage} of ${totalPages}</span>
            </div>
        `;

        paginationEl.classList.remove('d-none');
    }

    // ── Price slider ──────────────────────────────────────
    function initPriceSlider() {
        const priceRange    = document.getElementById('priceRange');
        const priceRangeVal = document.getElementById('priceRangeVal');
        const priceRangeMin = document.getElementById('priceRangeMin');
        const cards         = [...document.querySelectorAll('.js-hotel-card')];
        const prices        = cards.map(c => normalizePrice(c.dataset.price)).filter(v => isFinite(v) && v > 0);

        if (!prices.length) {
            if (priceRangeMin) priceRangeMin.textContent = `${listingCurrency()} 0`;
            if (priceRangeVal) priceRangeVal.textContent = `${listingCurrency()} 0`;
            return;
        }

        const minP = Math.floor(Math.min(...prices));
        const maxP = Math.ceil(Math.max(...prices));

        priceRange.min   = minP;
        priceRange.max   = maxP;
        priceRange.value = maxP;

        const fmt = v => formatMoney(v, listingCurrency());
        if (priceRangeMin) priceRangeMin.textContent = fmt(minP);
        if (priceRangeVal) priceRangeVal.textContent = fmt(maxP);

        function paintSlider() {
            const mn = +priceRange.min, mx = +priceRange.max, vl = +priceRange.value;
            const pct = mx > mn ? ((vl - mn) / (mx - mn)) * 100 : 100;
            priceRange.style.background = `linear-gradient(to right,var(--hotel-theme) 0%,var(--hotel-theme) ${pct}%,#e5e7eb ${pct}%,#e5e7eb 100%)`;
        }
        paintSlider();

        priceRange.addEventListener('input', function () {
            if (priceRangeVal) priceRangeVal.textContent = fmt(+this.value);
            paintSlider();
            filterCards(true);
        });
    }

    // ── Filter cards ──────────────────────────────────────
    function filterCards(resetPage = false) {
        const priceRange    = document.getElementById('priceRange');
        const maxPrice      = priceRange ? +priceRange.value : Infinity;
        const starFilter    = document.querySelector('.star-filter:checked')?.value ?? 'all';
        const provFilter    = document.querySelector('.provider-filter:checked')?.value ?? 'all';

        document.querySelectorAll('.js-hotel-card').forEach(card => {
            const price    = normalizePrice(card.dataset.price);
            const cardStar = parseInt(card.dataset.stars ?? 0, 10);
            const prov     = card.dataset.provider ?? '';

            let show = price <= maxPrice;
            if (starFilter !== 'all') show = show && cardStar >= parseInt(starFilter, 10);
            if (provFilter !== 'all') show = show && prov === provFilter;

            card.dataset.filteredOut = show ? '0' : '1';
        });

        if (resetPage) {
            currentPage = 1;
        }

        renderPagination();
    }

    // ── Sort ──────────────────────────────────────────────
    document.getElementById('sortSelect')?.addEventListener('change', function () {
        const key    = this.value;
        const parent = offersEl;
        const cards  = [...parent.querySelectorAll('.js-hotel-card')];

        cards.sort((a, b) => {
            if (key === 'price') return normalizePrice(a.dataset.price) - normalizePrice(b.dataset.price);
            if (key === 'stars') return parseInt(b.dataset.stars) - parseInt(a.dataset.stars);
            if (key === 'name')  return a.dataset.name.localeCompare(b.dataset.name);
            return 0;
        });
        cards.forEach(c => parent.appendChild(c));
        currentPage = 1;
        renderPagination();
    });

    // ── Filter listeners ──────────────────────────────────
    document.querySelectorAll('.star-filter, .provider-filter').forEach(el => {
        el.addEventListener('change', () => filterCards(true));
    });

    paginationEl?.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-page]');
        if (!btn || btn.disabled) return;

        currentPage = Math.max(1, parseInt(btn.dataset.page ?? '1', 10) || 1);
        renderPagination();

        const top = header ? header.getBoundingClientRect().top + window.scrollY - 120 : 0;
        window.scrollTo({ top, behavior: 'smooth' });
    });

    // ── View Deal → fetch rooms via API → open modal ─────
    const roomsUrl = '{{ route('api.hotels.rooms') }}';

    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.js-view-deal');
        if (!btn) return;

        let hotel;
        try { hotel = JSON.parse(btn.dataset.hotel); } catch { return; }

        // Open modal immediately with loading spinner
        document.getElementById('modalHotelName').textContent = hotel.name;
        document.getElementById('modalBody').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-muted mt-3 small">Checking availability…</p>
            </div>`;
        const bsModal = new bootstrap.Modal(document.getElementById('hotelModal'));
        bsModal.show();

        // Local hotels already have rooms embedded from the search response — skip the API call
        if (hotel.provider !== 'local') {
            try {
                const res = await fetch(roomsUrl, {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    },
                    body: JSON.stringify({
                        offer_id:  hotel.id,
                        city_code: hotel.city ?? '',
                        check_in:  params.check_in  ?? '',
                        check_out: params.check_out ?? '',
                        adults:    parseInt(params.adults   ?? 1, 10),
                        children:  parseInt(params.children ?? 0, 10),
                        provider:  hotel.provider,
                    }),
                });
                const data = await res.json();
                hotel.rooms = (data.success && data.data?.length) ? data.data : (hotel.rooms ?? []);
            } catch {
                // keep whatever rooms were in the search result
            }
        }

        renderModal(hotel);
    });

    // ── Select Room → prebook API → checkout ─────────────
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.js-select-room');
        if (!btn) return;

        btn.disabled    = true;
        btn.textContent = 'Please wait…';

        const payload = {
            offer_id:   btn.dataset.offerId,
            room_id:    btn.dataset.roomId,
            provider:   btn.dataset.provider,
            hotel_name: btn.dataset.hotelName,
            room_name:  btn.dataset.roomName,
            city:       btn.dataset.city,
            country:    btn.dataset.country,
            check_in:   btn.dataset.checkIn,
            check_out:  btn.dataset.checkOut,
            adults:     parseInt(btn.dataset.adults ?? 1, 10),
            children:   parseInt(btn.dataset.children ?? 0, 10),
            currency:   'USD',
        };

        try {
            const res  = await fetch(prebookUrl, {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify(payload),
            });

            const data = await res.json();

            if (data.success && data.checkout_url) {
                window.location.href = data.checkout_url;
                return;
            }

            alert(data.message ?? 'Could not select this room. Please try again.');
        } catch {
            alert('Network error. Please try again.');
        }

        btn.disabled    = false;
        btn.textContent = 'Select Room';
    });

    loadHotels();

})();
</script>
@endpush
@endif
