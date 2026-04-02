@extends('layouts.master')

@section('title', 'Flight Results – Travolyo')

@push('styles')
<style>
/* ── Flight page ─────────────────────────────────── */
.flight-page-bg {
    background:
        /* radial-gradient(circle at top left, rgba(23, 195, 206, .08), transparent 28%),
        linear-gradient(180deg, #f8fbfd 0%, #f1f5fb 100%); */
    min-height: 60vh;
}
:root {
    --flight-theme: var(--primary, #17c3ce);
    --flight-theme-dark: #1099a6;
    --flight-ink: #12314d;
    --flight-muted: #6b7a90;
    --flight-line: #d9e4ef;
}

/* Filters card */
.filter-card { background: #fff; border-radius: 18px; padding: 20px; box-shadow: 0 10px 32px rgba(18,38,63,.08); }
.filter-card .filter-title { font-size: .7rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d; margin-bottom: 12px; }
.filter-divider { border-top: 1px solid #eef0f4; margin: 16px 0; }

/* Price slider */
.price-slider-wrap input[type=range] {
    -webkit-appearance: none; width: 100%; height: 4px;
    border-radius: 2px; outline: none; cursor: pointer;
    background: linear-gradient(to right, var(--flight-theme) 100%, #e5e7eb 100%);
}
.price-slider-wrap input[type=range]::-webkit-slider-thumb {
    -webkit-appearance: none; width: 16px; height: 16px;
    border-radius: 50%; background: var(--flight-theme); cursor: pointer;
    box-shadow: 0 0 0 3px rgba(23, 195, 206, .15);
}
.price-slider-wrap input[type=range]::-moz-range-thumb {
    width: 16px; height: 16px; border-radius: 50%;
    background: var(--flight-theme); cursor: pointer; border: none;
}

/* Stop / time filter pills */
.filter-pill { display: flex; align-items: center; gap: 8px; padding: 7px 10px; border-radius: 8px; cursor: pointer; transition: background .15s; font-size: .875rem; }
.filter-pill:hover { background: #f0f4ff; }
.filter-pill input[type=radio] { accent-color: var(--flight-theme); }

/* Results header */
.results-header { border-bottom: 1px solid #eef0f4; padding-bottom: 12px; margin-bottom: 16px; }
.sort-select { border: 1px solid #dee2e6; border-radius: 10px; padding: 6px 12px; font-size: .85rem; color: #495057; background: #fff; cursor: pointer; }

/* Flight card */
.flight-card {
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    border: 1px solid #dde7f2;
    border-radius: 22px;
    box-shadow: 0 16px 38px rgba(18,38,63,.08);
    margin-bottom: 14px;
    overflow: hidden;
    transition: box-shadow .2s, transform .2s, border-color .2s;
}
.flight-card:hover {
    border-color: rgba(23, 195, 206, .28);
    box-shadow: 0 22px 48px rgba(18,38,63,.12);
    transform: translateY(-2px);
}
.flight-card__body { padding: 22px; }
.flight-card__layout {
    align-items: center;
    display: grid;
    gap: 18px;
    grid-template-columns: 146px minmax(0, 1fr) 132px;
}
.flight-card__badge {
    font-size: .62rem;
    font-weight: 700;
    letter-spacing: .04em;
    padding: 4px 9px;
    border-radius: 999px;
}

/* Airline logo col */
.airline-col { display: flex; flex-direction: column; gap: 12px; }
.airline-meta { display: flex; flex-direction: column; gap: 10px; }
.airline-logo-wrap {
    width: 54px; height: 54px; border-radius: 16px; background: #f4f7fb;
    display: flex; align-items: center; justify-content: center;
}
.airline-logo { height: 30px; max-width: 30px; object-fit: contain; }
.airline-brand { color: var(--flight-ink); font-size: .94rem; font-weight: 800; line-height: 1.15; }
.airline-flight-no { color: var(--flight-muted); font-size: .8rem; }
.airline-provider-row { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }

/* Leg */
.flight-itinerary { display: flex; flex-direction: column; gap: 12px; min-width: 0; }
.flight-itinerary__segment {
    display: grid;
    gap: 16px;
    grid-template-columns: minmax(78px, 94px) minmax(0, 1fr) minmax(78px, 94px);
    align-items: center;
}
.flight-itinerary__chip {
    background: rgba(23, 195, 206, .12);
    color: var(--flight-theme-dark);
    border-radius: 999px;
    display: inline-flex;
    font-size: .58rem;
    font-weight: 800;
    letter-spacing: .1em;
    margin-bottom: 6px;
    padding: 3px 8px;
    text-transform: uppercase;
}
.leg-point { min-width: 0; }
.leg-point--arrival { text-align: right; }
.leg-time { color: var(--flight-ink); font-size: 1.45rem; font-weight: 800; line-height: 1; }
.leg-iata { font-size: .9rem; color: var(--flight-ink); font-weight: 700; margin-top: 6px; }
.leg-date { font-size: .78rem; color: #8ea0b4; margin-top: 4px; }
.flight-path { text-align: center; min-width: 0; }
.flight-path__line { display: flex; align-items: center; gap: 10px; justify-content: center; margin-bottom: 8px; }
.flight-path__dot { width: 8px; height: 8px; border-radius: 50%; background: #91a5bd; flex: 0 0 8px; }
.flight-path__dash { flex: 1 1 auto; min-width: 24px; border-top: 2px dashed var(--flight-line); }
.flight-path__plane { color: #879bb1; font-size: .82rem; display: inline-flex; align-items: center; justify-content: center; }
.flight-path__meta { display: flex; flex-direction: column; gap: 3px; color: var(--flight-muted); }
.flight-path__duration { color: var(--flight-ink); font-size: .92rem; font-weight: 700; }
.flight-path__stops { font-size: .82rem; font-weight: 600; }
.stops-direct { color: #16a34a; }
.stops-multi  { color: #d97706; }

/* Price col */
.price-col {
    align-items: flex-end;
    display: flex;
    flex-direction: column;
    gap: 6px;
    justify-self: end;
    text-align: right;
    width: 118px;
}
.price-from {
    color: var(--flight-muted);
    font-size: .72rem;
    font-weight: 600;
    letter-spacing: .02em;
}
.price-amount {
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--flight-theme);
    line-height: 1;
    letter-spacing: -.03em;
}
.price-cabin  {
    font-size: .66rem;
    color: #6c757d;
    margin-top: 0;
    letter-spacing: .04em;
    text-transform: uppercase;
}
.btn-select {
    background: var(--flight-theme);
    border: none;
    border-radius: 14px;
    box-shadow: 0 10px 20px rgba(23, 195, 206, .16);
    font-size: .82rem;
    font-weight: 700;
    padding: 9px 0;
    width: 106px;
}
.btn-select:hover,
.btn-select:focus { background: var(--flight-theme-dark); }

/* Skeleton */
.skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 8px; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

@media (max-width: 1199.98px) {
    .flight-card__layout { grid-template-columns: 138px minmax(0, 1fr) 122px; }
    .leg-time { font-size: 1.32rem; }
}

@media (max-width: 991.98px) {
    .flight-card__layout { grid-template-columns: 1fr; }
    .price-col { align-items: flex-start; justify-self: stretch; text-align: left; width: 100%; }
    .btn-select { width: 100%; }
}

@media (max-width: 575.98px) {
    .flight-card__body { padding: 18px; }
    .flight-itinerary__segment { grid-template-columns: 1fr; gap: 10px; }
    .leg-point, .leg-point--arrival, .flight-path { text-align: left; }
    .flight-path__line { justify-content: flex-start; }
    .leg-time { font-size: 1.2rem; }
    .price-amount { font-size: 1.08rem; }
}
</style>
@endpush

@section('content')

@include('website.partials._search-section', ['activeTab' => 'flights'])

<div class="flight-page-bg py-4">
<div class="container">
<div class="row g-4">

    {{-- ── Filters sidebar ───────────────────────────── --}}
    <div class="col-12 col-lg-3" id="filters-col">
        <div class="filter-card">

            <div class="filter-title">Price</div>
            <div class="price-slider-wrap">
                <input type="range" id="priceRange" min="0" max="9999" value="9999" step="1">
            </div>
            <div class="d-flex justify-content-between mt-2 small text-muted">
                <span id="priceRangeMin">—</span>
                <span>Up to <strong id="priceRangeVal">—</strong></span>
            </div>

            <div class="filter-divider"></div>
            <div class="filter-title">Stops</div>
            <label class="filter-pill">
                <input type="radio" name="stopFilter" class="stop-filter" value="all" checked> Any
            </label>
            <label class="filter-pill">
                <input type="radio" name="stopFilter" class="stop-filter" value="0"> Direct only
            </label>
            <label class="filter-pill">
                <input type="radio" name="stopFilter" class="stop-filter" value="1"> 1 Stop
            </label>
            <label class="filter-pill">
                <input type="radio" name="stopFilter" class="stop-filter" value="2"> 2+ Stops
            </label>

            <div class="filter-divider"></div>
            <div class="filter-title">Departure Time</div>
            <label class="filter-pill">
                <input type="radio" name="timeFilter" class="dep-time-filter" value="any" checked> Any time
            </label>
            <label class="filter-pill">
                <input type="radio" name="timeFilter" class="dep-time-filter" value="morning">
                <span><i class="bi bi-sunrise me-1 text-warning"></i>Morning <small class="text-muted">06–12</small></span>
            </label>
            <label class="filter-pill">
                <input type="radio" name="timeFilter" class="dep-time-filter" value="afternoon">
                <span><i class="bi bi-sun me-1 text-warning"></i>Afternoon <small class="text-muted">12–18</small></span>
            </label>
            <label class="filter-pill">
                <input type="radio" name="timeFilter" class="dep-time-filter" value="evening">
                <span><i class="bi bi-moon-stars me-1 text-primary"></i>Evening <small class="text-muted">18+</small></span>
            </label>

        </div>
    </div>

    {{-- ── Results column ─────────────────────────────── --}}
    <div class="col-12 col-lg-9">

        @if(empty($params['origin']))
            <div class="filter-card text-center py-5">
                <i class="bi bi-airplane text-muted" style="font-size:3rem"></i>
                <p class="text-muted mt-3">Enter your route above and click <strong>Search Flights</strong>.</p>
            </div>
        @else

            {{-- Results header (hidden until loaded) --}}
            <div class="results-header d-none d-flex justify-content-between align-items-center" id="results-header">
                <div>
                    <h6 class="mb-0 fw-semibold">Available Flights</h6>
                    <p class="text-muted small mb-0" id="results-count"></p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-down-up text-muted small"></i>
                    <span class="text-muted small">Sort:</span>
                    <select class="sort-select" id="sortSelect">
                        <option value="price">Price</option>
                        <option value="duration">Duration</option>
                        <option value="dep_time">Departure</option>
                        <option value="arr_time">Arrival</option>
                    </select>
                </div>
            </div>

            {{-- Skeleton loader --}}
            <div id="flight-loading">
                @for($i = 0; $i < 4; $i++)
                <div class="flight-card mb-3">
                    <div class="flight-card__body">
                        <div class="flight-card__layout">
                            <div>
                                <div class="skeleton mb-3" style="height:26px;width:84px;border-radius:999px"></div>
                                <div class="skeleton mb-3" style="width:54px;height:54px;border-radius:16px"></div>
                                <div class="skeleton mb-2" style="height:16px;width:102px"></div>
                                <div class="skeleton" style="height:13px;width:76px"></div>
                            </div>
                            <div>
                                <div class="skeleton mb-3" style="height:14px;width:72px;border-radius:999px"></div>
                                <div class="skeleton mb-3" style="height:72px;width:100%;border-radius:18px"></div>
                            </div>
                            <div>
                                <div class="skeleton mb-2 ms-auto" style="height:14px;width:40px"></div>
                                <div class="skeleton mb-3 ms-auto" style="height:38px;width:106px"></div>
                                <div class="skeleton mb-3 ms-auto" style="height:13px;width:66px"></div>
                                <div class="skeleton ms-auto" style="height:44px;width:126px;border-radius:16px"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>

            {{-- Flight cards rendered by JS --}}
            <div id="flight-offers" class="d-none"></div>

            {{-- Load More button --}}
            <div id="load-more-wrap" class="d-none text-center mt-3 mb-4">
                <button class="btn btn-outline-primary px-4 py-2 fw-semibold" id="load-more-btn">
                    <span id="load-more-text">Load More Flights</span>
                    <span id="load-more-spinner" class="d-none spinner-border spinner-border-sm ms-2" role="status"></span>
                </button>
                <p class="text-muted small mt-2" id="pagination-info"></p>
            </div>

            <div id="flight-error"  class="d-none alert alert-danger rounded-3"></div>

        @endif

    </div>{{-- /col --}}
</div>{{-- /row --}}
</div>{{-- /container --}}
</div>{{-- /flight-page-bg --}}

@endsection

@if(!empty($params['origin']))
@push('scripts')
<script>
(function () {

    const params      = @json($params);
    const isLoggedIn  = {{ auth()->check() ? 'true' : 'false' }};
    const apiUrl      = '{{ route('api.flights.search') }}';
    const loading     = document.getElementById('flight-loading');
    const offers      = document.getElementById('flight-offers');
    const error       = document.getElementById('flight-error');
    const header      = document.getElementById('results-header');
    const countEl     = document.getElementById('results-count');
    const loadMoreWrap    = document.getElementById('load-more-wrap');
    const loadMoreBtn     = document.getElementById('load-more-btn');
    const loadMoreText    = document.getElementById('load-more-text');
    const loadMoreSpinner = document.getElementById('load-more-spinner');
    const paginationInfo  = document.getElementById('pagination-info');
    let serverPage    = 1;
    let lastPage      = 1;
    let totalFlights  = 0;
    let allFlights    = [];
    let isLoading     = false;

    // ── Helpers ──────────────────────────────────────────────────
    function fmt(dt) {
        if (!dt) return '—';
        return new Date(dt).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
    }
    function fmtDate(dt) {
        if (!dt) return '';
        return new Date(dt).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
    }
    function depHour(dt) {
        if (!dt) return 0;
        return new Date(dt).getHours();
    }
    function depTimeStr(dt) {
        if (!dt) return '00:00';
        const d = new Date(dt);
        return String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0');
    }
    function durationToMins(str) {
        if (!str) return 9999;
        const m = str.match(/(\d+)h\s*(\d+)m/);
        return m ? parseInt(m[1]) * 60 + parseInt(m[2]) : 9999;
    }
    function moneyLabel(currency, value) {
        const amount = Math.round(parseFloat(value) || 0).toLocaleString('en-US');
        return String(currency || 'USD').toUpperCase() === 'USD' ? `$${amount}` : `${currency} ${amount}`;
    }
    function updateCount() {
        if (!countEl) return;
        const visible = offers.querySelectorAll('.js-flight-card:not([style*="none"])').length;

        if (!visible) {
            countEl.textContent = `No flights available · ${(params.origin ?? '').toUpperCase()} → ${(params.destination ?? '').toUpperCase()}`;
            return;
        }

        const totalStr = totalFlights ? ` of ${totalFlights}` : '';
        countEl.textContent = `Showing ${visible}${totalStr} flights · ${(params.origin ?? '').toUpperCase()} → ${(params.destination ?? '').toUpperCase()}`;
    }
    function applyVisibility() {
        document.querySelectorAll('.js-flight-card').forEach(card => {
            card.style.display = card.dataset.filteredOut === '1' ? 'none' : '';
        });
        updateCount();
    }

    // ── Render a single leg ───────────────────────────────────────
    function renderLeg(origin, destination, depAt, arrAt, duration, stops, stopsLabel, isNextDay) {
        const stopsClass = stops === 0 ? 'stops-direct' : 'stops-multi';
        const stopsText  = stopsLabel ?? (stops === 0 ? 'Direct' : stops + ' stop' + (stops > 1 ? 's' : ''));
        return `
        <div class="flight-itinerary__segment">
            <div class="leg-point">
                <div class="leg-time">${fmt(depAt)}</div>
                <div class="leg-iata">${origin}</div>
                <div class="leg-date">${fmtDate(depAt)}</div>
            </div>
            <div class="flight-path">
                <div class="flight-path__line">
                    <span class="flight-path__dot"></span>
                    <span class="flight-path__dash"></span>
                    <span class="flight-path__plane"><i class="bi bi-airplane-fill"></i></span>
                    <span class="flight-path__dash"></span>
                    <span class="flight-path__dot"></span>
                </div>
                <div class="flight-path__meta">
                    <div class="flight-path__duration">${duration ?? '—'}</div>
                    <div class="flight-path__stops ${stopsClass}">${stopsText}</div>
                </div>
            </div>
            <div class="leg-point leg-point--arrival">
                <div class="leg-time">${fmt(arrAt)}${isNextDay ? '<sup class="text-danger" style="font-size:.6rem">+1</sup>' : ''}</div>
                <div class="leg-iata">${destination}</div>
                <div class="leg-date">${fmtDate(arrAt)}</div>
            </div>
        </div>`;
    }

    // ── Provider badge ────────────────────────────────────────────
    function providerBadge(provider) {
        const map = {
            'duffel':                    { label: 'Duffel',    bg: '#e8f0fe', color: '#1a56db' },
            'travolyo_b2b_xml_agency':   { label: 'B2B',       bg: '#ecfdf5', color: '#059669' },
        };
        const p = map[provider] ?? { label: provider ?? 'Unknown', bg: '#f3f4f6', color: '#6b7280' };
        return `<span class="flight-card__badge" style="background:${p.bg};color:${p.color}">${p.label}</span>`;
    }

    // ── Render one card ───────────────────────────────────────────
    function renderCard(f) {
        const hasReturn = !!f.return_departure_at;

        const badgeHtml = f.badge
            ? `<span class="flight-card__badge bg-success-subtle text-success">${f.badge}</span>`
            : '';

        const logoHtml = f.airline_logo
            ? `<img src="${f.airline_logo}" alt="${f.airline_name ?? ''}" class="airline-logo">`
            : `<span class="fw-bold text-primary fs-5">${f.airline_code ?? ''}</span>`;

        const returnLegHtml = hasReturn ? `
            <div>
                <span class="flight-itinerary__chip">Return</span>
                ${renderLeg(f.destination, f.origin, f.return_departure_at, f.return_arrival_at, f.return_duration, f.return_stops ?? 0, null, false)}
            </div>
        ` : '';

        return `
        <div class="flight-card js-flight-card"
             data-price="${f.total_amount}"
             data-stops="${f.stops}"
             data-duration="${f.duration ?? ''}"
             data-dep-time="${depTimeStr(f.departure_at)}"
             data-arr-time="${depTimeStr(f.arrival_at)}"
             data-currency="${f.currency}">
            <div class="flight-card__body">
                <div class="flight-card__layout">

                    <div class="airline-col">
                        <div class="airline-provider-row">
                            ${providerBadge(f.provider)}
                            ${badgeHtml}
                        </div>
                        <div class="airline-meta">
                            <div class="airline-logo-wrap">
                                ${logoHtml}
                            </div>
                            <div>
                                <div class="airline-brand">${f.airline_name ?? 'Unknown Airline'}</div>
                                <div class="airline-flight-no">Flight: ${f.flight_number ?? 'N/A'}</div>
                            </div>
                        </div>
                    </div>

                    <div class="flight-itinerary">
                        <div>
                            <span class="flight-itinerary__chip">Outbound</span>
                            ${renderLeg(f.origin, f.destination, f.departure_at, f.arrival_at, f.duration, f.stops, f.stops_label, f.is_next_day)}
                        </div>
                        ${returnLegHtml}
                    </div>

                    <div class="price-col">
                        <div class="price-from">From</div>
                        <div class="price-amount">${moneyLabel(f.currency, f.total_amount)}</div>
                        <div class="price-cabin">${f.cabin_class ?? 'Economy'}</div>
                        <button class="btn btn-primary btn-select mt-3 js-select-flight w-100"
                            data-offer-id="${f.id}"
                            data-provider="${f.provider}"
                            data-dep-iata="${f.origin}"
                            data-arr-iata="${f.destination}"
                            data-dep-time="${f.flight_details?.[0]?.dep_time ?? depTimeStr(f.departure_at)}"
                            data-dep-date="${f.flight_details?.[0]?.dep_date ?? ''}"
                            data-arr-time="${f.flight_details?.[0]?.arr_time ?? depTimeStr(f.arrival_at)}"
                            data-arr-date="${f.flight_details?.[0]?.arr_date ?? ''}"
                            data-duration="${f.duration ?? ''}"
                            data-stops="${f.stops}"
                            data-price="${f.total_amount}"
                            data-currency="${f.currency}"
                            data-cabin-class="${f.cabin_class ?? 'ECONOMY'}"
                            data-airline-name="${f.airline_name ?? ''}"
                            data-airline-logo="${f.airline_logo ?? ''}">
                            Select
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
    }

    function updateLoadMoreUI() {
        if (serverPage < lastPage) {
            loadMoreWrap.classList.remove('d-none');
            paginationInfo.textContent = `Showing ${allFlights.length} of ${totalFlights} flights`;
        } else {
            loadMoreWrap.classList.add('d-none');
        }
    }

    // ── Fetch flights ─────────────────────────────────────────────
    async function loadFlights(page = 1) {
        if (isLoading) return;
        isLoading = true;

        if (page > 1) {
            loadMoreText.textContent = 'Loading…';
            loadMoreSpinner.classList.remove('d-none');
            loadMoreBtn.disabled = true;
        }

        try {
            const res  = await fetch(apiUrl, {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify({
                    ...params,
                    page: page,
                }),
            });

            const data = await res.json();
            loading.classList.add('d-none');

            if (!data.success || !data.data?.length) {
                if (page === 1) {
                    error.textContent = data.message ?? 'No flights found for your search.';
                    error.classList.remove('d-none');
                }
                isLoading = false;
                return;
            }

            serverPage   = data.current_page;
            lastPage     = data.last_page;
            totalFlights = data.total;

            allFlights = allFlights.concat(data.data);

            const newHtml = data.data.map(f => renderCard(f)).join('');
            if (page === 1) {
                offers.innerHTML = newHtml;
            } else {
                offers.insertAdjacentHTML('beforeend', newHtml);
            }
            offers.classList.remove('d-none');

            header.classList.remove('d-none');
            header.classList.add('d-flex');

            initPriceSlider();
            filterCards();
            updateLoadMoreUI();

        } catch (err) {
            loading.classList.add('d-none');
            if (page === 1) {
                error.textContent = 'Failed to load flights. Please try again.';
                error.classList.remove('d-none');
            }
            console.error(err);
        } finally {
            isLoading = false;
            loadMoreText.textContent = 'Load More Flights';
            loadMoreSpinner.classList.add('d-none');
            loadMoreBtn.disabled = false;
        }
    }

    // ── Load More click ──────────────────────────────────────────
    loadMoreBtn?.addEventListener('click', () => {
        if (serverPage < lastPage) {
            loadFlights(serverPage + 1);
        }
    });

    // ── Price slider ──────────────────────────────────────────────
    function initPriceSlider() {
        const priceRange    = document.getElementById('priceRange');
        const priceRangeVal = document.getElementById('priceRangeVal');
        const priceRangeMin = document.getElementById('priceRangeMin');
        const cards         = [...document.querySelectorAll('.js-flight-card')];
        const prices        = cards.map(c => parseFloat(c.dataset.price)).filter(v => isFinite(v));

        if (!prices.length) return;

        const minP = Math.floor(Math.min(...prices));
        const maxP = Math.ceil(Math.max(...prices));

        const oldMax   = +priceRange.max || 0;
        const wasAtMax = +priceRange.value >= oldMax || oldMax === 0;

        priceRange.min   = minP;
        priceRange.max   = maxP;
        priceRange.value = wasAtMax ? maxP : priceRange.value;

        const fmt = v => moneyLabel(cards[0]?.dataset.currency ?? 'USD', v);
        if (priceRangeMin) priceRangeMin.textContent = fmt(minP);
        if (priceRangeVal) priceRangeVal.textContent = fmt(maxP);

        function paintSlider() {
            const mn = +priceRange.min, mx = +priceRange.max, vl = +priceRange.value;
            const pct = mx > mn ? ((vl - mn) / (mx - mn)) * 100 : 100;
            priceRange.style.background = `linear-gradient(to right,var(--flight-theme) 0%,var(--flight-theme) ${pct}%,#e5e7eb ${pct}%,#e5e7eb 100%)`;
        }
        paintSlider();

        priceRange.addEventListener('input', function () {
            if (priceRangeVal) priceRangeVal.textContent = fmt(+this.value);
            paintSlider();
            filterCards();
        });
    }

    // ── Filter cards ──────────────────────────────────────────────
    function filterCards() {
        const priceRange = document.getElementById('priceRange');
        const maxPrice   = priceRange ? +priceRange.value : Infinity;
        const stopFilter = document.querySelector('.stop-filter:checked')?.value ?? 'all';
        const timeFilter = document.querySelector('.dep-time-filter:checked')?.value ?? 'any';

        document.querySelectorAll('.js-flight-card').forEach(card => {
            const price  = parseFloat(card.dataset.price);
            const stops  = parseInt(card.dataset.stops);
            const hour   = parseInt((card.dataset.depTime || '00:00').split(':')[0]);

            let show = price <= maxPrice;
            if (stopFilter === '0') show = show && stops === 0;
            else if (stopFilter === '1') show = show && stops === 1;
            else if (stopFilter === '2') show = show && stops >= 2;

            if (timeFilter === 'morning')   show = show && hour >= 6  && hour < 12;
            else if (timeFilter === 'afternoon') show = show && hour >= 12 && hour < 18;
            else if (timeFilter === 'evening')   show = show && hour >= 18;

            card.dataset.filteredOut = show ? '0' : '1';
        });

        applyVisibility();
    }

    // ── Sort ──────────────────────────────────────────────────────
    document.getElementById('sortSelect')?.addEventListener('change', function () {
        const key    = this.value;
        const parent = offers;
        const cards  = [...parent.querySelectorAll('.js-flight-card')];

        cards.sort((a, b) => {
            if (key === 'price')    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
            if (key === 'duration') return durationToMins(a.dataset.duration) - durationToMins(b.dataset.duration);
            if (key === 'dep_time') return a.dataset.depTime.localeCompare(b.dataset.depTime);
            if (key === 'arr_time') return a.dataset.arrTime.localeCompare(b.dataset.arrTime);
            return 0;
        });
        cards.forEach(c => parent.appendChild(c));
        applyVisibility();
    });

    // ── Filter listeners ──────────────────────────────────────────
    document.querySelectorAll('.stop-filter, .dep-time-filter').forEach(el => {
        el.addEventListener('change', () => filterCards());
    });

    // ── Select button → prebook API → checkout page ──────────────
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.js-select-flight');
        if (!btn) return;

        if (!isLoggedIn) {
            window.openAuthModal('signin');
            return;
        }

        btn.disabled    = true;
        btn.textContent = 'Please wait…';

        const payload = {
            offer_id:     btn.dataset.offerId,
            provider:     btn.dataset.provider,
            dep_iata:     btn.dataset.depIata,
            arr_iata:     btn.dataset.arrIata,
            dep_time:     btn.dataset.depTime,
            dep_date:     btn.dataset.depDate,
            arr_time:     btn.dataset.arrTime,
            arr_date:     btn.dataset.arrDate,
            duration:     btn.dataset.duration,
            stops:        parseInt(btn.dataset.stops, 10),
            price:        parseFloat(btn.dataset.price),
            currency:     btn.dataset.currency,
            cabin_class:  btn.dataset.cabinClass,
            airline_name: btn.dataset.airlineName,
            airline_logo: btn.dataset.airlineLogo,
            trip_type:    params.trip_type    ?? 'one_way',
            adults:       parseInt(params.adults    ?? 1, 10),
            children:     parseInt(params.children  ?? 0, 10),
            infants:      parseInt(params.infants   ?? 0, 10),
        };

        try {
            const res  = await fetch('{{ route('api.flights.prebook') }}', {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    'Accept':       'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await res.json();

            if (data.success && data.checkout_url) {
                window.location.href = data.checkout_url;
                return;
            }

            alert(data.message ?? 'Could not select this flight. Please try again.');
        } catch {
            alert('Network error. Please try again.');
        }

        btn.disabled    = false;
        btn.textContent = 'Select';
    });

    loadFlights();

})();
</script>
@endpush
@endif
