@extends('layouts.master')

@section('title', 'Homes & Apartments – Travolyo')

@push('styles')
<style>
/* ── Homes listing page ─────────────────────────────── */
.homes-page-bg { background: #f4f6fb; min-height: 60vh; }

/* Filter card */
.filter-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 6px rgba(0,0,0,.07); }
.filter-card .filter-title { font-size: .7rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d; margin-bottom: 12px; }
.filter-divider { border-top: 1px solid #eef0f4; margin: 16px 0; }

/* Price slider */
.price-slider-wrap input[type=range] {
    -webkit-appearance: none; width: 100%; height: 4px;
    border-radius: 2px; outline: none; cursor: pointer;
    background: linear-gradient(to right, var(--bs-primary) 100%, #e5e7eb 100%);
}
.price-slider-wrap input[type=range]::-webkit-slider-thumb {
    -webkit-appearance: none; width: 16px; height: 16px;
    border-radius: 50%; background: var(--bs-primary); cursor: pointer;
    box-shadow: 0 0 0 3px rgba(13,110,253,.15);
}
.filter-pill { display: flex; align-items: center; gap: 8px; padding: 7px 10px; border-radius: 8px; cursor: pointer; transition: background .15s; font-size: .875rem; }
.filter-pill:hover { background: #f0f4ff; }
.filter-pill input { accent-color: var(--bs-primary); }

/* Results header */
.results-header { border-bottom: 1px solid #eef0f4; padding-bottom: 12px; margin-bottom: 16px; }
.sort-select { border: 1px solid #dee2e6; border-radius: 6px; padding: 4px 10px; font-size: .85rem; color: #495057; background: #fff; cursor: pointer; }

/* Space card */
.space-card {
    background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(0,0,0,.07);
    margin-bottom: 12px; overflow: hidden; transition: box-shadow .2s, transform .2s;
    display: flex;
}
.space-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,.12); transform: translateY(-1px); }
.space-card__img {
    width: 220px; min-width: 220px; height: 165px;
    object-fit: cover; flex-shrink: 0;
}
.space-card__img-placeholder {
    width: 220px; min-width: 220px; height: 165px;
    background: #f0f4ff; display: flex; align-items: center; justify-content: center;
    color: #a0aec0; font-size: 2rem; flex-shrink: 0;
}
.space-card__body { padding: 16px 20px; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
.space-card__name { font-size: 1.05rem; font-weight: 700; color: #1a2942; margin-bottom: 4px; }
.space-card__addr { font-size: .8rem; color: #6c757d; margin-bottom: 8px; }
.space-card__meta { font-size: .8rem; color: #6c757d; }
.space-card__amenities { font-size: .75rem; color: #6c757d; }
.space-card__price { font-size: 1.3rem; font-weight: 700; color: var(--bs-primary); }
.space-card__night { font-size: .75rem; color: #6c757d; }
.space-card__badge { font-size: .65rem; font-weight: 600; padding: 2px 8px; border-radius: 20px; }

/* Skeleton */
.skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 8px; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

@media (max-width: 576px) {
    .space-card { flex-direction: column; }
    .space-card__img, .space-card__img-placeholder { width: 100%; min-width: unset; height: 160px; }
}
</style>
@endpush

@section('content')

@php
    $searchKeyword = trim((string) ($params['city'] ?: $params['destination']));
@endphp

@include('website.partials._search-section', ['activeTab' => 'homes'])

<div class="homes-page-bg py-4">

@if(session('error'))
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif
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
            <div class="filter-title">Type</div>
            <label class="filter-pill">
                <input type="radio" name="typeFilter" class="type-filter" value="all" checked> Any
            </label>
            <label class="filter-pill">
                <input type="radio" name="typeFilter" class="type-filter" value="apartment"> Apartment
            </label>
            <label class="filter-pill">
                <input type="radio" name="typeFilter" class="type-filter" value="house"> House
            </label>
            <label class="filter-pill">
                <input type="radio" name="typeFilter" class="type-filter" value="villa"> Villa
            </label>
            <label class="filter-pill">
                <input type="radio" name="typeFilter" class="type-filter" value="studio"> Studio
            </label>
            <label class="filter-pill">
                <input type="radio" name="typeFilter" class="type-filter" value="room"> Room
            </label>

        </div>
    </div>

    {{-- ── Results column ─────────────────────────────── --}}
    <div class="col-12 col-lg-9">

        @if(empty($searchKeyword))
            <div class="filter-card text-center py-5">
                <i class="bi bi-house-door text-muted" style="font-size:3rem"></i>
                <p class="text-muted mt-3 mb-0">Enter a destination above and click <strong>Search Homes</strong>.</p>
            </div>
        @else

            {{-- Results header --}}
            <div class="results-header d-none d-flex justify-content-between align-items-center" id="results-header">
                <div>
                    <h6 class="mb-0 fw-semibold">Available Homes & Apartments</h6>
                    <p class="text-muted small mb-0" id="results-count"></p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-down-up text-muted small"></i>
                    <span class="text-muted small">Sort:</span>
                    <select class="sort-select" id="sortSelect">
                        <option value="price">Price</option>
                        <option value="name">Name</option>
                        <option value="newest">Newest</option>
                    </select>
                </div>
            </div>

            {{-- Skeleton loader --}}
            <div id="space-loading">
                @for($i = 0; $i < 4; $i++)
                <div class="space-card mb-3" style="border-radius:12px;overflow:hidden;">
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

            {{-- Space cards rendered by JS --}}
            <div id="space-offers" class="d-none"></div>
            <div id="space-error"  class="d-none alert alert-danger rounded-3"></div>

            {{-- Load More button --}}
            <div id="load-more-wrap" class="d-none text-center mt-3 mb-4">
                <button class="btn btn-outline-primary px-4 py-2 fw-semibold" id="load-more-btn">
                    <span id="load-more-text">Load More</span>
                    <span id="load-more-spinner" class="d-none spinner-border spinner-border-sm ms-2" role="status"></span>
                </button>
                <p class="text-muted small mt-2" id="pagination-info"></p>
            </div>

        @endif

    </div>
</div>
</div>
</div>

@endsection

@if(!empty($searchKeyword))
@push('scripts')
<script>
(function () {

    const params        = @json($params);
    const searchKeyword = @json($searchKeyword);
    const searchUrl     = '{{ route('api.spaces.search') }}';
    const loading       = document.getElementById('space-loading');
    const offersEl      = document.getElementById('space-offers');
    const errorEl       = document.getElementById('space-error');
    const header        = document.getElementById('results-header');
    const countEl       = document.getElementById('results-count');

    let allSpaces   = [];
    let currentPage = 1;
    let lastPage    = 1;
    let totalSpaces = 0;
    let isLoading   = false;

    // ── Type badge ──────────────────────────────────────
    function typeBadge(t) {
        const map = {
            apartment: { label: 'Apartment', bg: '#e0f2fe', color: '#0369a1' },
            house:     { label: 'House',     bg: '#dcfce7', color: '#15803d' },
            villa:     { label: 'Villa',     bg: '#fef3c7', color: '#b45309' },
            studio:    { label: 'Studio',    bg: '#f3e8ff', color: '#7c3aed' },
            room:      { label: 'Room',      bg: '#f3f4f6', color: '#6b7280' },
        };
        const m = map[t] ?? { label: t || 'Space', bg: '#f3f4f6', color: '#6b7280' };
        return `<span class="space-card__badge" style="background:${m.bg};color:${m.color}">${m.label}</span>`;
    }

    // ── Render one space card ───────────────────────────
    function renderCard(s) {
        const img = (s.images && s.images[0])
            ? `<img class="space-card__img" src="${s.images[0]}" alt="${s.name}" loading="lazy">`
            : `<div class="space-card__img-placeholder"><i class="bi bi-house-door"></i></div>`;

        const amenities = (s.amenities ?? []).slice(0, 4).join(' · ');
        const meta = [
            s.bedrooms ? `${s.bedrooms} bed${s.bedrooms > 1 ? 's' : ''}` : '',
            s.bathrooms ? `${s.bathrooms} bath${s.bathrooms > 1 ? 's' : ''}` : '',
            s.max_guests ? `up to ${s.max_guests} guests` : '',
        ].filter(Boolean).join(' · ');

        const price    = parseFloat(s.converted_price_per_night ?? s.base_price_per_night ?? 0);
        const currency = s.converted_currency ?? s.base_currency ?? 'USD';

        return `
        <div class="space-card js-space-card"
             data-price="${price}"
             data-type="${s.type}"
             data-name="${(s.name ?? '').toLowerCase()}">
            ${img}
            <div class="space-card__body">
                <div>
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div class="space-card__name">${s.name}</div>
                        <div class="text-end flex-shrink-0">${typeBadge(s.type)}</div>
                    </div>
                    <div class="space-card__addr">
                        <i class="bi bi-geo-alt me-1"></i>${s.city ?? s.address ?? ''}${s.country ? ', ' + s.country : ''}
                    </div>
                    ${meta ? `<div class="space-card__meta mb-1"><i class="bi bi-layout-text-window me-1"></i>${meta}</div>` : ''}
                    ${amenities ? `<div class="space-card__amenities"><i class="bi bi-check2-circle me-1 text-success"></i>${amenities}</div>` : ''}
                </div>
                <div class="d-flex align-items-center justify-content-between mt-3">
                    <div>
                        ${price > 0
                            ? `<div class="space-card__price">${currency} ${price.toLocaleString('en-US', {minimumFractionDigits:0})}</div>
                               <div class="space-card__night">per night</div>`
                            : `<div class="space-card__price" style="font-size:.85rem;color:#6c757d">View for pricing</div>`}
                    </div>
                    <button class="btn btn-primary btn-select-room js-view-space" data-space-id="${s.id}">
                        View Details <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>`;
    }

    // ── Pagination elements ─────────────────────────────
    const loadMoreWrap    = document.getElementById('load-more-wrap');
    const loadMoreBtn     = document.getElementById('load-more-btn');
    const loadMoreText    = document.getElementById('load-more-text');
    const loadMoreSpinner = document.getElementById('load-more-spinner');
    const paginationInfo  = document.getElementById('pagination-info');

    function updatePaginationUI() {
        if (currentPage < lastPage) {
            loadMoreWrap.classList.remove('d-none');
            paginationInfo.textContent = `Showing ${allSpaces.length} of ${totalSpaces} results`;
        } else {
            loadMoreWrap.classList.add('d-none');
        }
    }

    // ── Fetch spaces ────────────────────────────────────
    async function loadSpaces(page = 1) {
        if (isLoading) return;
        isLoading = true;

        if (page > 1) {
            loadMoreText.textContent = 'Loading…';
            loadMoreSpinner.classList.remove('d-none');
            loadMoreBtn.disabled = true;
        }

        try {
            const res = await fetch(searchUrl, {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify({
                    destination: params.destination || params.city,
                    city:        params.city,
                    check_in:    params.check_in,
                    check_out:   params.check_out,
                    guests:      parseInt(params.guests ?? params.adults ?? 1, 10),
                    page:        page,
                    currency:    document.querySelector('meta[name="currency"]')?.content ?? 'USD',
                }),
            });

            const data = await res.json();
            loading.classList.add('d-none');

            if (!res.ok) {
                if (page === 1) {
                    const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : `Server error (${res.status})`);
                    errorEl.textContent = msg;
                    errorEl.classList.remove('d-none');
                }
                isLoading = false;
                return;
            }

            if (!data.success || !data.data?.length) {
                if (page === 1) {
                    errorEl.textContent = data.message ?? 'No homes or apartments found for your search.';
                    errorEl.classList.remove('d-none');
                }
                isLoading = false;
                return;
            }

            currentPage = data.current_page;
            lastPage    = data.last_page;
            totalSpaces = data.total;

            allSpaces = allSpaces.concat(data.data);

            const newHtml = data.data.map(s => renderCard(s)).join('');
            if (page === 1) {
                offersEl.innerHTML = newHtml;
            } else {
                offersEl.insertAdjacentHTML('beforeend', newHtml);
            }
            offersEl.classList.remove('d-none');

            header.classList.remove('d-none');
            header.classList.add('d-flex');
            updateCount();
            updatePaginationUI();

            initPriceSlider();
            filterCards();

        } catch (err) {
            loading.classList.add('d-none');
            if (page === 1) {
                errorEl.textContent = 'Failed to load results. Please try again.';
                errorEl.classList.remove('d-none');
            }
            console.error(err);
        } finally {
            isLoading = false;
            loadMoreText.textContent = 'Load More';
            loadMoreSpinner.classList.add('d-none');
            loadMoreBtn.disabled = false;
        }
    }

    // ── Load More click ─────────────────────────────────
    loadMoreBtn?.addEventListener('click', () => {
        if (currentPage < lastPage) {
            loadSpaces(currentPage + 1);
        }
    });

    function updateCount() {
        const visible = offersEl.querySelectorAll('.js-space-card:not([style*="none"])').length;
        const totalStr = totalSpaces ? ` of ${totalSpaces}` : '';
        countEl.textContent = `Showing ${visible}${totalStr} result${visible !== 1 ? 's' : ''} in ${searchKeyword}`;
    }

    // ── Price slider ─────────────────────────────────────
    function initPriceSlider() {
        const priceRange    = document.getElementById('priceRange');
        const priceRangeVal = document.getElementById('priceRangeVal');
        const priceRangeMin = document.getElementById('priceRangeMin');
        const cards         = [...document.querySelectorAll('.js-space-card')];
        const prices        = cards.map(c => parseFloat(c.dataset.price)).filter(v => isFinite(v));

        if (!prices.length) return;

        const minP = Math.floor(Math.min(...prices));
        const maxP = Math.ceil(Math.max(...prices));

        const oldMax    = +priceRange.max || 0;
        const wasAtMax  = +priceRange.value >= oldMax || oldMax === 0;

        priceRange.min   = minP;
        priceRange.max   = maxP;
        priceRange.value = wasAtMax ? maxP : priceRange.value;

        const fmt = v => `$${Math.round(v).toLocaleString()}`;
        if (priceRangeMin) priceRangeMin.textContent = fmt(minP);
        if (priceRangeVal) priceRangeVal.textContent = fmt(maxP);

        function paintSlider() {
            const mn = +priceRange.min, mx = +priceRange.max, vl = +priceRange.value;
            const pct = mx > mn ? ((vl - mn) / (mx - mn)) * 100 : 100;
            priceRange.style.background = `linear-gradient(to right,var(--bs-primary) 0%,var(--bs-primary) ${pct}%,#e5e7eb ${pct}%,#e5e7eb 100%)`;
        }
        paintSlider();

        priceRange.addEventListener('input', function () {
            if (priceRangeVal) priceRangeVal.textContent = fmt(+this.value);
            paintSlider();
            filterCards();
        });
    }

    // ── Filter cards ─────────────────────────────────────
    function filterCards() {
        const priceRange  = document.getElementById('priceRange');
        const maxPrice    = priceRange ? +priceRange.value : Infinity;
        const typeFilter  = document.querySelector('.type-filter:checked')?.value ?? 'all';

        document.querySelectorAll('.js-space-card').forEach(card => {
            const price = parseFloat(card.dataset.price);
            const type  = card.dataset.type ?? '';

            let show = price <= maxPrice;
            if (typeFilter !== 'all') show = show && type === typeFilter;

            card.style.display = show ? '' : 'none';
        });

        updateCount();
    }

    // ── Sort ─────────────────────────────────────────────
    document.getElementById('sortSelect')?.addEventListener('change', function () {
        const key    = this.value;
        const parent = offersEl;
        const cards  = [...parent.querySelectorAll('.js-space-card')];

        cards.sort((a, b) => {
            if (key === 'price') return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
            if (key === 'name')  return a.dataset.name.localeCompare(b.dataset.name);
            return 0;
        });
        cards.forEach(c => parent.appendChild(c));
    });

    // ── Filter listeners ─────────────────────────────────
    document.querySelectorAll('.type-filter').forEach(el => {
        el.addEventListener('change', filterCards);
    });

    // ── View Details → navigate to /homes/detail page ───
    const detailPageUrl = '{{ route('homes.detail') }}';

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-view-space');
        if (!btn) return;
        e.preventDefault();

        const spaceId = btn.dataset.spaceId;
        const url = new URL(detailPageUrl, window.location.origin);
        url.searchParams.set('space_id',  spaceId);
        url.searchParams.set('check_in',  params.check_in ?? '');
        url.searchParams.set('check_out', params.check_out ?? '');
        url.searchParams.set('guests',    params.guests ?? 1);
        url.searchParams.set('currency',  document.querySelector('meta[name="currency"]')?.content ?? 'USD');

        window.location.href = url.toString();
    });

    // ── Auto-search on load ──────────────────────────────
    loadSpaces(1);

})();
</script>
@endpush
@endif
