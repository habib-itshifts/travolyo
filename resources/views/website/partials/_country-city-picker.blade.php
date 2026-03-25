@php
    $prefix = $prefix ?? 'location';
    $countryColumnClass = $countryColumnClass ?? 'col-12 col-md-3';
    $cityColumnClass = $cityColumnClass ?? 'col-12 col-md-3';
    $countryLabel = $countryLabel ?? 'Country';
    $cityLabel = $cityLabel ?? 'City';
    $countryNameField = $countryNameField ?? 'country';
    $countryCodeField = $countryCodeField ?? 'country_code';
    $cityNameField = $cityNameField ?? 'city';
    $cityCodeField = $cityCodeField ?? 'location';
    $selectedCountryCode = $selectedCountryCode ?? '';
    $selectedCountryName = $selectedCountryName ?? '';
    $selectedCityName = $selectedCityName ?? '';
    $selectedCityCode = $selectedCityCode ?? '';
    $countriesUrl = $countriesUrl ?? route('api.locations.countries');
    $citiesUrl = $citiesUrl ?? url('api/locations/cities');
    $disabled = $disabled ?? false;
@endphp

<div class="{{ $countryColumnClass }}" data-location-group="{{ $prefix }}">
    <label class="form-label text-muted small mb-1">{{ $countryLabel }}</label>
    <div class="custom-picker"
         data-picker
         data-location-group="{{ $prefix }}"
         data-role="country-picker"
         data-countries-url="{{ $countriesUrl }}"
         data-selected-code="{{ $selectedCountryCode }}"
         data-selected-name="{{ $selectedCountryName }}">
        <div class="input-icon-wrap picker-trigger" role="button" tabindex="0" aria-expanded="false">
            <i class="bi bi-globe2 input-icon"></i>
            <input type="text"
                   name="{{ $countryNameField }}"
                   class="form-control search-input picker-input"
                   data-location-group="{{ $prefix }}"
                   data-role="country-input"
                   placeholder="Select country"
                   autocomplete="off"
                   value="{{ $selectedCountryName }}"
                   {{ $disabled ? 'disabled' : '' }} />
        </div>
        <div class="picker-menu" data-location-group="{{ $prefix }}" data-role="country-menu">
            <div class="p-2 text-muted small">Loading countries...</div>
        </div>
    </div>
    <input type="hidden"
           name="{{ $countryCodeField }}"
           data-location-group="{{ $prefix }}"
           data-role="country-code"
           value="{{ $selectedCountryCode }}"
           {{ $disabled ? 'disabled' : '' }} />
</div>

<div class="{{ $cityColumnClass }}" data-location-group="{{ $prefix }}">
    <label class="form-label text-muted small mb-1">{{ $cityLabel }}</label>
    <div class="custom-picker"
         data-picker
         data-location-group="{{ $prefix }}"
         data-role="city-picker"
         data-cities-url="{{ $citiesUrl }}"
         data-initial-country="{{ $selectedCountryCode }}">
        <div class="input-icon-wrap picker-trigger" role="button" tabindex="0" aria-expanded="false">
            <i class="bi bi-geo-alt input-icon"></i>
            <input type="text"
                   name="{{ $cityNameField }}"
                   class="form-control search-input picker-input"
                   data-location-group="{{ $prefix }}"
                   data-role="city-input"
                   placeholder="Select city"
                   autocomplete="off"
                   value="{{ $selectedCityName }}"
                   {{ $disabled ? 'disabled' : '' }} />
        </div>
        <div class="picker-menu" data-location-group="{{ $prefix }}" data-role="city-menu">
            @if ($selectedCountryCode)
                <div class="p-2 text-muted small">Loading cities...</div>
            @else
                <div class="p-2 text-muted small">Select a country first</div>
            @endif
        </div>
    </div>
    <input type="hidden"
           name="{{ $cityCodeField }}"
           data-location-group="{{ $prefix }}"
           data-role="city-code"
           value="{{ $selectedCityCode }}"
           {{ $disabled ? 'disabled' : '' }} />
</div>
