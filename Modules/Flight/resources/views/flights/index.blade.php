@extends('layouts.master')

@section('title', 'Flight Results – Travolyo')

@push('styles')
<style>
/* ── Flight page ─────────────────────────────────── */
.flight-page-bg { background: #f4f6fb; min-height: 60vh; }

/* Filters card */
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
.price-slider-wrap input[type=range]::-moz-range-thumb {
    width: 16px; height: 16px; border-radius: 50%;
    background: var(--bs-primary); cursor: pointer; border: none;
}

/* Stop / time filter pills */
.filter-pill { display: flex; align-items: center; gap: 8px; padding: 7px 10px; border-radius: 8px; cursor: pointer; transition: background .15s; font-size: .875rem; }
.filter-pill:hover { background: #f0f4ff; }
.filter-pill input[type=radio] { accent-color: var(--bs-primary); }

/* Results header */
.results-header { border-bottom: 1px solid #eef0f4; padding-bottom: 12px; margin-bottom: 16px; }
.sort-select { border: 1px solid #dee2e6; border-radius: 6px; padding: 4px 10px; font-size: .85rem; color: #495057; background: #fff; cursor: pointer; }

/* Flight card */
.flight-card {
    background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(0,0,0,.07);
    margin-bottom: 12px; overflow: hidden; transition: box-shadow .2s, transform .2s;
}
.flight-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,.12); transform: translateY(-1px); }
.flight-card__body { padding: 18px 20px; }
.flight-card__badge { font-size: .7rem; font-weight: 600; padding: 3px 8px; border-radius: 20px; }

/* Airline logo col */
.airline-col { min-width: 90px; max-width: 110px; }
.airline-logo { height: 32px; max-width: 80px; object-fit: contain; }

/* Leg */
.leg-time { font-size: 1.2rem; font-weight: 700; line-height: 1.1; }
.leg-iata { font-size: .8rem; color: #6c757d; }
.leg-date { font-size: .75rem; color: #9ca3af; margin-top: 2px; }
.stops-direct { color: #16a34a; }
.stops-multi  { color: #d97706; }

/* Return leg separator */
.return-sep { border-left: 2px dashed #dee2e6; margin: 0 8px; flex-shrink: 0; }

/* Price col */
.price-col { min-width: 160px; max-width: 180px; }
.price-amount { font-size: 1.4rem; font-weight: 700; color: var(--bs-primary); line-height: 1.1; }
.price-cabin  { font-size: .75rem; color: #6c757d; margin-top: 2px; }
.btn-select { padding: 8px 0; font-size: .85rem; border-radius: 8px; font-weight: 600; width: 140px; }

/* Skeleton */
.skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 8px; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
</style>
@endpush

@section('content')

{{-- Search widget --}}
<div class="bg-white border-bottom py-3 shadow-sm">
    <div class="container">
        @include('website.partials._search-widget', ['activeTab' => 'flights'])
    </div>
</div>

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
                    <div class="flight-card__body d-flex align-items-center gap-3">
                        <div class="skeleton" style="width:70px;height:32px;flex-shrink:0"></div>
                        <div class="flex-fill">
                            <div class="skeleton mb-2" style="height:18px;width:60%"></div>
                            <div class="skeleton" style="height:14px;width:40%"></div>
                        </div>
                        <div>
                            <div class="skeleton mb-2" style="height:24px;width:80px"></div>
                            <div class="skeleton" style="height:32px;width:80px;border-radius:8px"></div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>

            {{-- Flight cards rendered by JS --}}
            <div id="flight-offers" class="d-none"></div>
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
    const apiUrl      = '{{ route('api.flights.search') }}';
    const loading     = document.getElementById('flight-loading');
    const offers      = document.getElementById('flight-offers');
    const error       = document.getElementById('flight-error');
    const header      = document.getElementById('results-header');
    const countEl     = document.getElementById('results-count');

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

    // ── Render a single leg ───────────────────────────────────────
    function renderLeg(origin, destination, depAt, arrAt, duration, stops, stopsLabel, isNextDay) {
        const stopsClass = stops === 0 ? 'stops-direct' : 'stops-multi';
        const stopsText  = stopsLabel ?? (stops === 0 ? 'Direct' : stops + ' stop' + (stops > 1 ? 's' : ''));
        return `
        <div class="d-flex align-items-center gap-2 flex-fill">
            <div class="text-start" style="min-width:56px">
                <div class="leg-time">${fmt(depAt)}</div>
                <div class="leg-iata">${origin}</div>
                <div class="leg-date">${fmtDate(depAt)}</div>
            </div>
            <div class="flex-fill text-center px-2">
                <div style="font-size:.75rem;color:#6c757d;margin-bottom:4px">${duration ?? ''}</div>
                <div class="position-relative" style="height:2px;background:#dee2e6;border-radius:2px">
                    <span class="position-absolute top-50 start-50 translate-middle bg-white px-1 text-primary lh-1" style="font-size:.8rem">
                        <i class="bi bi-airplane-fill"></i>
                    </span>
                </div>
                <div class="fw-semibold mt-1 ${stopsClass}" style="font-size:.75rem">${stopsText}</div>
            </div>
            <div class="text-start" style="min-width:56px">
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
        return `<span style="font-size:.65rem;font-weight:600;padding:2px 7px;border-radius:20px;background:${p.bg};color:${p.color};letter-spacing:.04em">${p.label}</span>`;
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
            <div class="return-sep align-self-stretch d-none d-md-block"></div>
            <div class="d-flex flex-column justify-content-center" style="min-width:38px;text-align:center">
                <span class="text-muted" style="font-size:.65rem;letter-spacing:.06em;text-transform:uppercase;writing-mode:vertical-lr;transform:rotate(180deg)">Return</span>
            </div>
            ${renderLeg(f.destination, f.origin, f.return_departure_at, f.return_arrival_at, f.return_duration, f.return_stops ?? 0, null, false)}
        ` : '';

        return `
        <div class="flight-card js-flight-card"
             data-price="${f.total_amount}"
             data-stops="${f.stops}"
             data-duration="${f.duration ?? ''}"
             data-dep-time="${depTimeStr(f.departure_at)}"
             data-arr-time="${depTimeStr(f.arrival_at)}">
            <div class="flight-card__body">
                <div class="d-flex align-items-center gap-3 w-100">

                    {{-- Airline --}}
                    <div class="airline-col d-flex flex-column align-items-center text-center flex-shrink-0">
                        ${logoHtml}
                        <div class="small text-muted mt-2 lh-sm">${f.airline_name ?? ''}</div>
                        <div class="text-muted" style="font-size:.72rem">Flt ${f.flight_number ?? ''}</div>
                        <div class="mt-1">${badgeHtml}</div>
                        <div class="mt-1">${providerBadge(f.provider)}</div>
                    </div>

                    {{-- Legs --}}
                    <div class="d-flex align-items-center flex-fill min-w-0 gap-1">
                        ${renderLeg(f.origin, f.destination, f.departure_at, f.arrival_at, f.duration, f.stops, f.stops_label, f.is_next_day)}
                        ${returnLegHtml}
                    </div>

                    {{-- Price --}}
                    <div class="price-col d-flex flex-column align-items-center align-items-end flex-shrink-0 ms-auto text-end">
                        <div class="price-amount">${f.currency} ${parseFloat(f.total_amount).toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</div>
                        <div class="price-cabin">${f.cabin_class}</div>
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

    // ── Fetch flights ─────────────────────────────────────────────
    async function loadFlights() {
        try {
            const res  = await fetch(apiUrl, {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify(params),
            });

            const data = await res.json();
            loading.classList.add('d-none');

            if (!data.success || !data.data?.length) {
                error.textContent = data.message ?? 'No flights found for your search.';
                error.classList.remove('d-none');
                return;
            }

            offers.innerHTML = data.data.map(f => renderCard(f)).join('');
            offers.classList.remove('d-none');

            header.classList.remove('d-none');
            header.classList.add('d-flex');
            const cnt = data.count;
            countEl.textContent = `Showing ${cnt} flight${cnt !== 1 ? 's' : ''} · ${(params.origin ?? '').toUpperCase()} → ${(params.destination ?? '').toUpperCase()}`;

            initPriceSlider();
            filterCards();

        } catch (err) {
            loading.classList.add('d-none');
            error.textContent = 'Failed to load flights. Please try again.';
            error.classList.remove('d-none');
            console.error(err);
        }
    }

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

        priceRange.min   = minP;
        priceRange.max   = maxP;
        priceRange.value = maxP;

        const fmt = v => `${cards[0]?.querySelector('.price-amount')?.textContent.charAt(0) ?? '$'}${Math.round(v).toLocaleString()}`;
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

            card.style.display = show ? '' : 'none';
        });
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
    });

    // ── Filter listeners ──────────────────────────────────────────
    document.querySelectorAll('.stop-filter, .dep-time-filter').forEach(el => {
        el.addEventListener('change', filterCards);
    });

    // ── Select button → prebook API → checkout page ──────────────
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.js-select-flight');
        if (!btn) return;

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
