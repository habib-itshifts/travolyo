@php
    $activeTab           = $activeTab ?? 'hotels';
    $selectedCountryCode = request('country_code', '');
    $selectedCountryName = request('country', '');
@endphp

<div class="search-widget bg-white rounded-4 shadow-lg p-4 text-start">

    {{-- ── Category Tabs ── --}}
    <ul class="nav search-tabs mb-4 gap-1" id="searchTabs">
        <li class="nav-item">
            <button type="button" class="search-tab-btn {{ $activeTab === 'hotels' ? 'active' : '' }}" data-tab="hotels">
                <i class="bi bi-building me-2"></i>Hotels
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="search-tab-btn {{ $activeTab === 'flights' ? 'active' : '' }}" data-tab="flights">
                <i class="bi bi-airplane me-2"></i>Flights
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="search-tab-btn {{ in_array($activeTab, ['home']) ? 'active' : '' }}" data-tab="home">
                <i class="bi bi-house me-2"></i>Home &amp; Apts
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="search-tab-btn {{ $activeTab === 'events' ? 'active' : '' }}" data-tab="events">
                <i class="bi bi-calendar-event me-2"></i>Events
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="search-tab-btn {{ $activeTab === 'activities' ? 'active' : '' }}" data-tab="activities">
                <i class="bi bi-compass me-2"></i>Activities
            </button>
        </li>
    </ul>

    {{-- ════════════════════════════════════════════
         HOTELS FORM
    ════════════════════════════════════════════ --}}
    <div id="sw-hotels-form" class="{{ $activeTab !== 'hotels' ? 'd-none' : '' }}">
        <form id="hotelSearchForm" action="{{ Route::has('hotels.index') ? route('hotels.index') : '#' }}" method="GET">
            <div class="row g-3">

                {{-- Country Picker (options loaded via AJAX) --}}
                <div class="col-12 col-md-3">
                    <label class="form-label text-muted small mb-1">Country</label>
                    <div class="custom-picker" data-picker data-country-picker data-hidden-id="countryCodeInput"
                         id="countryPicker"
                         data-countries-url="{{ route('api.locations.countries') }}"
                         data-selected-code="{{ $selectedCountryCode }}"
                         data-selected-name="{{ $selectedCountryName }}">
                        <div class="input-icon-wrap picker-trigger" role="button" tabindex="0" aria-expanded="false">
                            <i class="bi bi-globe2 input-icon"></i>
                            <input type="text" name="country" id="countryDisplayInput"
                                   class="form-control search-input picker-input"
                                   placeholder="Select country" autocomplete="off"
                                   value="{{ $selectedCountryName }}" />
                        </div>
                        <div class="picker-menu" id="countryPickerMenu">
                            <div class="p-2 text-muted small">Loading countries&hellip;</div>
                        </div>
                    </div>
                    <input type="hidden" name="country_code" id="countryCodeInput" value="{{ $selectedCountryCode }}" />
                </div>

                {{-- City Picker --}}
                <div class="col-12 col-md-3">
                    <label class="form-label text-muted small mb-1">City</label>
                    <div class="custom-picker" data-picker
                         id="cityPicker"
                         data-cities-url="{{ url('api/locations/cities') }}"
                         data-initial-country="{{ $selectedCountryCode }}">
                        <div class="input-icon-wrap picker-trigger" role="button" tabindex="0" aria-expanded="false">
                            <i class="bi bi-geo-alt input-icon"></i>
                            <input type="text" name="city" id="cityDisplayInput"
                                   class="form-control search-input picker-input"
                                   placeholder="Select city" autocomplete="off"
                                   value="{{ request('city', '') }}" />
                        </div>
                        <div class="picker-menu" id="cityPickerMenu">
                            @if($selectedCountryCode)
                                <div class="p-2 text-muted small">Loading cities&hellip;</div>
                            @else
                                <div class="p-2 text-muted small">Select a country first</div>
                            @endif
                        </div>
                    </div>
                    <input type="hidden" name="location" id="locationInput" value="{{ request('location', '') }}" />
                </div>

                {{-- Check In --}}
                <div class="col-12 col-md-3">
                    <label class="form-label text-muted small mb-1">Check In</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-calendar3 input-icon"></i>
                        <input type="date" name="check_in" class="form-control search-input"
                               value="{{ request('check_in', now()->addDays(4)->format('Y-m-d')) }}" />
                    </div>
                </div>

                {{-- Check Out --}}
                <div class="col-12 col-md-3">
                    <label class="form-label text-muted small mb-1">Check Out</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-calendar3 input-icon"></i>
                        <input type="date" name="check_out" class="form-control search-input"
                               value="{{ request('check_out', now()->addDays(8)->format('Y-m-d')) }}" />
                    </div>
                </div>

                {{-- Guests --}}
                <div class="col-12 col-md-4">
                    <label class="form-label text-muted small mb-1">Guests</label>
                    <div class="guests-picker" id="guestsPicker">
                        <div class="input-icon-wrap guests-trigger" role="button" tabindex="0" aria-expanded="false">
                            <i class="bi bi-people-fill input-icon"></i>
                            <input id="guestsSummary" type="text" class="form-control search-input guests-summary-input"
                                   value="1 Unit - 1 Adult - 0 Children" readonly />
                        </div>
                        <div class="guests-menu" id="guestsMenu">
                            <div id="roomsContainer"></div>
                            <button type="button" class="btn-add-room" id="addRoomBtn" style="display:none;">+ Add Room</button>
                        </div>
                    </div>
                    <div id="guestsHiddenFields"></div>
                </div>

            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-search px-5 py-2">
                    <i class="bi bi-search me-2"></i>Search Hotels
                </button>
            </div>
        </form>
    </div>

    {{-- ════════════════════════════════════════════
         FLIGHTS / HOME / EVENTS FORM CONTAINER
    ════════════════════════════════════════════ --}}
    <div id="sw-flights-form-container" class="{{ $activeTab === 'hotels' ? 'd-none' : '' }}">

        {{-- Trip Type Sub-tabs --}}
        <div id="flight-subtabs" class="mb-3 {{ in_array($activeTab, ['home', 'events', 'activities']) ? 'd-none' : '' }}">
            <button class="trip-type-btn {{ request('trip_type', 'one_way') === 'one_way' ? 'active' : '' }}"
                    id="oneWayBtn" data-type="one_way">One-way</button>
            <button class="trip-type-btn {{ request('trip_type') === 'round_trip' ? 'active' : '' }}"
                    id="roundTripBtn" data-type="round_trip">Round-Trip</button>
        </div>

        <form action="{{ Route::has('flights.index') ? route('flights.index') : '#' }}" method="GET"
              id="flightSearchForm"
              data-airports-url="{{ route('api.locations.airports.search') }}">
            <input type="hidden" name="trip_type" id="tripTypeInput" value="{{ request('trip_type', 'one_way') }}" />

            {{-- ── Flights Pane ── --}}
            <div data-tab-pane="flights" class="{{ $activeTab !== 'flights' ? 'd-none' : '' }}">
                <div class="row g-3 align-items-end">

                    {{-- From --}}
                    <div class="col-12 col-md-3">
                        <label class="form-label text-muted small mb-1">From</label>
                        <div class="input-icon-wrap flight-airport-field">
                            <i class="bi bi-send input-icon"></i>
                            <input type="text" name="origin" class="form-control search-input airport-autocomplete"
                                   placeholder="Departure city or IATA" value="{{ request('origin') }}"
                                   autocomplete="off" data-airport-input="origin" />
                            <div class="airport-suggest-list d-none" data-airport-list="origin"></div>
                        </div>
                    </div>

                    {{-- To --}}
                    <div class="col-12 col-md-3">
                        <label class="form-label text-muted small mb-1">To</label>
                        <div class="input-icon-wrap flight-airport-field">
                            <i class="bi bi-geo-alt input-icon"></i>
                            <input type="text" name="destination" class="form-control search-input airport-autocomplete"
                                   placeholder="Destination city or IATA" value="{{ request('destination') }}"
                                   autocomplete="off" data-airport-input="destination" />
                            <div class="airport-suggest-list d-none" data-airport-list="destination"></div>
                        </div>
                    </div>

                    {{-- Departure --}}
                    <div class="col-12 col-md-3">
                        <label class="form-label text-muted small mb-1">Departure</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-calendar3 input-icon"></i>
                            <input type="date" name="departure_date" class="form-control search-input"
                                   value="{{ request('departure_date', now()->addDay()->format('Y-m-d')) }}" />
                        </div>
                    </div>

                    {{-- Passengers (one-way row) --}}
                    <div class="col-12 col-md-3 flight-one-way-only">
                        <label class="form-label text-muted small mb-1">Passengers</label>
                        <div class="flight-passenger-picker" data-flight-pax-picker>
                            <div class="input-icon-wrap flight-passenger-trigger" role="button" tabindex="0" aria-expanded="false">
                                <i class="bi bi-people input-icon"></i>
                                <input type="text" class="form-control search-input flight-passenger-summary" value="" readonly />
                            </div>
                            <div class="flight-passenger-menu">
                                <div class="flight-passenger-row" data-type="adults" data-min="1" data-max="9">
                                    <div>
                                        <div class="flight-passenger-label">Adult</div>
                                        <div class="flight-passenger-sub">18+ years old</div>
                                    </div>
                                    <div class="flight-passenger-counter">
                                        <button type="button" class="flight-passenger-btn" data-delta="-1">-</button>
                                        <span class="flight-passenger-val">1</span>
                                        <button type="button" class="flight-passenger-btn" data-delta="1">+</button>
                                    </div>
                                </div>
                                <div class="flight-passenger-row" data-type="children" data-min="0" data-max="9">
                                    <div>
                                        <div class="flight-passenger-label">Child</div>
                                        <div class="flight-passenger-sub">0-17 years old</div>
                                    </div>
                                    <div class="flight-passenger-counter">
                                        <button type="button" class="flight-passenger-btn" data-delta="-1">-</button>
                                        <span class="flight-passenger-val">0</span>
                                        <button type="button" class="flight-passenger-btn" data-delta="1">+</button>
                                    </div>
                                </div>
                                <div class="flight-passenger-row" data-type="infants" data-min="0" data-max="9">
                                    <div>
                                        <div class="flight-passenger-label">Infant</div>
                                        <div class="flight-passenger-sub">Under 2 years old</div>
                                    </div>
                                    <div class="flight-passenger-counter">
                                        <button type="button" class="flight-passenger-btn" data-delta="-1">-</button>
                                        <span class="flight-passenger-val">0</span>
                                        <button type="button" class="flight-passenger-btn" data-delta="1">+</button>
                                    </div>
                                </div>
                                <input type="hidden" name="adults"   value="{{ max(1, (int) request('adults', 1)) }}">
                                <input type="hidden" name="children" value="{{ max(0, (int) request('children', 0)) }}">
                                <input type="hidden" name="infants"  value="{{ max(0, (int) request('infants', 0)) }}">
                            </div>
                        </div>
                    </div>

                    {{-- Return date (round-trip only) --}}
                    <div class="col-12 col-md-3 flight-round-trip-only d-none" id="returnDateWrap">
                        <label class="form-label text-muted small mb-1">Return</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-calendar3 input-icon"></i>
                            <input type="date" name="return_date" class="form-control search-input"
                                   value="{{ request('return_date') }}" />
                        </div>
                    </div>
                </div>

                <div class="row g-3 align-items-end mt-0">
                    {{-- Cabin (one-way row) --}}
                    <div class="col-12 col-md-3 flight-one-way-only">
                        <label class="form-label text-muted small mb-1">Cabin</label>
                        <select name="cabin_class" class="form-select search-input">
                            @foreach(['ECONOMY' => 'Economy', 'PREMIUM_ECONOMY' => 'Premium Economy', 'BUSINESS' => 'Business', 'FIRST' => 'First'] as $val => $label)
                                <option value="{{ $val }}" {{ request('cabin_class', 'ECONOMY') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Passengers + Cabin (round-trip row) --}}
                    <div class="col-12 flight-round-trip-only d-none">
                        <div class="row g-3">
                            <div class="col-12 col-md-3">
                                <label class="form-label text-muted small mb-1">Passengers</label>
                                <div class="flight-passenger-picker" data-flight-pax-picker>
                                    <div class="input-icon-wrap flight-passenger-trigger" role="button" tabindex="0" aria-expanded="false">
                                        <i class="bi bi-people input-icon"></i>
                                        <input type="text" class="form-control search-input flight-passenger-summary" value="" readonly />
                                    </div>
                                    <div class="flight-passenger-menu">
                                        <div class="flight-passenger-row" data-type="adults" data-min="1" data-max="9">
                                            <div>
                                                <div class="flight-passenger-label">Adult</div>
                                                <div class="flight-passenger-sub">18+ years old</div>
                                            </div>
                                            <div class="flight-passenger-counter">
                                                <button type="button" class="flight-passenger-btn" data-delta="-1">-</button>
                                                <span class="flight-passenger-val">1</span>
                                                <button type="button" class="flight-passenger-btn" data-delta="1">+</button>
                                            </div>
                                        </div>
                                        <div class="flight-passenger-row" data-type="children" data-min="0" data-max="9">
                                            <div>
                                                <div class="flight-passenger-label">Child</div>
                                                <div class="flight-passenger-sub">0-17 years old</div>
                                            </div>
                                            <div class="flight-passenger-counter">
                                                <button type="button" class="flight-passenger-btn" data-delta="-1">-</button>
                                                <span class="flight-passenger-val">0</span>
                                                <button type="button" class="flight-passenger-btn" data-delta="1">+</button>
                                            </div>
                                        </div>
                                        <div class="flight-passenger-row" data-type="infants" data-min="0" data-max="9">
                                            <div>
                                                <div class="flight-passenger-label">Infant</div>
                                                <div class="flight-passenger-sub">Under 2 years old</div>
                                            </div>
                                            <div class="flight-passenger-counter">
                                                <button type="button" class="flight-passenger-btn" data-delta="-1">-</button>
                                                <span class="flight-passenger-val">0</span>
                                                <button type="button" class="flight-passenger-btn" data-delta="1">+</button>
                                            </div>
                                        </div>
                                        <input type="hidden" name="adults"   value="{{ max(1, (int) request('adults', 1)) }}">
                                        <input type="hidden" name="children" value="{{ max(0, (int) request('children', 0)) }}">
                                        <input type="hidden" name="infants"  value="{{ max(0, (int) request('infants', 0)) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label text-muted small mb-1">Cabin</label>
                                <select name="cabin_class" class="form-select search-input">
                                    @foreach(['ECONOMY' => 'Economy', 'PREMIUM_ECONOMY' => 'Premium Economy', 'BUSINESS' => 'Business', 'FIRST' => 'First'] as $val => $label)
                                        <option value="{{ $val }}" {{ request('cabin_class', 'ECONOMY') === $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>{{-- /data-tab-pane="flights" --}}

            {{-- ── Home & Apts Pane ── --}}
            <div data-tab-pane="home" class="{{ $activeTab !== 'home' ? 'd-none' : '' }}">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-6">
                        <label class="form-label text-muted small mb-1">Location</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-geo-alt input-icon"></i>
                            <input type="text" class="form-control search-input" name="home_location"
                                   placeholder="City or area" {{ $activeTab !== 'home' ? 'disabled' : '' }} />
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label text-muted small mb-1">Date</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-calendar3 input-icon"></i>
                            <input type="date" class="form-control search-input" name="home_date"
                                   {{ $activeTab !== 'home' ? 'disabled' : '' }} />
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Events Pane ── --}}
            <div data-tab-pane="events" class="{{ $activeTab !== 'events' ? 'd-none' : '' }}">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-6">
                        <label class="form-label text-muted small mb-1">Event / City</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-geo-alt input-icon"></i>
                            <input type="text" class="form-control search-input" name="event_location"
                                   placeholder="Concert, city or venue" {{ $activeTab !== 'events' ? 'disabled' : '' }} />
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label text-muted small mb-1">Date</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-calendar3 input-icon"></i>
                            <input type="date" class="form-control search-input" name="event_date"
                                   {{ $activeTab !== 'events' ? 'disabled' : '' }} />
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Activities Pane ── --}}
            <div data-tab-pane="activities" class="{{ $activeTab !== 'activities' ? 'd-none' : '' }}">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-6">
                        <label class="form-label text-muted small mb-1">City</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-geo-alt input-icon"></i>
                            <input type="text" class="form-control search-input" name="city"
                                   value="{{ request('city', '') }}"
                                   placeholder="e.g. Dubai" {{ $activeTab !== 'activities' ? 'disabled' : '' }} />
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label text-muted small mb-1">Date</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-calendar3 input-icon"></i>
                            <input type="date" class="form-control search-input" name="activity_date"
                                   value="{{ request('activity_date', now()->format('Y-m-d')) }}"
                                   {{ $activeTab !== 'activities' ? 'disabled' : '' }} />
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-search px-5 py-2" id="flightSearchSubmitBtn">
                    <i class="bi bi-search me-2"></i>
                    <span data-search-btn-text>
                        @if($activeTab === 'home') Search Home &amp; Apts
                        @elseif($activeTab === 'events') Search Events
                        @elseif($activeTab === 'activities') Search Activities
                        @else Search Flights
                        @endif
                    </span>
                </button>
            </div>
        </form>

    </div>{{-- /sw-flights-form-container --}}

</div>{{-- /search-widget --}}

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/custom/flight.css') }}" />
@endpush

@push('scripts')
<script>
(function () {
    const activeTabInit = '{{ $activeTab }}';

    const tabButtons      = [...document.querySelectorAll('#searchTabs .search-tab-btn[data-tab]')];
    const hotelsContainer = document.getElementById('sw-hotels-form');
    const flightsContainer = document.getElementById('sw-flights-form-container');
    const tabPanes        = [...document.querySelectorAll('[data-tab-pane]')];
    const flightSubtabs   = document.getElementById('flight-subtabs');
    const submitBtn       = document.getElementById('flightSearchSubmitBtn');
    const submitText      = submitBtn ? submitBtn.querySelector('[data-search-btn-text]') : null;

    const tabTextMap = {
        flights: 'Search Flights',
        home:    'Search Home & Apts',
        events:  'Search Events',
        activities: 'Search Activities',
        hotels:  'Search Hotels',
    };
    const tabActionMap = {
        flights: "{{ Route::has('flights.index') ? route('flights.index') : '#' }}",
        home:    "{{ Route::has('flights.index') ? route('flights.index') : '#' }}",
        events:  "{{ Route::has('flights.index') ? route('flights.index') : '#' }}",
        activities: "{{ Route::has('activities.index') ? route('activities.index') : '#' }}",
    };

    let activeTopTab = activeTabInit;

    const setPaneEnabled = (pane, enabled) => {
        pane.querySelectorAll('input, select, textarea, button').forEach((field) => {
            if (field.id === 'flightSearchSubmitBtn') return;
            if (!field.name) return;
            field.disabled = !enabled;
        });
    };

    const setActiveTopTab = (tab) => {
        activeTopTab = tab;
        const isHotels = (tab === 'hotels');

        // Show/hide the two outer containers
        if (hotelsContainer)  hotelsContainer.classList.toggle('d-none', !isHotels);
        if (flightsContainer) flightsContainer.classList.toggle('d-none', isHotels);

        // Highlight the correct tab button
        tabButtons.forEach((btn) => btn.classList.toggle('active', btn.dataset.tab === tab));

        if (!isHotels) {
            // Switch inner panes within the flight form
            tabPanes.forEach((pane) => {
                const isActive = pane.dataset.tabPane === tab;
                pane.classList.toggle('d-none', !isActive);
                setPaneEnabled(pane, isActive);
            });

            if (flightSubtabs) {
                flightSubtabs.classList.toggle('d-none', tab !== 'flights');
            }
            if (submitText) {
                submitText.textContent = tabTextMap[tab] || 'Search';
            }
            const formEl = document.getElementById('flightSearchForm');
            if (formEl) {
                formEl.action = tabActionMap[tab] || tabActionMap.flights;
            }
        }
    };

    tabButtons.forEach((btn) => btn.addEventListener('click', () => setActiveTopTab(btn.dataset.tab)));

    // Initialise to server-rendered active tab
    setActiveTopTab(activeTabInit);

    // Hotel country/city picker (works on flights page widget too)
    (function () {
        const countryPicker = document.getElementById('countryPicker');
        const countryMenu = document.getElementById('countryPickerMenu');
        const countryInput = document.getElementById('countryDisplayInput');
        const countryCodeInput = document.getElementById('countryCodeInput');

        const cityPicker = document.getElementById('cityPicker');
        const cityMenu = document.getElementById('cityPickerMenu');
        const cityInput = document.getElementById('cityDisplayInput');
        const locationInput = document.getElementById('locationInput');

        if (!countryPicker || !countryMenu || !cityPicker || !cityMenu) return;

        const countriesUrl = countryPicker.dataset.countriesUrl || '';
        const citiesUrl = cityPicker.dataset.citiesUrl || '';
        const preselectedCode = countryPicker.dataset.selectedCode || '';
        const preselectedName = countryPicker.dataset.selectedName || '';
        const initialCountryForCities = cityPicker.dataset.initialCountry || preselectedCode;

        const esc = (value) => String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        const allPickers = [countryPicker, cityPicker];
        const closePickers = () => {
            allPickers.forEach((picker) => {
                picker.classList.remove('is-open');
                const trigger = picker.querySelector('.picker-trigger');
                if (trigger) trigger.setAttribute('aria-expanded', 'false');
            });
        };

        const openPicker = (picker) => {
            closePickers();
            picker.classList.add('is-open');
            const trigger = picker.querySelector('.picker-trigger');
            if (trigger) trigger.setAttribute('aria-expanded', 'true');
        };

        const bindPickerOpen = (picker, input) => {
            const trigger = picker.querySelector('.picker-trigger');
            if (!trigger || !input) return;

            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                const willOpen = !picker.classList.contains('is-open');
                if (willOpen) openPicker(picker);
                else closePickers();
            });

            input.addEventListener('focus', () => openPicker(picker));
            input.addEventListener('click', (e) => e.stopPropagation());

            trigger.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    trigger.click();
                }
            });
        };

        bindPickerOpen(countryPicker, countryInput);
        bindPickerOpen(cityPicker, cityInput);
        document.addEventListener('click', closePickers);

        const renderCountries = (countries) => {
            if (!countries.length) {
                countryMenu.innerHTML = '<div class="p-2 text-muted small">No countries available</div>';
                return;
            }

            countryMenu.innerHTML = countries.map((country) => `
                <button type="button" class="picker-option"
                        data-value="${esc(country.country_name)}"
                        data-code="${esc(country.country_code)}">
                    <i class="bi bi-globe2"></i>
                    <span>${esc(country.country_name)}</span>
                    <span class="picker-code">${esc(country.country_code)}</span>
                </button>
            `).join('');

            if (preselectedCode && !countryInput?.value) {
                const match = countries.find((country) => country.country_code === preselectedCode);
                if (match && countryInput) countryInput.value = match.country_name;
            } else if (!countryInput?.value && preselectedName && countryInput) {
                countryInput.value = preselectedName;
            }
        };

        const renderCities = (cities) => {
            if (!cities.length) {
                cityMenu.innerHTML = '<div class="p-2 text-muted small">No cities found</div>';
                return;
            }

            cityMenu.innerHTML = cities.map((city) => `
                <button type="button" class="picker-option"
                        data-value="${esc(city.city_name)}"
                        data-code="${esc(city.city_code)}">
                    <i class="bi bi-geo-alt"></i>
                    <span>${esc(city.city_name)}</span>
                    <span class="picker-code">${esc(city.city_code)}</span>
                </button>
            `).join('');
        };

        const fetchCities = (countryCode) => {
            if (!countryCode || !citiesUrl) {
                cityMenu.innerHTML = '<div class="p-2 text-muted small">Select a country first</div>';
                return;
            }

            cityMenu.innerHTML = '<div class="p-2 text-muted small">Loading cities...</div>';

            const url = citiesUrl.replace(/\/$/, '') + '/' + countryCode;

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            })
                .then((r) => r.json())
                .then((payload) => renderCities(Array.isArray(payload.data) ? payload.data : []))
                .catch(() => {
                    cityMenu.innerHTML = '<div class="p-2 text-muted small text-danger">Failed to load cities</div>';
                });
        };

        countryMenu.addEventListener('click', (e) => {
            const option = e.target.closest('.picker-option');
            if (!option) return;
            if (countryInput) countryInput.value = option.dataset.value || '';
            if (countryCodeInput) countryCodeInput.value = option.dataset.code || '';
            if (cityInput) cityInput.value = '';
            if (locationInput) locationInput.value = '';
            closePickers();
            fetchCities(option.dataset.code || '');
        });

        cityMenu.addEventListener('click', (e) => {
            const option = e.target.closest('.picker-option');
            if (!option) return;
            if (cityInput) cityInput.value = option.dataset.value || '';
            if (locationInput) locationInput.value = option.dataset.code || '';
            closePickers();
        });

        fetch(countriesUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        })
            .then((r) => r.json())
            .then((payload) => renderCountries(Array.isArray(payload.data) ? payload.data : []))
            .catch(() => {
                countryMenu.innerHTML = '<div class="p-2 text-muted small">Unable to load countries</div>';
            });

        if (initialCountryForCities) {
            fetchCities(initialCountryForCities);
        }
    })();

    // Hotel guests picker (multi-room)
    (function () {
        const guestsPicker = document.getElementById('guestsPicker');
        const guestsTrigger = guestsPicker ? guestsPicker.querySelector('.guests-trigger') : null;
        const guestsSummary = document.getElementById('guestsSummary');
        const roomsContainer = document.getElementById('roomsContainer');
        const addRoomBtn = document.getElementById('addRoomBtn');
        const guestsHiddenFields = document.getElementById('guestsHiddenFields');
        let rooms = [{ adults: 1, children: 0, unit: 1, childAges: [] }];

        if (!guestsPicker || !guestsTrigger || !roomsContainer || !guestsSummary) return;

        const updateGuestsSummary = () => {
            const totals = rooms.reduce((acc, room) => {
                acc.adults += room.adults;
                acc.children += room.children;
                acc.units += room.unit;
                return acc;
            }, { adults: 0, children: 0, units: 0 });
            guestsSummary.value = `${totals.units} Unit - ${totals.adults} Adult - ${totals.children} Children`;
        };

        const roomMarkup = (room, index) => {
            const childAgesMarkup = room.children > 0
                ? `
                <div class="room-divider"></div>
                ${room.childAges.map((age, childIndex) => `
                    <div class="guest-row child-age-row">
                        <div><div class="guest-label">Child ${childIndex + 1} Age</div></div>
                        <div class="counter-wrap">
                            <button type="button" class="counter-btn" data-counter="childAge" data-child-index="${childIndex}" data-delta="-1">-</button>
                            <span class="counter-val">${age}</span>
                            <button type="button" class="counter-btn" data-counter="childAge" data-child-index="${childIndex}" data-delta="1">+</button>
                        </div>
                    </div>
                `).join('')}
            `
                : '';

            return `
                <div class="room-card" data-room-index="${index}">
                    <div class="room-card__head">
                        <div class="room-title">Room ${index + 1}</div>
                        ${rooms.length > 1 ? '<button type="button" class="room-remove" data-remove-room>Remove</button>' : ''}
                    </div>
                    <div class="guest-row">
                        <div>
                            <div class="guest-label">Adults</div>
                            <div class="guest-sub">Above 12 years</div>
                        </div>
                        <div class="counter-wrap">
                            <button type="button" class="counter-btn" data-counter="adults" data-delta="-1">-</button>
                            <span class="counter-val">${room.adults}</span>
                            <button type="button" class="counter-btn" data-counter="adults" data-delta="1">+</button>
                        </div>
                    </div>
                    <div class="guest-row">
                        <div>
                            <div class="guest-label">Children</div>
                            <div class="guest-sub">Below 12 years</div>
                        </div>
                        <div class="counter-wrap">
                            <button type="button" class="counter-btn" data-counter="children" data-delta="-1">-</button>
                            <span class="counter-val">${room.children}</span>
                            <button type="button" class="counter-btn" data-counter="children" data-delta="1">+</button>
                        </div>
                    </div>
                    <div class="guest-row">
                        <div>
                            <div class="guest-label">Unit</div>
                            <div class="guest-sub">Rooms</div>
                        </div>
                        <div class="counter-wrap">
                            <button type="button" class="counter-btn" data-counter="unit" data-delta="-1">-</button>
                            <span class="counter-val">${room.unit}</span>
                            <button type="button" class="counter-btn" data-counter="unit" data-delta="1">+</button>
                        </div>
                    </div>
                    ${childAgesMarkup}
                </div>
            `;
        };

        const renderRooms = () => {
            roomsContainer.innerHTML = rooms.map(roomMarkup).join('');
            updateGuestsSummary();

            if (guestsHiddenFields) {
                guestsHiddenFields.innerHTML = rooms.map((room) => {
                    const childAges = (room.childAges || [])
                        .map((age, ageIndex) => `<input type="hidden" name="child_ages[${ageIndex}]" value="${age}">`)
                        .join('');

                    return `
                        <input type="hidden" name="adults" value="${room.adults}">
                        <input type="hidden" name="children" value="${room.children}">
                        <input type="hidden" name="unit" value="${room.unit}">
                        ${childAges}
                    `;
                }).join('');
            }
        };

        const closeGuestsPicker = () => {
            guestsPicker.classList.remove('is-open');
            guestsTrigger.setAttribute('aria-expanded', 'false');
        };

        guestsTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const willOpen = !guestsPicker.classList.contains('is-open');
            if (willOpen) {
                guestsPicker.classList.add('is-open');
                guestsTrigger.setAttribute('aria-expanded', 'true');
            } else {
                closeGuestsPicker();
            }
        });

        guestsTrigger.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                guestsTrigger.click();
            }
        });

        roomsContainer.addEventListener('click', (e) => {
            e.stopPropagation();
            const roomEl = e.target.closest('.room-card');
            if (!roomEl) return;
            const roomIndex = Number(roomEl.dataset.roomIndex);
            if (Number.isNaN(roomIndex)) return;

            if (e.target.matches('[data-remove-room]')) {
                if (rooms.length > 1) {
                    rooms.splice(roomIndex, 1);
                    renderRooms();
                }
                return;
            }

            const counterBtn = e.target.closest('.counter-btn');
            if (!counterBtn) return;

            const field = counterBtn.dataset.counter;
            const delta = Number(counterBtn.dataset.delta || '0');
            if (!field || !delta) return;

            if (field === 'childAge') {
                const childIndex = Number(counterBtn.dataset.childIndex);
                if (Number.isNaN(childIndex)) return;
                const currentAge = rooms[roomIndex].childAges[childIndex] ?? 0;
                rooms[roomIndex].childAges[childIndex] = Math.max(0, Math.min(17, currentAge + delta));
                renderRooms();
                return;
            }

            const mins = { adults: 1, children: 0, unit: 1 };
            const maxs = { adults: 20, children: 20, unit: 20 };
            const current = rooms[roomIndex][field];
            const next = Math.min(maxs[field], Math.max(mins[field], current + delta));
            rooms[roomIndex][field] = next;

            if (field === 'children') {
                const room = rooms[roomIndex];
                if (delta > 0) room.childAges.push(0);
                else room.childAges = room.childAges.slice(0, room.children);
            }

            renderRooms();
        });

        if (addRoomBtn) {
            addRoomBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                rooms.push({ adults: 1, children: 0, unit: 1, childAges: [] });
                renderRooms();
            });
        }

        const guestsMenu = guestsPicker.querySelector('.guests-menu');
        if (guestsMenu) {
            guestsMenu.addEventListener('click', (e) => e.stopPropagation());
        }

        document.addEventListener('click', (e) => {
            if (!guestsPicker.contains(e.target)) closeGuestsPicker();
        });

        renderRooms();
    })();

    // ── Trip type toggle (one-way / round-trip) ───────────────────────────────
    const tripInput           = document.getElementById('tripTypeInput');
    const oneWayBtn           = document.getElementById('oneWayBtn');
    const roundTripBtn        = document.getElementById('roundTripBtn');
    const oneWayOnlyFields    = document.querySelectorAll('.flight-one-way-only');
    const roundTripOnlyFields = document.querySelectorAll('.flight-round-trip-only');

    function toggleFieldGroup(fields, show) {
        fields.forEach((wrap) => {
            wrap.classList.toggle('d-none', !show);
            wrap.querySelectorAll('input, select, textarea').forEach((field) => {
                if (field.name) field.disabled = !show;
            });
        });
    }

    function setTripType(type) {
        if (tripInput) tripInput.value = type;
        if (type === 'round_trip') {
            oneWayBtn    && oneWayBtn.classList.remove('active');
            roundTripBtn && roundTripBtn.classList.add('active');
            toggleFieldGroup(oneWayOnlyFields, false);
            toggleFieldGroup(roundTripOnlyFields, true);
        } else {
            oneWayBtn    && oneWayBtn.classList.add('active');
            roundTripBtn && roundTripBtn.classList.remove('active');
            toggleFieldGroup(oneWayOnlyFields, true);
            toggleFieldGroup(roundTripOnlyFields, false);
        }
    }

    if (oneWayBtn)    oneWayBtn.addEventListener('click',    () => setTripType('one_way'));
    if (roundTripBtn) roundTripBtn.addEventListener('click', () => setTripType('round_trip'));
    setTripType(tripInput ? (tripInput.value || 'one_way') : 'one_way');

    // ── Airport autocomplete ──────────────────────────────────────────────────
    const flightForm       = document.getElementById('flightSearchForm');
    const airportsUrl      = flightForm ? flightForm.dataset.airportsUrl : '';
    const originInput      = document.querySelector('[data-airport-input="origin"]');
    const destinationInput = document.querySelector('[data-airport-input="destination"]');
    const originList       = document.querySelector('[data-airport-list="origin"]');
    const destinationList  = document.querySelector('[data-airport-list="destination"]');
    let activeList = null;

    const debounce = (fn, delay = 280) => {
        let timer = null;
        return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), delay); };
    };

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;').replaceAll("'", '&#039;');

    const hideList = (list) => {
        if (!list) return;
        list.classList.add('d-none');
        list.innerHTML = '';
        if (activeList === list) activeList = null;
    };

    const hideAllLists = () => { hideList(originList); hideList(destinationList); };

    const renderAirportList = (list, items, input) => {
        if (!list) return;
        if (!items.length) {
            list.innerHTML = '<div class="airport-suggest-empty">No airport found</div>';
            list.classList.remove('d-none');
            activeList = list;
            return;
        }
        list.innerHTML = items.map((item) => `
            <button type="button" class="airport-suggest-item"
                    data-code="${escapeHtml(item.code)}"
                    data-city="${escapeHtml(item.city)}"
                    data-country="${escapeHtml(item.country)}">
                <span class="airport-suggest-icon"><i class="bi bi-airplane-fill"></i></span>
                <span class="airport-suggest-meta">
                    <span class="airport-suggest-name">${escapeHtml(item.name || item.city)}</span>
                    <span class="airport-suggest-sub">${escapeHtml(item.city)}, ${escapeHtml(item.country)}</span>
                </span>
                <span class="airport-suggest-code">${escapeHtml(item.code)}</span>
            </button>
        `).join('');
        list.classList.remove('d-none');
        activeList = list;
        list.querySelectorAll('.airport-suggest-item').forEach((btn) => {
            btn.addEventListener('click', () => {
                input.value = (btn.dataset.code || '').toUpperCase();
                hideList(list);
            });
        });
    };

    const fetchAirportSuggestions = async (input, list) => {
        if (!airportsUrl || !input || !list) return;
        const keyword = (input.value || '').trim();
        if (keyword.length < 2) { hideList(list); return; }

        list.innerHTML = '<div class="airport-suggest-loading">Searching airports...</div>';
        list.classList.remove('d-none');
        activeList = list;

        try {
            const url = new URL(airportsUrl, window.location.origin);
            url.searchParams.set('keyword', keyword);
            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            });
            if (!response.ok) throw new Error('Request failed');
            const payload = await response.json();
            const items = Array.isArray(payload.data) ? payload.data : [];
            renderAirportList(list, items, input);
        } catch {
            list.innerHTML = '<div class="airport-suggest-empty">Unable to load airports</div>';
            list.classList.remove('d-none');
            activeList = list;
        }
    };

    if (originInput && originList) {
        const originHandler = debounce(() => fetchAirportSuggestions(originInput, originList));
        originInput.addEventListener('input', originHandler);
        originInput.addEventListener('focus', () => {
            if ((originInput.value || '').trim().length >= 2) originHandler();
        });
    }

    if (destinationInput && destinationList) {
        const destinationHandler = debounce(() => fetchAirportSuggestions(destinationInput, destinationList));
        destinationInput.addEventListener('input', destinationHandler);
        destinationInput.addEventListener('focus', () => {
            if ((destinationInput.value || '').trim().length >= 2) destinationHandler();
        });
    }

    document.addEventListener('click', (e) => {
        if (e.target.closest('.flight-airport-field')) return;
        hideAllLists();
    });

    // ── Submit loading state ──────────────────────────────────────────────────
    const resetSubmitState = () => {
        if (!submitBtn || !submitText) return;
        submitBtn.disabled = false;
        submitBtn.classList.remove('is-loading');
        submitText.textContent = tabTextMap[activeTopTab] || 'Search';
    };

    resetSubmitState();
    window.addEventListener('pageshow', resetSubmitState);

    if (flightForm && submitBtn && submitText) {
        flightForm.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitText.textContent = 'Searching...';
            submitBtn.classList.add('is-loading');
        });
    }

})();
</script>
@endpush
