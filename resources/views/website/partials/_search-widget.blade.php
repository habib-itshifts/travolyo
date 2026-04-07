@php
    $activeTab = $activeTab
        ?? (request()->routeIs('flights.*')
            ? 'flights'
            : (request()->routeIs('activities.*')
                ? 'activities'
                : 'hotels'));
    $hotelLikeTabs = ['hotels', 'homes'];
    $isHotelLikeTab = in_array($activeTab, $hotelLikeTabs, true);
    $widgetVariant = $widgetVariant ?? 'inline';
    $isHeroWidget = $widgetVariant === 'hero';
    $defaultSearchCity = '';
    $defaultHotelCheckIn = $defaultHotelCheckIn ?? now()->addDays(4)->format('Y-m-d');
    $defaultHotelCheckOut = $defaultHotelCheckOut ?? now()->addDays(8)->format('Y-m-d');

    // Only fall back to request('destination') on hotel pages — avoids flight destination leaking in
    $hotelDestinationValue = request('city', $isHotelLikeTab ? request('destination', $defaultSearchCity) : $defaultSearchCity);

    // If the hotel destination looks like an IATA code (e.g. "DXB"), resolve it to the city name ("Dubai")
    if ($hotelDestinationValue !== '' && preg_match('/^[A-Z]{3}$/', $hotelDestinationValue)) {
        $resolvedAirport = app(\App\Services\LocationSearchService::class)->findByCode($hotelDestinationValue);
        if ($resolvedAirport) {
            $hotelDestinationValue = $resolvedAirport['city'];
        }
    }

    $activityCityValue = request('city', $defaultSearchCity);
    $initialHotelAdults = max(1, (int) request('adults', 1));
    $initialHotelChildren = max(0, (int) request('children', 0));
    $initialHotelUnits = max(1, (int) request('unit', request('rooms', 1)));
@endphp

@push('styles')
<style>

.trav-search-widget { 
    position: relative; 
    z-index: 3; 
}

.trav-search-widget--hero { 
    margin: 0 auto; 
    max-width: 990px; 
}

.trav-search-widget__shell { 
    border-radius: 34px; 
    overflow: visible; 
}

.trav-search-widget__nav { 
    position: relative; 
    z-index: 10; 
}

.trav-search-widget--hero .trav-search-widget__shell {
    background: transparent;
    border: 0;
    box-shadow: none;
    overflow: visible;
    padding: 0;
    position: relative;
}

.trav-search-widget--hero .trav-search-widget__shell::before { 
    display: none; 
}

.trav-search-widget--inline .trav-search-widget__shell {
    background: #ffffff;
    border: 1px solid #d8e7f1;
    box-shadow: 0 18px 44px rgba(18, 38, 63, 0.08);
    padding: 18px;
}

.trav-search-widget__topbar { 
    display: flex; 
    flex-wrap: wrap; 
    gap: 10px; 
    margin-bottom: 18px; 
}

/* ========== NAV DESIGN - LESS BLUR (Background image clear dikhegi) ========== */
.trav-search-widget--hero .trav-search-widget__nav {
    margin: 0 auto;
    align-items: center;
    display: flex;
    flex-direction: column;
    width: fit-content;
    max-width: calc(100% - 36px);
    padding: 14px 18px 12px;
    position: relative;
    z-index: 5;
    background: rgba(214, 240, 248, 0.38);
    border: 1px solid rgba(255, 255, 255, 0.52);
    /* border-radius: 28px; */
    border-bottom: none;
    border-top-left-radius: 30px;
    border-top-right-radius: 30px;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    box-shadow: 0 10px 24px rgba(27, 73, 99, 0.08);
}

.trav-search-widget--hero .trav-search-widget__nav::before {
    display: none;
}

.trav-search-widget--hero .trav-search-widget__nav::after {
    display: none;
}

.trav-search-widget--hero .trav-search-widget__nav.has-subtabs {
    padding-bottom: 14px;
}

.trav-search-widget--hero .trav-search-widget__topbar {
    align-items: center;
    gap: 12px;
    justify-content: center;
    margin: 0;
    min-height: 0;
    padding: 0;
    position: relative;
    z-index: 6;
    width: fit-content;
    max-width: 100%;
    background: transparent;
    border: 0;
    border-radius: 0;
    backdrop-filter: none;
    -webkit-backdrop-filter: none;
    box-shadow: none;
}

/* ========== FORM DESIGN - LESS BLUR (Background image clear dikhegi) ========== */
.trav-search-widget--hero #sw-hotels-form {
    margin-top: -6px;
    position: relative;
    z-index: 4;
}

.trav-search-widget--hero #sw-flights-form-container {
    margin-top: -4px;
    position: relative;
    z-index: 4;
}

.trav-search-widget--hero .trav-search-form {
    background: rgba(235, 247, 252, 0.68);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 0 0 28px 28px;
    box-shadow: 0 10px 30px rgba(27, 73, 99, 0.10);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    padding: 26px 20px 20px;
    margin-top: 0;
    position: relative;
    border-radius: 20px;
}

.trav-search-widget--hero #sw-flights-form-container .trav-search-form {
    padding-top: 26px;
}

.trav-search-widget--hero .trav-search-form::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 8px;
    background: transparent;
    border-radius: 28px 28px 0 0;
    pointer-events: none;
}

/* ========== TAB STYLING ========== */
.trav-search-tab {
    align-items: center;
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(144, 184, 208, 0.4);
    border-radius: 999px;
    color: #1e4a6e;
    cursor: pointer;
    display: inline-flex;
    font-size: 0.95rem;
    font-weight: 700;
    gap: 8px;
    min-height: 48px;
    padding: 0 18px;
    transition: all 0.18s ease;
}

.trav-search-widget--inline .trav-search-tab { 
    background: #f6fbff; 
    min-height: 44px; 
}

.trav-search-widget--hero .trav-search-tab {
    background: rgba(255, 255, 255, 0.85);
    border-color: rgba(144, 184, 208, 0.4);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    font-size: 0.78rem;
    font-weight: 700;
    min-height: 38px;
    padding: 0 16px;
}

.trav-search-widget--hero .trav-search-tab i { 
    font-size: .74rem; 
}

.trav-search-tab:hover { 
    border-color: rgba(23, 195, 206, 0.7); 
    color: #0b7da7; 
    transform: translateY(-1px);
    background: rgba(255, 255, 255, 0.95);
}

.trav-search-tab.active {
    background: linear-gradient(135deg, #0f88ca 0%, #19cfe3 100%);
    box-shadow: 0 6px 14px rgba(12, 125, 167, 0.25);
    color: #ffffff;
    border-color: transparent;
}

.trav-search-tab--placeholder { 
    color: #6f8ba1; 
    cursor: default; 
    opacity: 0.7;
    background: rgba(255, 255, 255, 0.7);
}

.trav-search-tab--placeholder:hover { 
    border-color: rgba(144, 184, 208, 0.4); 
    transform: none; 
    color: #6f8ba1;
    background: rgba(255, 255, 255, 0.75);
}

/* ========== FORM GRID ========== */
.trav-search-form { 
    display: flex; 
    flex-direction: column; 
    gap: 18px; 
}

.trav-search-grid { 
    display: grid; 
    gap: 14px; 
    grid-template-columns: repeat(12, minmax(0, 1fr)); 
}

.trav-search-widget--hero .trav-search-grid { 
    gap: 12px; 
}

.trav-field { 
    display: flex; 
    flex-direction: column; 
    gap: 8px; 
}

.trav-search-widget--hero .trav-field { 
    gap: 0; 
}

.trav-field--wide { 
    grid-column: span 12; 
}

.trav-field--third { 
    grid-column: span 4; 
}

.trav-field--quarter {
    grid-column: span 3;
}

.trav-field--half { 
    grid-column: span 6; 
}

.trav-field__label {
    color: #1e4a6e;
    font-size: 0.76rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    margin: 0;
    padding-left: 8px;
    text-transform: uppercase;
}

.trav-field__surface {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(16, 109, 160, 0.2);
    border-radius: 24px;
    min-height: 74px;
    padding: 10px 14px;
    transition: all 0.2s ease;
}

.trav-field__surface:hover {
    border-color: rgba(23, 195, 206, 0.5);
    background: rgba(255, 255, 255, 0.98);
}

.trav-search-widget--inline .trav-field__surface { 
    background: #f9fcfe; 
}

.trav-search-widget--hero .trav-field__label { 
    display: none; 
}

.trav-search-widget--hero .trav-field__surface {
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(186, 210, 224, 0.6);
    border-radius: 12px;
    min-height: 54px;
    padding: 3px 14px;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.5);
    margin-top: 21px;
}

.trav-search-widget--hero .trav-field__surface--date {
    align-items: flex-start;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding-bottom: 4px;
    padding-top: 6px;
}

.trav-search-widget--hero .trav-field__surface--date .input-icon-wrap { 
    width: 100%; 
}

.trav-search-widget--hero .trav-field--wide .trav-field__surface { 
    min-height: 54px; 
}

.trav-search-widget--hero .trav-field--third .trav-field__surface { 
    min-height: 68px; 
}

.trav-field__surface .search-input,
.trav-field__surface .form-select {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-bottom: 0;
    border-radius: 16px;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
    color: #11314d;
    font-size: 1rem;
    font-weight: 600;
    height: 52px;
    padding-left: 44px;
    width: 100%;
}

.trav-field__surface .search-input::placeholder { 
    color: #89a4ba; 
    font-weight: 500; 
}

.trav-field__surface .search-input:focus,
.trav-field__surface .form-select:focus {
    background: transparent;
    box-shadow: none;
    outline: none;
}

.trav-search-widget--hero .trav-field__surface--date .search-input { 
    height: 36px; 
}

.trav-search-widget--hero .trav-field__surface .search-input,
.trav-search-widget--hero .trav-field__surface .form-select {
    font-size: 0.9rem;
    font-weight: 700;
    height: 44px;
    padding-left: 38px;
}

.trav-search-widget--hero .trav-field__surface .search-input::placeholder {
    color: #6f8fa8;
    font-size: 0.86rem;
    font-weight: 600;
}

.trav-search-widget--hero .trav-field__surface .input-icon {
    font-size: 1rem;
    left: 14px;
    color: #0f88ca;
}

.input-icon-wrap { 
    position: relative; 
}

.trav-field__helper { 
    color: #4a6f8a; 
    font-size: 0.84rem; 
    margin-top: -4px; 
    padding-left: 8px; 
}

.trav-search-widget--hero .trav-field__helper {
    display: block;
    font-size: 0.72rem;
    font-weight: 500;
    line-height: 1;
    min-height: 12px;
    padding-left: 38px;
    text-align: left;
    width: 100%;
}

/* ========== FLIGHT SUBTABS ========== */
.trav-flight-subtabs { 
    display: flex; 
    flex-wrap: wrap; 
    gap: 12px; 
    margin-bottom: 4px; 
}

.trip-type-btn {
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(14, 124, 167, 0.3);
    border-radius: 999px;
    color: #2c5a7a;
    font-size: 0.86rem;
    font-weight: 700;
    min-height: 40px;
    padding: 0 18px;
    transition: all 0.18s ease;
    cursor: pointer;
}

.trip-type-btn.active, .trip-type-btn:hover {
    background: rgba(23, 195, 206, 0.25);
    border-color: rgba(23, 195, 206, 0.7);
    color: #0f88ca;
}

.trav-search-widget__subtabs {
    align-items: center;
    display: flex;
    justify-content: flex-start;
    margin: 0;
    position: relative;
    width: 100%;
    z-index: 3;
}

.trav-search-widget--hero .trav-search-widget__subtabs {
    background: transparent;
    justify-content: flex-start;
    margin-top: 12px;
    margin-bottom: 0;
    padding: 0;
    position: relative;
    z-index: 7;
}

.trav-search-widget--hero .trav-flight-subtabs {
    background: rgba(255, 255, 255, 0.42);
    border: 1px solid rgba(255, 255, 255, 0.38);
    border-radius: 999px;
    gap: 10px;
    justify-content: flex-start;
    margin: 0;
    padding: 6px;
    position: relative;
    z-index: 8;
    box-shadow: 0 4px 14px rgba(27, 73, 99, 0.08);
}

.trav-search-widget--hero .trip-type-btn {
    background: rgba(255, 255, 255, 0.85);
    border-color: rgba(154, 191, 210, 0.6);
    color: #2c5a7a;
    font-size: 0.8rem;
    font-weight: 700;
    min-height: 36px;
    padding: 0 18px;
}

.trav-search-widget--hero .trip-type-btn.active,
.trav-search-widget--hero .trip-type-btn:hover {
    background: linear-gradient(135deg, #14cfe4 0%, #0b7ec8 100%);
    border-color: transparent;
    color: #ffffff;
}

/* ========== FLIGHT GRID ADJUSTMENTS ========== */
.trav-search-widget--hero [data-tab-pane="flights"] .trav-search-grid { 
    gap: 12px; 
}

.trav-search-widget--hero [data-tab-pane="flights"] .trav-field--third,
.trav-search-widget--hero [data-tab-pane="flights"] .trav-field--half { 
    grid-column: span 6; 
}

.trav-search-widget--hero [data-tab-pane="flights"] .trav-field__surface { 
    min-height: 58px; 
}

.trav-search-widget--hero [data-tab-pane="flights"] .trav-field__surface .search-input,
.trav-search-widget--hero [data-tab-pane="flights"] .trav-field__surface .form-select { 
    font-size: 0.9rem; 
    height: 48px; 
}

/* ========== UPSELL SECTION ========== */
.trav-flight-upsell {
    align-items: center;
    display: flex;
    flex-wrap: wrap;
    gap: 10px 12px;
    margin-top: 6px;
}

.trav-search-widget--hero .trav-flight-upsell { 
    margin-top: 8px; 
}

.trav-flight-upsell__label {
    align-items: center;
    color: #1e4a6e;
    display: inline-flex;
    font-size: 0.74rem;
    font-weight: 600;
    gap: 8px;
}

.trav-flight-upsell__badge {
    background: linear-gradient(135deg, #ff7247 0%, #ff4a43 100%);
    border-radius: 999px;
    color: #ffffff;
    display: inline-flex;
    font-size: 0.64rem;
    font-weight: 800;
    letter-spacing: 0.03em;
    padding: 6px 12px;
}

/* ========== FORM META ========== */
.trav-search-form__meta {
    align-items: center;
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    justify-content: space-between;
}

.trav-search-widget--hero .trav-search-form__meta {
    align-items: flex-start;
    flex-direction: column;
    gap: 10px;
    margin-top: 2px;
}

.trav-search-badges { 
    display: flex; 
    flex-wrap: wrap; 
    gap: 12px; 
}

.trav-search-widget--hero .trav-search-badges { 
    gap: 10px 18px; 
}

.trav-search-badge {
    align-items: center;
    color: #1e4a6e;
    display: inline-flex;
    font-size: 0.9rem;
    font-weight: 600;
    gap: 8px;
}

.trav-search-widget--hero .trav-search-badge { 
    font-size: 0.74rem; 
    gap: 8px; 
    cursor: pointer; 
}

.trav-search-check {
    appearance: none;
    background: #ffffff;
    border: 1.8px solid #54c7ea;
    border-radius: 4px;
    display: inline-block;
    height: 16px;
    margin: 0;
    position: relative;
    width: 16px;
}

.trav-search-check[disabled] { 
    cursor: default; 
    opacity: 1; 
}

.trav-search-check:checked { 
    background: #1bcbea; 
    border-color: #1bcbea; 
}

.trav-search-check:checked::after {
    border-bottom: 2px solid #ffffff;
    border-right: 2px solid #ffffff;
    content: "";
    height: 8px;
    left: 4px;
    position: absolute;
    top: 1px;
    transform: rotate(45deg);
    width: 4px;
}

.trav-search-link {
    background: transparent;
    border: 0;
    color: #0f88ca;
    font-size: 0.95rem;
    font-weight: 700;
    padding: 0;
    cursor: pointer;
}

.trav-search-widget--hero .trav-search-link { 
    font-size: 0.84rem; 
}

.trav-search-link:hover { 
    color: #0d6f98; 
}

.trav-search-form__actions { 
    display: flex; 
    justify-content: center; 
}

.trav-search-widget--hero .trav-search-form__actions { 
    margin-top: 6px; 
}

.btn-search {
    background: linear-gradient(135deg, #1ed4ea 0%, #0a81c8 100%);
    border: 0;
    border-radius: 999px;
    box-shadow: 0 12px 28px rgba(10, 129, 200, 0.3);
    color: #ffffff;
    font-size: 1.02rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    min-height: 54px;
    min-width: min(100%, 360px);
    padding: 0 46px;
    text-transform: uppercase;
    transition: all 0.18s ease;
    cursor: pointer;
}

.trav-search-widget--hero .btn-search {
    box-shadow: 0 8px 18px rgba(10, 129, 200, 0.28);
    font-size: 0.84rem;
    min-height: 38px;
    min-width: min(100%, 190px);
    padding: 0 28px;
}

.btn-search:hover {
    background: linear-gradient(135deg, #10c8e0 0%, #0976b8 100%);
    box-shadow: 0 14px 28px rgba(10, 129, 200, 0.38);
    transform: translateY(-2px);
}

/* ========== GUESTS PICKER ========== */
.guests-picker, .flight-passenger-picker, .flight-airport-field, .hotel-destination-field { 
    position: relative; 
}

.guests-trigger, .flight-passenger-trigger { 
    cursor: pointer; 
}

.guests-summary-input, .flight-passenger-summary { 
    cursor: pointer; 
}

.guests-menu, .flight-passenger-menu, .airport-suggest-list, .hotel-destination-suggest-list {
    background: #ffffff;
    border: 1px solid #eef2f6;
    border-radius: 20px;
    box-shadow: 0 16px 32px rgba(0, 0, 0, 0.12);
    display: none;
    left: 0;
    right: 0;
    top: calc(100% + 12px);
    padding: 12px;
    z-index: 100;
    max-height: 380px;
    overflow-y: auto;
}

.guests-menu::before, .guests-menu::after,
.flight-passenger-menu::before, .flight-passenger-menu::after,
.guests-picker::before, .guests-picker::after {
    display: none !important;
    content: none !important;
}

.trav-search-widget--hero .guests-menu {
    width: 300px;
    left: auto;
    right: 0;
    top: calc(100% + 12px);
}

.guests-picker.is-open .guests-menu,
.flight-passenger-picker.is-open .flight-passenger-menu,
.hotel-destination-suggest-list:not(.d-none),
.airport-suggest-list:not(.d-none) { 
    display: block; 
}

/* ========== ROOM CARD ========== */
.room-card {
    background: #ffffff;
    border: 1px solid #eef2f6;
    border-radius: 18px;
    padding: 14px;
    margin-bottom: 10px;
}

.room-card:last-child { 
    margin-bottom: 0; 
}

.room-card__head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f0f4f9;
}

.room-title {
    color: #1e3a4d;
    font-weight: 600;
    font-size: 0.85rem;
}

.room-remove {
    background: transparent;
    border: none;
    color: #e0746f;
    font-size: 0.7rem;
    font-weight: 500;
    padding: 4px 10px;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.room-remove:hover { 
    background: #fff5f5; 
    color: #c23d38; 
}

.guest-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
}

.guest-row + .guest-row { 
    margin-top: 0; 
}

.guest-label {
    color: #2c4a6e;
    font-weight: 500;
    font-size: 0.85rem;
}

.guest-sub {
    color: #8ba0ae;
    font-size: 0.7rem;
    margin-top: 2px;
}

.counter-wrap {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    border-radius: 30px;
    padding: 2px 6px;
}

.counter-btn {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 25px;
    color: #2c7da0;
    font-size: 0.9rem;
    font-weight: 600;
    height: 28px;
    width: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.counter-btn:hover {
    background: #2c7da0;
    border-color: #2c7da0;
    color: white;
}

.counter-val {
    color: #1e3a4d;
    font-weight: 600;
    min-width: 24px;
    text-align: center;
    font-size: 0.9rem;
}

.room-divider {
    border-top: 1px solid #f0f4f9;
    margin: 12px 0;
}

.child-age-row {
    background: #fafcff;
    border-radius: 12px;
    padding: 6px 10px;
    margin-top: 8px;
}

.child-age-row .guest-label { 
    font-size: 0.7rem; 
}

.btn-add-room {
    background: #f8fafc;
    border: 1px dashed #cbdde6;
    border-radius: 30px;
    color: #2c7da0;
    font-size: 0.75rem;
    font-weight: 500;
    margin-top: 10px;
    padding: 8px 14px;
    width: 100%;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-add-room:hover {
    background: #f1f9ff;
    border-color: #2c7da0;
}

/* ========== FLIGHT PASSENGER PICKER ========== */
.flight-passenger-menu {
    padding: 12px;
}

.flight-passenger-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
}

.flight-passenger-row + .flight-passenger-row { 
    margin-top: 4px; 
}

.flight-passenger-label {
    color: #2c4a6e;
    font-weight: 500;
    font-size: 0.85rem;
}

.flight-passenger-sub {
    color: #8ba0ae;
    font-size: 0.7rem;
    margin-top: 2px;
}

.flight-passenger-counter {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    border-radius: 30px;
    padding: 2px 6px;
}

.flight-passenger-btn {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 25px;
    color: #2c7da0;
    font-size: 0.9rem;
    font-weight: 600;
    height: 28px;
    width: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.flight-passenger-btn:hover {
    background: #2c7da0;
    border-color: #2c7da0;
    color: white;
}

.flight-passenger-val {
    color: #1e3a4d;
    font-weight: 600;
    min-width: 24px;
    text-align: center;
    font-size: 0.9rem;
}

/* ========== AIRPORT SUGGESTIONS ========== */
.airport-suggest-list {
    max-height: 300px;
}

.hotel-destination-suggest-list {
    max-height: 320px;
}

.airport-suggest-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    width: 100%;
    background: transparent;
    border: none;
    border-radius: 12px;
    text-align: left;
    cursor: pointer;
}

.airport-suggest-item:hover { 
    background: #f4fbff; 
}

.hotel-destination-suggest-item {
    align-items: flex-start;
}

.hotel-destination-suggest-item .airport-suggest-meta {
    gap: 2px;
    min-width: 0;
}

.hotel-destination-type {
    align-items: center;
    background: #eef8fd;
    border-radius: 999px;
    color: #0f88ca;
    display: inline-flex;
    font-size: 0.64rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    padding: 3px 8px;
    text-transform: uppercase;
}

.hotel-destination-badges {
    align-items: center;
    display: inline-flex;
    flex-shrink: 0;
    gap: 6px;
    margin-left: auto;
    padding-left: 10px;
}

.hotel-destination-suggest-item .airport-suggest-code {
    margin-left: 0;
}

.airport-suggest-code {
    background: #e7f7fc;
    border-radius: 999px;
    color: #0f88ca;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 4px 8px;
    margin-left: auto;
}

.airport-suggest-meta { 
    display: flex; 
    flex-direction: column; 
}

.airport-suggest-name { 
    font-weight: 700; 
    color: #12314d; 
}

.airport-suggest-sub { 
    font-size: 0.75rem; 
    color: #7893a7; 
}

.d-none { 
    display: none !important; 
}

.flight-round-trip-only.d-none, 
.flight-one-way-only.d-none { 
    display: none !important; 
}

/* ========== RESPONSIVE ========== */
@media (max-width: 991.98px) {
    .trav-field--third, .trav-field--half, .trav-field--quarter { 
        grid-column: span 6; 
    }
    .trav-search-widget--hero .trav-search-widget__nav { 
        width: calc(100% - 24px); 
    }
}

@media (max-width: 767.98px) {
    .trav-search-widget--hero { 
        max-width: 100%; 
    }
    .trav-search-widget--hero .trav-search-widget__shell { 
        border-radius: 26px; 
    }
    .trav-search-widget__topbar { 
        flex-wrap: nowrap; 
        overflow-x: auto; 
        padding-bottom: 4px; 
    }
    .trav-search-widget__topbar::-webkit-scrollbar { 
        display: none; 
    }
    .trav-search-tab { 
        flex: 0 0 auto; 
        font-size: 0.88rem; 
        min-height: 42px; 
        padding: 0 14px; 
    }
    
    .trav-search-widget--hero .trav-search-widget__nav {
        width: calc(100% - 12px);
        max-width: none;
        padding: 12px 12px 10px;
        margin: 0 auto;
        border-radius: 22px;
        display: block;
    }

    .trav-search-widget--hero .trav-search-widget__topbar {
        justify-content: flex-start;
        margin: 0;
        min-height: 46px;
        padding: 0;
        width: auto;
        border-radius: 0;
    }
    
    .trav-search-widget--hero #sw-hotels-form { 
        margin-top: -4px; 
    }

    .trav-search-widget--hero #sw-flights-form-container { 
        margin-top: 0; 
    }
    
    .trav-search-widget--hero .trav-search-form {
        border-radius: 20px;
        padding: 20px 14px 18px;
    }

    .trav-search-widget--hero #sw-flights-form-container .trav-search-form {
        padding-top: 22px;
    }

    .trav-search-widget--hero .trav-search-widget__subtabs {
        justify-content: flex-start;
        margin-top: 6px;
        margin-bottom: 0;
        padding: 0;
    }
    
    .trav-search-widget--hero [data-tab-pane="flights"] .trav-field--third,
    .trav-search-widget--hero [data-tab-pane="flights"] .trav-field--half { 
        grid-column: span 12; 
    }
    
    .trav-search-widget--hero .guests-menu {
        width: calc(100% - 20px);
        left: 10px;
        right: 10px;
    }
    
    .trav-field--third, .trav-field--half, .trav-field--quarter, .trav-field--wide { 
        grid-column: span 12; 
    }
    .trav-field__surface { 
        min-height: 68px; 
    }
    .trav-search-form__meta { 
        align-items: flex-start; 
        flex-direction: column; 
    }
    .trav-search-badges { 
        gap: 10px 14px; 
    }
    .btn-search { 
        min-width: 100%; 
    }
    
    .trav-search-widget__subtabs { 
        overflow-x: auto; 
        padding-bottom: 4px; 
    }
    .trav-search-widget__subtabs::-webkit-scrollbar { 
        display: none; 
    }
    .trav-search-widget--hero .trav-search-widget__subtabs { 
        padding: 0; 
    }

}
</style>
@endpush

<div class="trav-search-widget trav-search-widget--{{ $widgetVariant }}">
    <div class="trav-search-widget__shell">
        <div class="trav-search-widget__nav {{ $activeTab === 'flights' ? 'has-subtabs' : '' }}" id="searchTabsWrap">
            <div class="trav-search-widget__topbar" id="searchTabs">
                <button type="button" class="trav-search-tab search-tab-btn {{ $activeTab === 'hotels' ? 'active' : '' }}" data-tab="hotels">
                    <i class="bi bi-building"></i>
                    <span>Hotels</span>
                </button>
                <button type="button" class="trav-search-tab search-tab-btn {{ $activeTab === 'flights' ? 'active' : '' }}" data-tab="flights">
                    <i class="bi bi-airplane"></i>
                    <span>Flights</span>
                </button>
                <button type="button" class="trav-search-tab search-tab-btn {{ $activeTab === 'activities' ? 'active' : '' }}" data-tab="activities">
                    <i class="bi bi-compass"></i>
                    <span>Activities</span>
                </button>
                @if ($isHeroWidget)
                    <button type="button" class="trav-search-tab search-tab-btn {{ $activeTab === 'homes' ? 'active' : '' }}" data-tab="homes">
                        <i class="bi bi-house-door"></i>
                        <span>Homes &amp; Apts</span>
                    </button>
                    <button type="button" class="trav-search-tab search-tab-btn {{ $activeTab === 'flight_hotel' ? 'active' : '' }}" data-tab="flight_hotel">
                        <i class="bi bi-stars"></i>
                        <span>Flight + Hotel</span>
                    </button>
                    <button type="button" class="trav-search-tab trav-search-tab--placeholder">
                        <i class="bi bi-car-front"></i>
                        <span>Airport transfer</span>
                    </button>
                @endif
            </div>

            <div class="trav-search-widget__subtabs {{ $activeTab !== 'flights' ? 'd-none' : '' }}" id="flightSubtabsWrap">
                <div id="flight-subtabs" class="trav-flight-subtabs {{ $activeTab !== 'flights' ? 'd-none' : '' }}">
                    <button class="trip-type-btn {{ request('trip_type', 'one_way') === 'one_way' ? 'active' : '' }}" id="oneWayBtn" data-type="one_way" type="button">One-way</button>
                    <button class="trip-type-btn {{ request('trip_type') === 'round_trip' ? 'active' : '' }}" id="roundTripBtn" data-type="round_trip" type="button">Round trip</button>
                </div>
            </div>
        </div>

        <div id="sw-hotels-form" class="{{ $isHotelLikeTab ? '' : 'd-none' }}">
            <form id="hotelSearchForm" action="{{ Route::has('hotels.index') ? route('hotels.index') : '#' }}" method="GET" class="trav-search-form" data-hotel-destinations-url="{{ route('api.locations.search') }}">
                <div class="trav-search-grid">
                    <div class="trav-field trav-field--wide">
                        <label class="trav-field__label">Destination</label>
                        <div class="trav-field__surface hotel-destination-field">
                            <div class="input-icon-wrap">
                                <i class="bi bi-search input-icon"></i>
                                <input id="hotelDestinationInput" type="text" name="city" class="form-control search-input" placeholder="Enter a destination or property" value="{{ $hotelDestinationValue }}" autocomplete="off" />
                                <input type="hidden" name="destination" id="hotelDestinationMirror" value="{{ $isHotelLikeTab ? request('destination', $hotelDestinationValue) : $hotelDestinationValue }}" />
                                <input type="hidden" name="country" value="{{ request('country', '') }}" />
                                <input type="hidden" name="country_code" value="{{ request('country_code', '') }}" />
                                <input type="hidden" name="location" value="{{ request('location', '') }}" />
                                <input type="hidden" name="region" value="{{ request('region', '') }}" />
                            </div>
                            <div class="airport-suggest-list hotel-destination-suggest-list d-none" id="hotelDestinationSuggestList"></div>
                        </div>
                    </div>

                    <div class="trav-field trav-field--third">
                        <label class="trav-field__label">Check In</label>
                        <div class="trav-field__surface trav-field__surface--date">
                            <div class="input-icon-wrap">
                                <i class="bi bi-calendar3 input-icon"></i>
                                <input type="date" name="check_in" id="hotelCheckInInput" class="form-control search-input" value="{{ request('check_in', $defaultHotelCheckIn) }}" min="{{ now()->format('Y-m-d') }}" />
                            </div>
                            <div class="trav-field__helper" id="hotelCheckInDay">{{ \Carbon\Carbon::parse(request('check_in', $defaultHotelCheckIn))->format('l') }}</div>
                        </div>
                    </div>

                    <div class="trav-field trav-field--third">
                        <label class="trav-field__label">Check Out</label>
                        <div class="trav-field__surface trav-field__surface--date">
                            <div class="input-icon-wrap">
                                <i class="bi bi-calendar3 input-icon"></i>
                                <input type="date" name="check_out" id="hotelCheckOutInput" class="form-control search-input" value="{{ request('check_out', $defaultHotelCheckOut) }}" min="{{ now()->format('Y-m-d') }}" />
                            </div>
                            <div class="trav-field__helper" id="hotelCheckOutDay">{{ \Carbon\Carbon::parse(request('check_out', $defaultHotelCheckOut))->format('l') }}</div>
                        </div>
                    </div>

                    <div class="trav-field trav-field--third">
                        <label class="trav-field__label">Guests</label>
                        <div class="guests-picker" id="guestsPicker">
                            <div class="trav-field__surface">
                                <div class="input-icon-wrap guests-trigger" role="button" tabindex="0" aria-expanded="false">
                                    <i class="bi bi-people input-icon"></i>
                                    <input id="guestsSummary" type="text" class="form-control search-input guests-summary-input" value="" readonly />
                                </div>
                            </div>
                            <div class="guests-menu" id="guestsMenu">
                                <div id="roomsContainer"></div>
                                <button type="button" class="btn-add-room" id="addRoomBtn" style="display:none;">+ Add Room</button>
                            </div>
                        </div>
                        <div id="guestsHiddenFields"></div>
                    </div>
                </div>

                @if ($isHeroWidget)
                    <div class="trav-search-form__meta">
                        <div class="trav-search-badges">
                            <label class="trav-search-badge">
                                <input class="trav-search-check" type="checkbox" checked disabled>
                                <span>Free breakfast</span>
                            </label>
                            <label class="trav-search-badge">
                                <input class="trav-search-check" type="checkbox" checked disabled>
                                <span>Free cancellation</span>
                            </label>
                            <label class="trav-search-badge">
                                <input class="trav-search-check" type="checkbox" checked disabled>
                                <span>Star Rating: 4+</span>
                            </label>
                            <label class="trav-search-badge">
                                <input class="trav-search-check" type="checkbox" checked disabled>
                                <span>Review score: 8+</span>
                            </label>
                        </div>
                        <button type="button" class="trav-search-link" data-tab-jump="flights">+ Add a flight</button>
                    </div>
                @endif

                <div class="trav-search-form__actions">
                    <button type="submit" class="btn btn-search">
                        <i class="bi bi-search me-2"></i>Search
                    </button>
                </div>
            </form>
        </div>

        <div id="sw-flights-form-container" class="{{ $isHotelLikeTab ? 'd-none' : '' }}">
            <form action="{{ Route::has('flights.index') ? route('flights.index') : '#' }}" method="GET" id="flightSearchForm" class="trav-search-form" data-airports-url="{{ route('api.locations.airports.search') }}" data-hotel-destinations-url="{{ route('api.locations.search') }}">
                <input type="hidden" name="trip_type" id="tripTypeInput" value="{{ request('trip_type', 'one_way') }}" />

                <div data-tab-pane="flights" class="{{ $activeTab !== 'flights' ? 'd-none' : '' }}">
                    <div class="trav-search-grid">
                        <div class="trav-field trav-field--third flight-one-way-only">
                            <label class="trav-field__label">From</label>
                            <div class="trav-field__surface flight-airport-field">
                                <div class="input-icon-wrap">
                                    <i class="bi bi-send input-icon"></i>
                                    <input type="text" name="origin_display" class="form-control search-input airport-autocomplete" placeholder="Departure city or IATA" value="{{ request()->filled('origin_display') ? request('origin_display') : (request()->filled('origin') ? request('origin') : '') }}" autocomplete="off" data-airport-input="origin" />
                                    <input type="hidden" name="origin" value="{{ request('origin') }}" data-airport-code="origin" />
                                    <div class="airport-suggest-list d-none" data-airport-list="origin"></div>
                                </div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--third flight-one-way-only">
                            <label class="trav-field__label">To</label>
                            <div class="trav-field__surface flight-airport-field">
                                <div class="input-icon-wrap">
                                    <i class="bi bi-geo-alt input-icon"></i>
                                    <input type="text" name="destination_display" class="form-control search-input airport-autocomplete" placeholder="Destination city or IATA" value="{{ in_array($activeTab, ['flights', 'flight_hotel']) ? (request()->filled('destination_display') ? request('destination_display') : (request()->filled('destination') ? request('destination') : '')) : '' }}" autocomplete="off" data-airport-input="destination" />
                                    <input type="hidden" name="destination" value="{{ in_array($activeTab, ['flights', 'flight_hotel']) ? request('destination') : '' }}" data-airport-code="destination" />
                                    <div class="airport-suggest-list d-none" data-airport-list="destination"></div>
                                </div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--third flight-one-way-only">
                            <label class="trav-field__label">Departure</label>
                            <div class="trav-field__surface">
                                <div class="input-icon-wrap">
                                    <i class="bi bi-calendar3 input-icon"></i>
                                    <input type="date" name="departure_date" class="form-control search-input" value="{{ request('departure_date', now()->addDay()->format('Y-m-d')) }}" min="{{ now()->format('Y-m-d') }}" />
                                </div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--third flight-one-way-only">
                            <label class="trav-field__label">Passengers</label>
                            <div class="flight-passenger-picker" data-flight-pax-picker>
                                <div class="trav-field__surface">
                                    <div class="input-icon-wrap flight-passenger-trigger" role="button" tabindex="0" aria-expanded="false">
                                        <i class="bi bi-people input-icon"></i>
                                        <input type="text" class="form-control search-input flight-passenger-summary" value="" readonly />
                                    </div>
                                </div>
                                <div class="flight-passenger-menu">
                                    <div class="flight-passenger-row" data-type="adults" data-min="1" data-max="9">
                                        <div><div class="flight-passenger-label">Adult</div><div class="flight-passenger-sub">18+ years old</div></div>
                                        <div class="flight-passenger-counter">
                                            <button type="button" class="flight-passenger-btn" data-delta="-1">-</button>
                                            <span class="flight-passenger-val">1</span>
                                            <button type="button" class="flight-passenger-btn" data-delta="1">+</button>
                                        </div>
                                    </div>
                                    <div class="flight-passenger-row" data-type="children" data-min="0" data-max="9">
                                        <div><div class="flight-passenger-label">Child</div><div class="flight-passenger-sub">0-17 years old</div></div>
                                        <div class="flight-passenger-counter">
                                            <button type="button" class="flight-passenger-btn" data-delta="-1">-</button>
                                            <span class="flight-passenger-val">0</span>
                                            <button type="button" class="flight-passenger-btn" data-delta="1">+</button>
                                        </div>
                                    </div>
                                    <div class="flight-passenger-row" data-type="infants" data-min="0" data-max="9">
                                        <div><div class="flight-passenger-label">Infant</div><div class="flight-passenger-sub">Under 2 years old</div></div>
                                        <div class="flight-passenger-counter">
                                            <button type="button" class="flight-passenger-btn" data-delta="-1">-</button>
                                            <span class="flight-passenger-val">0</span>
                                            <button type="button" class="flight-passenger-btn" data-delta="1">+</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="adults" value="{{ max(1, (int) request('adults', 1)) }}">
                                    <input type="hidden" name="children" value="{{ max(0, (int) request('children', 0)) }}">
                                    <input type="hidden" name="infants" value="{{ max(0, (int) request('infants', 0)) }}">
                                </div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--third flight-round-trip-only d-none">
                            <label class="trav-field__label">From</label>
                            <div class="trav-field__surface flight-airport-field">
                                <div class="input-icon-wrap">
                                    <i class="bi bi-send input-icon"></i>
                                    <input type="text" name="origin_display" class="form-control search-input airport-autocomplete" placeholder="Departure city or IATA" value="{{ request()->filled('origin_display') ? request('origin_display') : (request()->filled('origin') ? request('origin') : '') }}" autocomplete="off" data-airport-input="origin" />
                                    <input type="hidden" name="origin" value="{{ request('origin') }}" data-airport-code="origin" />
                                    <div class="airport-suggest-list d-none" data-airport-list="origin"></div>
                                </div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--third flight-round-trip-only d-none">
                            <label class="trav-field__label">To</label>
                            <div class="trav-field__surface flight-airport-field">
                                <div class="input-icon-wrap">
                                    <i class="bi bi-geo-alt input-icon"></i>
                                    <input type="text" name="destination_display" class="form-control search-input airport-autocomplete" placeholder="Destination city or IATA" value="{{ in_array($activeTab, ['flights', 'flight_hotel']) ? (request()->filled('destination_display') ? request('destination_display') : (request()->filled('destination') ? request('destination') : '')) : '' }}" autocomplete="off" data-airport-input="destination" />
                                    <input type="hidden" name="destination" value="{{ in_array($activeTab, ['flights', 'flight_hotel']) ? request('destination') : '' }}" data-airport-code="destination" />
                                    <div class="airport-suggest-list d-none" data-airport-list="destination"></div>
                                </div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--third flight-round-trip-only d-none">
                            <label class="trav-field__label">Departure</label>
                            <div class="trav-field__surface">
                                <div class="input-icon-wrap">
                                    <i class="bi bi-calendar3 input-icon"></i>
                                    <input type="date" name="departure_date" class="form-control search-input" value="{{ request('departure_date', now()->addDay()->format('Y-m-d')) }}" min="{{ now()->format('Y-m-d') }}" />
                                </div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--third flight-round-trip-only d-none" id="returnDateWrap">
                            <label class="trav-field__label">Return</label>
                            <div class="trav-field__surface">
                                <div class="input-icon-wrap">
                                    <i class="bi bi-calendar3 input-icon"></i>
                                    <input type="date" name="return_date" class="form-control search-input" value="{{ request('return_date') }}" min="{{ now()->format('Y-m-d') }}" />
                                </div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--half flight-one-way-only">
                            <label class="trav-field__label">Cabin</label>
                            <div class="trav-field__surface">
                                <select name="cabin_class" class="form-select search-input">
                                    @foreach (['ECONOMY' => 'Economy', 'PREMIUM_ECONOMY' => 'Premium Economy', 'BUSINESS' => 'Business', 'FIRST' => 'First'] as $val => $label)
                                        <option value="{{ $val }}" {{ request('cabin_class', 'ECONOMY') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="trav-field trav-field--half flight-round-trip-only d-none">
                            <label class="trav-field__label">Passengers</label>
                            <div class="flight-passenger-picker" data-flight-pax-picker>
                                <div class="trav-field__surface">
                                    <div class="input-icon-wrap flight-passenger-trigger" role="button" tabindex="0" aria-expanded="false">
                                        <i class="bi bi-people input-icon"></i>
                                        <input type="text" class="form-control search-input flight-passenger-summary" value="" readonly />
                                    </div>
                                </div>
                                <div class="flight-passenger-menu">
                                    <div class="flight-passenger-row" data-type="adults" data-min="1" data-max="9">
                                        <div><div class="flight-passenger-label">Adult</div><div class="flight-passenger-sub">18+ years old</div></div>
                                        <div class="flight-passenger-counter">
                                            <button type="button" class="flight-passenger-btn" data-delta="-1">-</button>
                                            <span class="flight-passenger-val">1</span>
                                            <button type="button" class="flight-passenger-btn" data-delta="1">+</button>
                                        </div>
                                    </div>
                                    <div class="flight-passenger-row" data-type="children" data-min="0" data-max="9">
                                        <div><div class="flight-passenger-label">Child</div><div class="flight-passenger-sub">0-17 years old</div></div>
                                        <div class="flight-passenger-counter">
                                            <button type="button" class="flight-passenger-btn" data-delta="-1">-</button>
                                            <span class="flight-passenger-val">0</span>
                                            <button type="button" class="flight-passenger-btn" data-delta="1">+</button>
                                        </div>
                                    </div>
                                    <div class="flight-passenger-row" data-type="infants" data-min="0" data-max="9">
                                        <div><div class="flight-passenger-label">Infant</div><div class="flight-passenger-sub">Under 2 years old</div></div>
                                        <div class="flight-passenger-counter">
                                            <button type="button" class="flight-passenger-btn" data-delta="-1">-</button>
                                            <span class="flight-passenger-val">0</span>
                                            <button type="button" class="flight-passenger-btn" data-delta="1">+</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="adults" value="{{ max(1, (int) request('adults', 1)) }}">
                                    <input type="hidden" name="children" value="{{ max(0, (int) request('children', 0)) }}">
                                    <input type="hidden" name="infants" value="{{ max(0, (int) request('infants', 0)) }}">
                                </div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--half flight-round-trip-only d-none">
                            <label class="trav-field__label">Cabin</label>
                            <div class="trav-field__surface">
                                <select name="cabin_class" class="form-select search-input">
                                    @foreach (['ECONOMY' => 'Economy', 'PREMIUM_ECONOMY' => 'Premium Economy', 'BUSINESS' => 'Business', 'FIRST' => 'First'] as $val => $label)
                                        <option value="{{ $val }}" {{ request('cabin_class', 'ECONOMY') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    @if ($isHeroWidget)
                        <div class="trav-flight-upsell">
                            <label class="trav-flight-upsell__label">
                                <input class="trav-search-check" type="checkbox" disabled>
                                <span>Add hotel to save up to 25%</span>
                            </label>
                            <span class="trav-flight-upsell__badge">Bundle and Save</span>
                        </div>
                    @endif
                </div>

                <div data-tab-pane="activities" class="{{ $activeTab !== 'activities' ? 'd-none' : '' }}">
                    <div class="trav-search-grid">
                        <div class="trav-field trav-field--wide">
                            <label class="trav-field__label">Location</label>
                            <div class="trav-field__surface hotel-destination-field activity-destination-field">
                                <div class="input-icon-wrap">
                                    <i class="bi bi-search input-icon"></i>
                                    <input id="activityDestinationInput" type="text" class="form-control search-input" name="city" placeholder="Enter city or experience" value="{{ $activityCityValue }}" {{ $activeTab !== 'activities' ? 'disabled' : '' }} />
                                    <input id="activityCountryInput" type="hidden" name="country" value="{{ request('country', '') }}" {{ $activeTab !== 'activities' ? 'disabled' : '' }} />
                                    <input id="activityCountryCodeInput" type="hidden" name="country_code" value="{{ request('country_code', '') }}" {{ $activeTab !== 'activities' ? 'disabled' : '' }} />
                                    <input id="activityLocationInput" type="hidden" name="location" value="{{ request('location', '') }}" {{ $activeTab !== 'activities' ? 'disabled' : '' }} />
                                </div>
                                <div class="airport-suggest-list hotel-destination-suggest-list d-none" id="activityDestinationSuggestList"></div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--half">
                            <label class="trav-field__label">Date</label>
                            <div class="trav-field__surface">
                                <div class="input-icon-wrap">
                                    <i class="bi bi-calendar3 input-icon"></i>
                                    <input type="date" class="form-control search-input" name="activity_date" value="{{ request('activity_date', now()->format('Y-m-d')) }}" min="{{ now()->format('Y-m-d') }}" {{ $activeTab !== 'activities' ? 'disabled' : '' }} />
                                </div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--half">
                            <label class="trav-field__label">Participants</label>
                            <div class="trav-field__surface">
                                <div class="input-icon-wrap">
                                    <i class="bi bi-people input-icon"></i>
                                    <input type="number" class="form-control search-input" name="participants" min="1" max="20" value="{{ max(1, (int) request('participants', 1)) }}" placeholder="1" {{ $activeTab !== 'activities' ? 'disabled' : '' }} />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div data-tab-pane="flight_hotel" class="{{ $activeTab !== 'flight_hotel' ? 'd-none' : '' }}">
                    <div class="trav-search-grid">
                        <div class="trav-field trav-field--half">
                            <label class="trav-field__label">Trip Type</label>
                            <div class="trav-field__surface">
                                <div class="input-icon-wrap">
                                    <i class="bi bi-arrow-left-right input-icon"></i>
                                    <select name="bundle_trip_type" id="flightHotelTripType" class="form-select search-input" {{ $activeTab !== 'flight_hotel' ? 'disabled' : '' }}>
                                        <option value="one_way" {{ request('trip_type', 'round_trip') === 'one_way' ? 'selected' : '' }}>One-way</option>
                                        <option value="round_trip" {{ request('trip_type', 'round_trip') === 'round_trip' ? 'selected' : '' }}>Round-trip</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--half">
                            <label class="trav-field__label">Cabin</label>
                            <div class="trav-field__surface">
                                <div class="input-icon-wrap">
                                    <i class="bi bi-chevron-down input-icon"></i>
                                    <select name="cabin_class" class="form-select search-input" {{ $activeTab !== 'flight_hotel' ? 'disabled' : '' }}>
                                        @foreach (['ECONOMY' => 'Economy', 'PREMIUM_ECONOMY' => 'Premium Economy', 'BUSINESS' => 'Business', 'FIRST' => 'First'] as $val => $label)
                                            <option value="{{ $val }}" {{ request('cabin_class', 'ECONOMY') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--half">
                            <label class="trav-field__label">Flying From</label>
                            <div class="trav-field__surface flight-airport-field">
                                <div class="input-icon-wrap">
                                    <i class="bi bi-send input-icon"></i>
                                    <input type="text" name="origin" class="form-control search-input airport-autocomplete" placeholder="Flying from city or airport" value="{{ request('origin') }}" autocomplete="off" data-airport-input="origin" {{ $activeTab !== 'flight_hotel' ? 'disabled' : '' }} />
                                    <div class="airport-suggest-list d-none" data-airport-list="origin"></div>
                                </div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--half">
                            <label class="trav-field__label">Flying To</label>
                            <div class="trav-field__surface flight-airport-field">
                                <div class="input-icon-wrap">
                                    <i class="bi bi-geo-alt input-icon"></i>
                                    <input type="text" name="destination" class="form-control search-input airport-autocomplete" placeholder="Flying to city or airport" value="{{ in_array($activeTab, ['flights', 'flight_hotel']) ? request('destination') : '' }}" autocomplete="off" data-airport-input="destination" {{ $activeTab !== 'flight_hotel' ? 'disabled' : '' }} />
                                    <div class="airport-suggest-list d-none" data-airport-list="destination"></div>
                                </div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--half">
                            <label class="trav-field__label">Staying At</label>
                            <div class="trav-field__surface hotel-destination-field">
                                <div class="input-icon-wrap">
                                    <i class="bi bi-building input-icon"></i>
                                    <input id="flightHotelDestinationInput" type="text" name="bundle_hotel_city" class="form-control search-input" placeholder="Enter a destination or property" value="{{ request('bundle_hotel_city', request('city', $hotelDestinationValue)) }}" autocomplete="off" {{ $activeTab !== 'flight_hotel' ? 'disabled' : '' }} />
                                </div>
                                <div class="airport-suggest-list hotel-destination-suggest-list d-none" id="flightHotelDestinationSuggestList"></div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--quarter">
                            <label class="trav-field__label">Check-in</label>
                            <div class="trav-field__surface">
                                <div class="input-icon-wrap">
                                    <i class="bi bi-calendar3 input-icon"></i>
                                    <input type="date" name="departure_date" class="form-control search-input" value="{{ request('departure_date', request('check_in', $defaultHotelCheckIn)) }}" min="{{ now()->format('Y-m-d') }}" {{ $activeTab !== 'flight_hotel' ? 'disabled' : '' }} />
                                </div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--quarter">
                            <label class="trav-field__label">Check-out</label>
                            <div class="trav-field__surface">
                                <div class="input-icon-wrap">
                                    <i class="bi bi-calendar3 input-icon"></i>
                                    <input type="date" name="return_date" class="form-control search-input" value="{{ request('return_date', request('check_out', $defaultHotelCheckOut)) }}" min="{{ now()->format('Y-m-d') }}" {{ $activeTab !== 'flight_hotel' ? 'disabled' : '' }} />
                                </div>
                            </div>
                        </div>

                        <div class="trav-field trav-field--wide">
                            <label class="trav-search-badge">
                                <input class="trav-search-check" type="checkbox" name="bundle_flexible_hotel" value="1" {{ request()->boolean('bundle_flexible_hotel') ? 'checked' : '' }} {{ $activeTab !== 'flight_hotel' ? 'disabled' : '' }}>
                                <span>Search for hotel in different cities or dates</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="trav-search-form__actions">
                    <button type="submit" class="btn btn-search" id="flightSearchSubmitBtn">
                        <i class="bi bi-search me-2"></i>
                        <span data-search-btn-text>
                            @if ($activeTab === 'activities')
                                Search Activities
                            @elseif ($activeTab === 'flight_hotel')
                                Search Flight + Hotel
                            @else
                                Search Flights
                            @endif
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const activeTabInit = '{{ $activeTab }}';
    const tabButtons = [...document.querySelectorAll('#searchTabs .search-tab-btn[data-tab]')];
    const searchTabsWrap = document.getElementById('searchTabsWrap');
    const hotelsContainer = document.getElementById('sw-hotels-form');
    const flightsContainer = document.getElementById('sw-flights-form-container');
    const tabPanes = [...document.querySelectorAll('[data-tab-pane]')];
    const flightSubtabs = document.getElementById('flight-subtabs');
    const flightSubtabsWrap = document.getElementById('flightSubtabsWrap');
    const submitBtn = document.getElementById('flightSearchSubmitBtn');
    const submitText = submitBtn ? submitBtn.querySelector('[data-search-btn-text]') : null;
    const hotelSearchForm = document.getElementById('hotelSearchForm');
    const hotelDestinationInput = document.getElementById('hotelDestinationInput');
    const hotelDestinationMirror = document.getElementById('hotelDestinationMirror');
    const hotelDestinationList = document.getElementById('hotelDestinationSuggestList');
    const flightHotelDestinationInput = document.getElementById('flightHotelDestinationInput');
    const flightHotelDestinationList = document.getElementById('flightHotelDestinationSuggestList');
    const activityDestinationInput = document.getElementById('activityDestinationInput');
    const activityDestinationList = document.getElementById('activityDestinationSuggestList');
    const activityCountryInput = document.getElementById('activityCountryInput');
    const activityCountryCodeInput = document.getElementById('activityCountryCodeInput');
    const activityLocationInput = document.getElementById('activityLocationInput');
    const hotelCheckInInput = document.getElementById('hotelCheckInInput');
    const hotelCheckOutInput = document.getElementById('hotelCheckOutInput');
    const hotelCheckInDay = document.getElementById('hotelCheckInDay');
    const hotelCheckOutDay = document.getElementById('hotelCheckOutDay');
    const flightHotelTripType = document.getElementById('flightHotelTripType');
    const tripInput = document.getElementById('tripTypeInput');
    const oneWayBtn = document.getElementById('oneWayBtn');
    const roundTripBtn = document.getElementById('roundTripBtn');
    const oneWayOnlyFields = document.querySelectorAll('.flight-one-way-only');
    const roundTripOnlyFields = document.querySelectorAll('.flight-round-trip-only');
    const hotelDestinationsUrl = (hotelSearchForm && hotelSearchForm.dataset.hotelDestinationsUrl)
        || (document.getElementById('flightSearchForm')?.dataset.hotelDestinationsUrl)
        || '';
    const hotelCountryInput = hotelSearchForm ? hotelSearchForm.querySelector('input[name="country"]') : null;
    const hotelCountryCodeInput = hotelSearchForm ? hotelSearchForm.querySelector('input[name="country_code"]') : null;
    const hotelLocationInput = hotelSearchForm ? hotelSearchForm.querySelector('input[name="location"]') : null;
    const hotelRegionInput = hotelSearchForm ? hotelSearchForm.querySelector('input[name="region"]') : null;

    const tabTextMap = {
        flights: 'Search Flights',
        activities: 'Search Activities',
        flight_hotel: 'Search Flight + Hotel',
        homes: 'Search Homes',
        hotels: 'Search Hotels',
    };

    const tabActionMap = {
        flight_hotel: "{{ Route::has('flights.index') ? route('flights.index') : '#' }}",
        flights: "{{ Route::has('flights.index') ? route('flights.index') : '#' }}",
        activities: "{{ Route::has('activities.index') ? route('activities.index') : '#' }}",
    };

    let activeTopTab = activeTabInit;
    const hotelLikeTabs = new Set(['hotels', 'homes']);

    const ensureFlightSubtabsVisible = () => {
        if (flightSubtabs) {
            flightSubtabs.classList.remove('d-none');
            flightSubtabs.style.display = 'flex';
        }
        if (flightSubtabsWrap) {
            flightSubtabsWrap.classList.remove('d-none');
            flightSubtabsWrap.style.display = 'flex';
        }
        if (searchTabsWrap) {
            searchTabsWrap.classList.add('has-subtabs');
        }
    };

    const hideFlightSubtabs = () => {
        if (flightSubtabs) {
            flightSubtabs.classList.add('d-none');
            flightSubtabs.style.display = 'none';
        }
        if (flightSubtabsWrap) {
            flightSubtabsWrap.classList.add('d-none');
            flightSubtabsWrap.style.display = 'none';
        }
        if (searchTabsWrap) {
            searchTabsWrap.classList.remove('has-subtabs');
        }
    };

    const setPaneEnabled = (pane, enabled) => {
        pane.querySelectorAll('input, select, textarea, button').forEach((field) => {
            if (field.id === 'flightSearchSubmitBtn') return;
            if (!field.name) return;
            field.disabled = !enabled;
        });
    };

    const setActiveTopTab = (tab) => {
        activeTopTab = tab;
        const isHotelLike = hotelLikeTabs.has(tab);

        if (hotelsContainer) hotelsContainer.classList.toggle('d-none', !isHotelLike);
        if (flightsContainer) flightsContainer.classList.toggle('d-none', isHotelLike);

        tabButtons.forEach((btn) => btn.classList.toggle('active', btn.dataset.tab === tab));

        if (!isHotelLike) {
            tabPanes.forEach((pane) => {
                const isActive = pane.dataset.tabPane === tab;
                pane.classList.toggle('d-none', !isActive);
                setPaneEnabled(pane, isActive);
            });

            if (tab === 'flights') {
                ensureFlightSubtabsVisible();
                setTripType(tripInput ? (tripInput.value || 'one_way') : 'one_way');
            } else {
                hideFlightSubtabs();
                if (tab === 'flight_hotel' && flightHotelTripType && tripInput) {
                    tripInput.value = flightHotelTripType.value || 'round_trip';
                }
            }

            if (submitText) {
                submitText.textContent = tabTextMap[tab] || 'Search';
            }

            const formEl = document.getElementById('flightSearchForm');
            if (formEl) {
                formEl.action = tabActionMap[tab] || tabActionMap.flights;
            }
        } else {
            hideFlightSubtabs();
        }
    };

    const clearHotelDestinationMeta = () => {
        if (hotelCountryInput) hotelCountryInput.value = '';
        if (hotelCountryCodeInput) hotelCountryCodeInput.value = '';
        if (hotelLocationInput) hotelLocationInput.value = '';
        if (hotelRegionInput) hotelRegionInput.value = '';
    };

    const syncHotelDestination = () => {
        if (hotelDestinationMirror && hotelDestinationInput) {
            hotelDestinationMirror.value = hotelDestinationInput.value || '';
        }
    };

    if (hotelDestinationInput && hotelDestinationMirror) {
        hotelDestinationInput.addEventListener('input', syncHotelDestination);

        if (hotelSearchForm) {
            hotelSearchForm.addEventListener('submit', () => {
                syncHotelDestination();
                hideHotelDestinationList();
            });
        }
    }

    const updateDayLabel = (input, output) => {
        if (!input || !output) return;
        const value = String(input.value || '').trim();
        if (!value) {
            output.textContent = '';
            return;
        }

        const date = new Date(`${value}T12:00:00`);
        if (Number.isNaN(date.getTime())) {
            output.textContent = '';
            return;
        }

        output.textContent = new Intl.DateTimeFormat('en-US', { weekday: 'long' }).format(date);
    };

    if (hotelCheckInInput && hotelCheckInDay) {
        updateDayLabel(hotelCheckInInput, hotelCheckInDay);
        hotelCheckInInput.addEventListener('input', () => updateDayLabel(hotelCheckInInput, hotelCheckInDay));
        hotelCheckInInput.addEventListener('change', () => updateDayLabel(hotelCheckInInput, hotelCheckInDay));
    }

    if (hotelCheckOutInput && hotelCheckOutDay) {
        updateDayLabel(hotelCheckOutInput, hotelCheckOutDay);
        hotelCheckOutInput.addEventListener('input', () => updateDayLabel(hotelCheckOutInput, hotelCheckOutDay));
        hotelCheckOutInput.addEventListener('change', () => updateDayLabel(hotelCheckOutInput, hotelCheckOutDay));
    }

    tabButtons.forEach((btn) => btn.addEventListener('click', () => setActiveTopTab(btn.dataset.tab)));

    document.querySelectorAll('[data-tab-jump]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const targetTab = btn.dataset.tabJump;
            setActiveTopTab(targetTab);
            if (targetTab === 'flights') {
                ensureFlightSubtabsVisible();
                requestAnimationFrame(() => {
                    setTripType(tripInput ? (tripInput.value || 'one_way') : 'one_way');
                });
            }
        });
    });

    setActiveTopTab(activeTabInit);

    (function () {
        const guestsPicker = document.getElementById('guestsPicker');
        const guestsTrigger = guestsPicker ? guestsPicker.querySelector('.guests-trigger') : null;
        const guestsSummary = document.getElementById('guestsSummary');
        const roomsContainer = document.getElementById('roomsContainer');
        const addRoomBtn = document.getElementById('addRoomBtn');
        const guestsHiddenFields = document.getElementById('guestsHiddenFields');
        const isHeroGuestsPicker = guestsPicker ? guestsPicker.closest('.trav-search-widget--hero') !== null : false;
        const initialAdults = {{ $initialHotelAdults }};
        const initialChildren = {{ $initialHotelChildren }};
        const initialUnits = {{ $initialHotelUnits }};
        let rooms = [{ adults: initialAdults, children: initialChildren, unit: initialUnits, childAges: Array(initialChildren).fill(0) }];

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

            if (guestsPicker.classList.contains('is-open')) {
                requestAnimationFrame(positionGuestsMenu);
            }
        };

        const closeGuestsPicker = () => {
            guestsPicker.classList.remove('is-open');
            guestsPicker.classList.remove('open-up');
            if (guestsMenu) {
                guestsMenu.style.maxHeight = '';
            }
            guestsTrigger.setAttribute('aria-expanded', 'false');
        };

        const positionGuestsMenu = () => {
            if (!guestsMenu || !guestsPicker.classList.contains('is-open')) return;

            guestsPicker.classList.remove('open-up');
            guestsMenu.style.maxHeight = '';

            const pickerRect = guestsPicker.getBoundingClientRect();
            const menuRect = guestsMenu.getBoundingClientRect();
            const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
            const spaceBelow = viewportHeight - pickerRect.bottom - 16;
            const spaceAbove = pickerRect.top - 16;
            const menuHeight = menuRect.height || guestsMenu.scrollHeight || 0;
            const preferOpenUp = isHeroGuestsPicker && window.innerWidth >= 768;

            if (preferOpenUp && spaceAbove > 120) {
                guestsPicker.classList.add('open-up');
                const available = Math.max(120, Math.min(spaceAbove, menuHeight || spaceAbove));
                guestsMenu.style.maxHeight = `${available}px`;
            } else if (spaceBelow < menuHeight && spaceAbove > spaceBelow) {
                guestsPicker.classList.add('open-up');
                const available = Math.max(120, Math.min(spaceAbove, menuHeight || spaceAbove));
                guestsMenu.style.maxHeight = `${available}px`;
            } else if (spaceBelow > 0) {
                const available = Math.max(120, Math.min(spaceBelow, menuHeight || spaceBelow));
                guestsMenu.style.maxHeight = `${available}px`;
            }
        };

        guestsTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const willOpen = !guestsPicker.classList.contains('is-open');

            if (willOpen) {
                guestsPicker.classList.add('is-open');
                guestsTrigger.setAttribute('aria-expanded', 'true');
                requestAnimationFrame(positionGuestsMenu);
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

        window.addEventListener('resize', positionGuestsMenu);
        window.addEventListener('scroll', positionGuestsMenu, true);

        document.addEventListener('click', (e) => {
            if (!guestsPicker.contains(e.target)) closeGuestsPicker();
        });

        renderRooms();
    })();

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
            oneWayBtn && oneWayBtn.classList.remove('active');
            roundTripBtn && roundTripBtn.classList.add('active');
            toggleFieldGroup(oneWayOnlyFields, false);
            toggleFieldGroup(roundTripOnlyFields, true);
        } else {
            oneWayBtn && oneWayBtn.classList.add('active');
            roundTripBtn && roundTripBtn.classList.remove('active');
            toggleFieldGroup(oneWayOnlyFields, true);
            toggleFieldGroup(roundTripOnlyFields, false);
        }
    }

    if (oneWayBtn) oneWayBtn.addEventListener('click', () => setTripType('one_way'));
    if (roundTripBtn) roundTripBtn.addEventListener('click', () => setTripType('round_trip'));
    if (flightHotelTripType) {
        flightHotelTripType.addEventListener('change', () => {
            if (tripInput) tripInput.value = flightHotelTripType.value || 'round_trip';
        });
    }
    setTripType(tripInput ? (tripInput.value || 'one_way') : 'one_way');

    const flightForm = document.getElementById('flightSearchForm');
    const airportsUrl = flightForm ? flightForm.dataset.airportsUrl : '';
    const originInputs = [...document.querySelectorAll('[data-airport-input="origin"]')];
    const destinationInputs = [...document.querySelectorAll('[data-airport-input="destination"]')];
    const originLists = [...document.querySelectorAll('[data-airport-list="origin"]')];
    const destinationLists = [...document.querySelectorAll('[data-airport-list="destination"]')];
    const originCodes = [...document.querySelectorAll('[data-airport-code="origin"]')];
    const destinationCodes = [...document.querySelectorAll('[data-airport-code="destination"]')];
    const airportRequestState = new WeakMap();
    let activeList = null;

    const debounce = (fn, delay = 280) => {
        let timer = null;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), delay);
        };
    };

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const hideList = (list) => {
        if (!list) return;
        list.classList.add('d-none');
        list.innerHTML = '';
        if (activeList === list) activeList = null;
    };

    const hideAllLists = () => {
        [...originLists, ...destinationLists].forEach((list) => hideList(list));
    };

    const hideDestinationList = (list) => {
        if (!list) return;
        list.classList.add('d-none');
        list.innerHTML = '';
    };

    const hideHotelDestinationList = () => hideDestinationList(hotelDestinationList);
    const hideFlightHotelDestinationList = () => hideDestinationList(flightHotelDestinationList);
    const hideActivityDestinationList = () => hideDestinationList(activityDestinationList);

    const syncAirportInputs = (type, value) => {
        const inputs = type === 'origin' ? originInputs : destinationInputs;
        inputs.forEach((input) => {
            input.value = value;
        });
    };

    const syncAirportCodes = (type, code) => {
        if (type === 'origin') {
            originCodes.forEach((input) => {
                input.value = code || '';
            });
        } else if (type === 'destination') {
            destinationCodes.forEach((input) => {
                input.value = code || '';
            });
        }
    };

    const formatAirportSelection = (item = {}) => {
        const city = (item.city || '').trim();
        const code = (item.code || '').trim().toUpperCase();

        if (city && code) {
            return `${city} (${code})`;
        }

        return city || code || '';
    };

    const setHotelDestinationMeta = (item = {}) => {
        if (hotelDestinationMirror) {
            hotelDestinationMirror.value = item.destination || item.city || item.country || hotelDestinationInput?.value || '';
        }
        if (hotelCountryInput) hotelCountryInput.value = item.country || '';
        if (hotelCountryCodeInput) hotelCountryCodeInput.value = item.country_code || '';
        if (hotelLocationInput) hotelLocationInput.value = item.location || '';
        if (hotelRegionInput) hotelRegionInput.value = item.region || '';
    };

    const clearActivityDestinationMeta = () => {
        if (activityCountryInput) activityCountryInput.value = '';
        if (activityCountryCodeInput) activityCountryCodeInput.value = '';
        if (activityLocationInput) activityLocationInput.value = '';
    };

    const setActivityDestinationMeta = (item = {}) => {
        if (activityCountryInput) activityCountryInput.value = item.country || '';
        if (activityCountryCodeInput) activityCountryCodeInput.value = item.country_code || '';
        if (activityLocationInput) activityLocationInput.value = item.location || '';
    };

    const renderAirportList = (list, items, type) => {
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
                const item = {
                    code: btn.dataset.code || '',
                    city: btn.dataset.city || '',
                    country: btn.dataset.country || '',
                };
                syncAirportInputs(type, formatAirportSelection(item));
                syncAirportCodes(type, item.code || '');
                hideAllLists();
            });
        });
    };

    const fetchAirportSuggestions = async (input, list, type) => {
        if (!airportsUrl || !input || !list) return;

        const keyword = (input.value || '').trim();
        const previousState = airportRequestState.get(input);

        if (previousState?.controller) {
            previousState.controller.abort();
        }

        if (keyword.length < 2) {
            hideList(list);
            airportRequestState.set(input, {
                keyword,
                requestId: (previousState?.requestId || 0) + 1,
                controller: null,
            });
            return;
        }

        const requestId = (previousState?.requestId || 0) + 1;
        const controller = new AbortController();

        airportRequestState.set(input, { keyword, requestId, controller });

        list.innerHTML = '<div class="airport-suggest-loading">Searching airports...</div>';
        list.classList.remove('d-none');
        activeList = list;

        try {
            const url = new URL(airportsUrl, window.location.origin);
            url.searchParams.set('keyword', keyword);

            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                signal: controller.signal,
            });

            if (!response.ok) throw new Error('Request failed');

            const payload = await response.json();
            const items = Array.isArray(payload.data) ? payload.data : [];
            const currentState = airportRequestState.get(input);

            if (!currentState || currentState.requestId !== requestId || currentState.keyword !== keyword) {
                return;
            }

            renderAirportList(list, items, type);
        } catch (error) {
            if (error?.name === 'AbortError') {
                return;
            }

            list.innerHTML = '<div class="airport-suggest-empty">Unable to load airports</div>';
            list.classList.remove('d-none');
            activeList = list;
        }
    };

    const resolveAirportList = (input, type) => (
        input?.closest('.flight-airport-field')?.querySelector(`[data-airport-list="${type}"]`) || null
    );

    const bindAirportInputs = (inputs, type) => {
        inputs.forEach((input) => {
            const handler = debounce(() => {
                const list = resolveAirportList(input, type);
                fetchAirportSuggestions(input, list, type);
            });

            input.addEventListener('input', () => {
                syncAirportInputs(type, input.value);
                const raw = (input.value || '').trim();
                const match = raw.match(/\(([A-Za-z]{3})\)\s*$/);
                syncAirportCodes(type, match ? match[1].toUpperCase() : (/^[A-Za-z]{3}$/.test(raw) ? raw.toUpperCase() : ''));
                handler();
            });

            input.addEventListener('focus', () => {
                if ((input.value || '').trim().length >= 2) handler();
            });

            input.addEventListener('click', () => {
                if ((input.value || '').trim().length >= 2) handler();
            });

            input.addEventListener('keyup', () => {
                if ((input.value || '').trim().length >= 2) handler();
            });
        });
    };

    bindAirportInputs(originInputs, 'origin');
    bindAirportInputs(destinationInputs, 'destination');

    const renderDestinationAutocompleteList = (list, items, onSelect) => {
        if (!list) return;

        if (!items.length) {
            list.innerHTML = '<div class="airport-suggest-empty">No destination found</div>';
            list.classList.remove('d-none');
            return;
        }

        list.innerHTML = items.map((item) => {
            const typeLabel = item.type === 'country' ? 'Country' : 'City';
            const title = escapeHtml(item.display_name || item.destination || item.city || item.country || '');
            const subtitleParts = [];

            if (item.type === 'city' && item.country) subtitleParts.push(item.country);
            if (item.region) subtitleParts.push(item.region);

            const code = item.type === 'city'
                ? (item.location || item.country_code || '')
                : (item.country_code || '');

            return `
                <button type="button" class="airport-suggest-item hotel-destination-suggest-item"
                        data-type="${escapeHtml(item.type || '')}"
                        data-destination="${escapeHtml(item.destination || '')}"
                        data-city="${escapeHtml(item.city || '')}"
                        data-country="${escapeHtml(item.country || '')}"
                        data-country-code="${escapeHtml(item.country_code || '')}"
                        data-location="${escapeHtml(item.location || '')}"
                        data-region="${escapeHtml(item.region || '')}">
                    <span class="airport-suggest-icon"><i class="bi ${item.type === 'country' ? 'bi-globe2' : 'bi-building'}"></i></span>
                    <span class="airport-suggest-meta">
                        <span class="airport-suggest-name">${title}</span>
                        <span class="airport-suggest-sub">${escapeHtml(subtitleParts.join(' · '))}</span>
                    </span>
                    <span class="hotel-destination-badges">
                        <span class="hotel-destination-type">${escapeHtml(typeLabel)}</span>
                        ${code ? `<span class="airport-suggest-code">${escapeHtml(code)}</span>` : ''}
                    </span>
                </button>
            `;
        }).join('');

        list.classList.remove('d-none');

        list.querySelectorAll('.hotel-destination-suggest-item').forEach((btn) => {
            btn.addEventListener('click', () => {
                const item = {
                    type: btn.dataset.type || 'city',
                    destination: btn.dataset.destination || '',
                    city: btn.dataset.city || '',
                    country: btn.dataset.country || '',
                    country_code: btn.dataset.countryCode || '',
                    location: btn.dataset.location || '',
                    region: btn.dataset.region || '',
                };
                onSelect(item);
            });
        });
    };

    const fetchDestinationSuggestions = async (input, list) => {
        if (!hotelDestinationsUrl || !input || !list) return;

        const keyword = (input.value || '').trim();
        if (keyword.length < 2) {
            hideDestinationList(list);
            return;
        }

        list.innerHTML = '<div class="airport-suggest-loading">Searching destinations...</div>';
        list.classList.remove('d-none');

        try {
            const url = new URL(hotelDestinationsUrl, window.location.origin);
            url.searchParams.set('keyword', keyword);

            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            });

            if (!response.ok) throw new Error('Request failed');

            const payload = await response.json();
            return Array.isArray(payload.data) ? payload.data : [];
        } catch {
            list.innerHTML = '<div class="airport-suggest-empty">Unable to load destinations</div>';
            list.classList.remove('d-none');
            return null;
        }
    };

    if (hotelDestinationInput) {
        const debouncedHotelDestinationSearch = debounce(async () => {
            const items = await fetchDestinationSuggestions(hotelDestinationInput, hotelDestinationList);
            if (!Array.isArray(items)) return;

            renderDestinationAutocompleteList(hotelDestinationList, items, (item) => {
                const nextValue = item.type === 'country'
                    ? (item.destination || item.country || '')
                    : (item.city || item.destination || '');

                if (hotelDestinationInput) {
                    hotelDestinationInput.value = nextValue;
                }

                setHotelDestinationMeta(item);
                hideHotelDestinationList();
            });
        });

        hotelDestinationInput.addEventListener('input', () => {
            clearHotelDestinationMeta();
            debouncedHotelDestinationSearch();
        });

        hotelDestinationInput.addEventListener('focus', () => {
            if ((hotelDestinationInput.value || '').trim().length >= 2) {
                debouncedHotelDestinationSearch();
            }
        });
    }

    if (flightHotelDestinationInput) {
        const debouncedFlightHotelDestinationSearch = debounce(async () => {
            const items = await fetchDestinationSuggestions(flightHotelDestinationInput, flightHotelDestinationList);
            if (!Array.isArray(items)) return;

            renderDestinationAutocompleteList(flightHotelDestinationList, items, (item) => {
                flightHotelDestinationInput.value = item.type === 'country'
                    ? (item.destination || item.country || '')
                    : (item.city || item.destination || '');

                hideFlightHotelDestinationList();
            });
        });

        flightHotelDestinationInput.addEventListener('input', () => {
            debouncedFlightHotelDestinationSearch();
        });

        flightHotelDestinationInput.addEventListener('focus', () => {
            if ((flightHotelDestinationInput.value || '').trim().length >= 2) {
                debouncedFlightHotelDestinationSearch();
            }
        });
    }

    if (activityDestinationInput) {
        const debouncedActivityDestinationSearch = debounce(async () => {
            const items = await fetchDestinationSuggestions(activityDestinationInput, activityDestinationList);
            if (!Array.isArray(items)) return;

            renderDestinationAutocompleteList(activityDestinationList, items, (item) => {
                const nextValue = item.type === 'country'
                    ? (item.destination || item.country || '')
                    : (item.city || item.destination || '');

                activityDestinationInput.value = nextValue;
                setActivityDestinationMeta(item);
                hideActivityDestinationList();
            });
        });

        activityDestinationInput.addEventListener('input', () => {
            clearActivityDestinationMeta();
            debouncedActivityDestinationSearch();
        });

        activityDestinationInput.addEventListener('focus', () => {
            if ((activityDestinationInput.value || '').trim().length >= 2) {
                debouncedActivityDestinationSearch();
            }
        });
    }

    document.addEventListener('click', (e) => {
        if (e.target.closest('.flight-airport-field, .hotel-destination-field, .activity-destination-field')) return;
        hideAllLists();
        hideHotelDestinationList();
        hideFlightHotelDestinationList();
        hideActivityDestinationList();
    });

    if (flightForm) {
        flightForm.addEventListener('submit', () => {
            hideFlightHotelDestinationList();
        });
    }

    const resetSubmitState = () => {
        if (!submitBtn || !submitText) return;
        submitBtn.disabled = false;
        submitBtn.classList.remove('is-loading');
        submitText.textContent = tabTextMap[activeTopTab] || 'Search';
    };

    resetSubmitState();
    window.addEventListener('pageshow', resetSubmitState);

    if (flightForm && submitBtn && submitText) {
        flightForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            submitBtn.disabled = true;
            submitText.textContent = 'Searching...';
            submitBtn.classList.add('is-loading');

            const fd = new FormData(flightForm);

            if (activeTopTab === 'activities') {
                try {
                    const qs = new URLSearchParams();
                    const country = String(fd.get('country') || '').trim();
                    const countryCode = String(fd.get('country_code') || '').trim();
                    const city = String(fd.get('city') || '').trim();
                    const location = String(fd.get('location') || '').trim();
                    const activityDate = String(fd.get('activity_date') || '').trim();
                    const participants = Math.max(1, parseInt(fd.get('participants') || '1', 10));

                    if (country) qs.set('country', country);
                    if (countryCode) qs.set('country_code', countryCode);
                    if (city) qs.set('city', city);
                    if (location) qs.set('location', location);
                    if (activityDate) qs.set('activity_date', activityDate);
                    qs.set('participants', String(participants));

                    const targetUrl = "{{ Route::has('activities.index') ? route('activities.index') : '#' }}";
                    window.location.href = qs.toString() ? `${targetUrl}?${qs.toString()}` : targetUrl;
                    return;
                } finally {
                    resetSubmitState();
                }
            }

            const payload = {
                trip_type: fd.get('trip_type'),
                origin: fd.get('origin'),
                destination: fd.get('destination'),
                departure_date: fd.get('departure_date'),
                return_date: fd.get('return_date') || null,
                adults: parseInt(fd.get('adults')) || 1,
                children: parseInt(fd.get('children')) || 0,
                infants: parseInt(fd.get('infants')) || 0,
                cabin_class: fd.get('cabin_class'),
                provider: 'duffel',
            };

            try {
                await fetch('{{ route('api.flights.search') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    },
                    body: JSON.stringify(payload),
                });

                const qs = new URLSearchParams({
                    origin: payload.origin ?? '',
                    origin_display: fd.get('origin_display') ?? '',
                    destination: payload.destination ?? '',
                    destination_display: fd.get('destination_display') ?? '',
                    departure_date: payload.departure_date ?? '',
                    trip_type: payload.trip_type ?? 'one_way',
                    adults: payload.adults ?? 1,
                    children: payload.children ?? 0,
                    infants: payload.infants ?? 0,
                    cabin_class: payload.cabin_class ?? 'ECONOMY',
                    provider: payload.provider ?? 'duffel',
                });

                if (payload.return_date) qs.set('return_date', payload.return_date);
                window.location.href = '{{ route('flights.index') }}?' + qs.toString();
            } catch (err) {
                console.error('Flight search error:', err);
            } finally {
                resetSubmitState();
            }
        });
    }
})();
</script>

<script>
(function () {
    const nearbyUrl = '{{ route('api.locations.airports.nearby') }}';
    const STORAGE_KEY = 'trav_ip_origin_airport';
    const STORAGE_TTL = 6 * 60 * 60 * 1000; // 6 hours (matches server cache)

    const originInputs = [...document.querySelectorAll('[data-airport-input="origin"]')];
    if (!originInputs.length) return;

    const applyAirport = (code) => {
        // Re-check emptiness at apply time so we never overwrite user input
        originInputs.forEach(el => {
            if (!(el.value || '').trim()) el.value = code;
        });
    };

    const run = async () => {
        // Check localStorage cache first
        try {
            const cached = JSON.parse(localStorage.getItem(STORAGE_KEY) || 'null');
            if (cached && cached.code && (Date.now() - cached.ts) < STORAGE_TTL) {
                applyAirport(cached.code);
                return;
            }
            // Remove stale/corrupt cache entries
            localStorage.removeItem(STORAGE_KEY);
        } catch {
            localStorage.removeItem(STORAGE_KEY);
        }

        try {
            const controller = new AbortController();
            const timer = setTimeout(() => controller.abort(), 8000);

            const res = await fetch(nearbyUrl, {
                signal: controller.signal,
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
            });
            clearTimeout(timer);

            if (!res.ok) return;
            const payload = await res.json();
            if (!payload.success || !payload.data?.code) return;

            const code = payload.data.code;
            applyAirport(code);

            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify({ code, ts: Date.now() }));
            } catch {}
        } catch {}
    };

    // Run after DOM is fully ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
})();
</script>
@endpush
