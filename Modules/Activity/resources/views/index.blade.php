@extends('layouts.master')

@section('title', 'Activities')

@push('styles')
<style>
.activity-page-bg { background: #f4f6fb; min-height: 60vh; }

/* Skeleton */
.skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 8px; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* Sort */
.sort-select { border: 1px solid #dee2e6; border-radius: 6px; padding: 4px 10px; font-size: .85rem; color: #495057; background: #fff; cursor: pointer; }
.results-header { border-bottom: 1px solid #eef0f4; padding-bottom: 12px; margin-bottom: 16px; }
</style>
@endpush

@section('content')
@include('website.partials._search-section', ['activeTab' => 'activities'])

<div class="activity-page-bg py-4">
<div class="container">
<div class="row g-4">

    {{-- Filters sidebar --}}
    <div class="col-12 col-lg-3">
        @include('activity::partials._filters')
    </div>

    {{-- Results column --}}
    <div class="col-12 col-lg-9">

        {{-- Results header --}}
        <div class="results-header d-flex justify-content-between align-items-center d-none" id="results-header">
            <div>
                <h5 class="mb-0 fw-semibold">Available Activities</h5>
                <p class="text-muted small mb-0" id="results-count"></p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-arrow-down-up text-muted small"></i>
                <span class="text-muted small">Sort:</span>
                <select class="sort-select" id="sortSelect">
                    <option value="recommended">Recommended</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                </select>
            </div>
        </div>

        {{-- Skeleton loader --}}
        <div id="activity-loading">
            @for ($i = 0; $i < 4; $i++)
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

        {{-- Activity cards rendered by JS --}}
        <div id="activity-offers" class="d-none"></div>
        <div id="activity-error"  class="d-none alert alert-danger rounded-3"></div>

        {{-- Load More --}}
        <div id="load-more-wrap" class="d-none text-center mt-3 mb-4">
            <button class="btn btn-outline-primary px-4 py-2 fw-semibold" id="load-more-btn">
                <span id="load-more-text">Load More Activities</span>
                <span id="load-more-spinner" class="d-none spinner-border spinner-border-sm ms-2" role="status"></span>
            </button>
            <p class="text-muted small mt-2" id="pagination-info"></p>
        </div>

    </div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const params      = @json($params);
    const searchUrl   = '{{ route('api.activities.search') }}';
    const csrfToken   = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const currency    = '{{ session('currency', config('currency.default', 'AED')) }}';
    const activitiesBase = '{{ url('/activities') }}';

    const loadingEl  = document.getElementById('activity-loading');
    const offersEl   = document.getElementById('activity-offers');
    const errorEl    = document.getElementById('activity-error');
    const headerEl   = document.getElementById('results-header');
    const countEl    = document.getElementById('results-count');
    const loadMoreWrap = document.getElementById('load-more-wrap');
    const loadMoreBtn  = document.getElementById('load-more-btn');
    const loadMoreText = document.getElementById('load-more-text');
    const loadMoreSpinner = document.getElementById('load-more-spinner');
    const paginationInfo = document.getElementById('pagination-info');
    const sortSelect  = document.getElementById('sortSelect');

    let currentPage = 1;
    let lastPage    = 1;
    let totalItems  = 0;
    let isLoading   = false;
    let currentSort = params.sort_by || 'recommended';

    // Sync sort select with current URL param
    if (sortSelect) sortSelect.value = currentSort;

    // ── Build checkout URL ───────────────────────────────
    function checkoutUrl(activity) {
        const slug   = activity.slug || activity.id;
        const date   = params.activity_date || '';
        const pax    = params.participants  || 1;
        const city   = params.city          || '';
        return `${activitiesBase}/${slug}/checkout?date=${date}&participants=${pax}&city=${encodeURIComponent(city)}`;
    }

    // ── Render one activity card ─────────────────────────
    function renderCard(a) {
        const img = a.image_url
            ? `<img class="hotel-card__img" src="${a.image_url}" alt="${a.title}" loading="lazy" style="width:220px;min-width:220px;height:165px;object-fit:cover;flex-shrink:0">`
            : `<div style="width:220px;min-width:220px;height:165px;background:#f0f4ff;display:flex;align-items:center;justify-content:center;color:#a0aec0;font-size:2rem;flex-shrink:0"><i class="bi bi-camera"></i></div>`;

        const tags = [
            a.category             ? `<span class="amenity-tag">${a.category}</span>` : '',
            a.duration             ? `<span class="amenity-tag"><i class="bi bi-clock me-1"></i>${a.duration}</span>` : '',
            a.instant_confirmation ? `<span class="amenity-tag"><i class="bi bi-check2-circle me-1"></i>Instant</span>` : '',
        ].filter(Boolean).join('');

        const price    = parseFloat(a.converted_price_per_person || a.base_price_per_person || 0);
        const cur      = a.converted_currency || a.base_currency || currency;
        const baseCur  = a.base_currency || '';
        const basePrice = parseFloat(a.base_price_per_person || 0);
        const showBase  = baseCur && baseCur !== cur;

        const priceHtml = showBase
            ? `<div class="d-flex align-items-baseline gap-2 flex-wrap">
                   <span class="price-current">${cur} ${price.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</span>
                   <span class="text-muted small">(${baseCur} ${basePrice.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})})</span>
               </div>`
            : `<span class="price-current">${cur} ${price.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</span>`;

        return `
        <div class="hotel-card mb-3">
            ${img}
            <div class="hotel-card__body">
                <div class="hotel-card__top">
                    <div class="hotel-card__info">
                        <h6 class="hotel-card__name">${a.title}</h6>
                        <p class="hotel-card__location">
                            <i class="bi bi-geo-alt-fill me-1"></i>${a.city || '-'}, ${a.country || '-'}
                        </p>
                        <div class="hotel-card__tags">${tags}</div>
                    </div>
                </div>
                <div class="hotel-card__price-row">
                    <div class="hotel-card__price">
                        <span class="price-per-night">Per person</span>
                        ${priceHtml}
                    </div>
                    <a href="${checkoutUrl(a)}" class="btn btn-view-deal">View Deal</a>
                </div>
            </div>
        </div>`;
    }

    // ── Fetch activities from API ────────────────────────
    async function fetchActivities(page, append) {
        if (isLoading) return;
        isLoading = true;

        if (!append) {
            loadingEl.classList.remove('d-none');
            offersEl.classList.add('d-none');
            errorEl.classList.add('d-none');
            headerEl.classList.add('d-none');
            loadMoreWrap.classList.add('d-none');
            offersEl.innerHTML = '';
        } else {
            loadMoreText.classList.add('d-none');
            loadMoreSpinner.classList.remove('d-none');
        }

        const body = {
            destination:          params.destination || params.city || params.country || '',
            currency:             currency,
            sort_by:              currentSort,
            per_page:             12,
            page:                 page,
        };
        if (params.category)             body.category             = params.category;
        if (params.price_max)            body.price_max            = params.price_max;
        if (params.instant_confirmation) body.instant_confirmation = true;
        if (params.activity_date)        body.activity_date        = params.activity_date;
        if (params.participants)         body.participants         = params.participants;

        try {
            const resp = await fetch(searchUrl, {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept':       'application/json',
                },
                body: JSON.stringify(body),
            });

            const data = await resp.json();

            if (!data.success) {
                throw new Error(data.message || 'Search failed.');
            }

            const offers     = data.data ?? [];
            const pagination = data.pagination ?? {};
            lastPage  = pagination.last_page  ?? 1;
            totalItems = pagination.total     ?? offers.length;
            currentPage = pagination.current_page ?? page;

            loadingEl.classList.add('d-none');

            if (offers.length === 0 && !append) {
                offersEl.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x text-muted" style="font-size:3rem;"></i>
                        <h5 class="mt-3 text-muted">No activities found</h5>
                        <p class="text-muted small">Try adjusting your filters or search in a different city.</p>
                    </div>`;
            } else {
                offers.forEach(a => {
                    offersEl.insertAdjacentHTML('beforeend', renderCard(a));
                });
            }

            offersEl.classList.remove('d-none');
            headerEl.classList.remove('d-none');
            countEl.textContent = `Showing ${offersEl.querySelectorAll('.hotel-card').length} of ${totalItems} activities`;

            // Load More
            if (currentPage < lastPage) {
                loadMoreWrap.classList.remove('d-none');
                paginationInfo.textContent = `Page ${currentPage} of ${lastPage}`;
            } else {
                loadMoreWrap.classList.add('d-none');
            }

        } catch (err) {
            loadingEl.classList.add('d-none');
            errorEl.textContent = err.message || 'Could not load activities. Please try again.';
            errorEl.classList.remove('d-none');
        } finally {
            isLoading = false;
            loadMoreText.classList.remove('d-none');
            loadMoreSpinner.classList.add('d-none');
        }
    }

    // ── Sort change (reload) ─────────────────────────────
    if (sortSelect) {
        sortSelect.addEventListener('change', () => {
            currentSort = sortSelect.value;
            currentPage = 1;
            fetchActivities(1, false);
        });
    }

    // ── Load More ────────────────────────────────────────
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', () => {
            if (currentPage < lastPage) {
                fetchActivities(currentPage + 1, true);
            }
        });
    }

    // ── Initial load ─────────────────────────────────────
    fetchActivities(1, false);

})();
</script>
@endpush