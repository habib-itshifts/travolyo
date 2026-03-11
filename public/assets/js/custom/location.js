/* ═══════════════════════════════════════════
   TRAVOLYO – location.js
   AJAX helpers for country / city / airport
   pickers in the search widget.
════════════════════════════════════════════ */

(function () {
  "use strict";

  /* ─── API base URL ───────────────────────
     Laravel registers api/* routes under /api
  ─────────────────────────────────────────── */
  var API = {
    countries : "/api/locations/countries",
    cities    : "/api/locations/cities",
    airports  : "/api/locations/airports",
    search    : "/api/locations/search",
  };

  /* ─── Tiny fetch helper ──────────────────
     Returns a Promise that resolves to the
     `data` array from the JSON response.
  ─────────────────────────────────────────── */
  function apiFetch(url, params) {
    var qs = params ? "?" + new URLSearchParams(params).toString() : "";
    return fetch(url + qs, {
      headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" },
    })
      .then(function (res) { return res.json(); })
      .then(function (json) { return json.data || []; })
      .catch(function (err) {
        console.warn("[LocationAjax] fetch error:", err);
        return [];
      });
  }

  /* ─── Country Picker ─────────────────────
     Populates .country-picker-menu elements
     (hotels form + any other form that has one)
  ─────────────────────────────────────────── */
  function loadCountries() {
    var menus = document.querySelectorAll("[data-location-role='country-menu']");
    if (!menus.length) return;

    apiFetch(API.countries).then(function (countries) {
      menus.forEach(function (menu) {
        menu.innerHTML = "";
        countries.forEach(function (country) {
          var btn = document.createElement("button");
          btn.type = "button";
          btn.className = "picker-option";
          btn.dataset.code = country.code;
          btn.dataset.name = country.name;
          btn.innerHTML =
            '<i class="bi bi-geo-alt"></i>' +
            '<span>' + country.flag + " " + country.name + "</span>" +
            '<span class="picker-code">' + country.code + "</span>";

          btn.addEventListener("click", function () {
            // Fill visible input
            var picker  = menu.closest(".custom-picker");
            var input   = picker && picker.querySelector(".picker-input");
            var hiddenCode = picker && picker.querySelector("[data-location-hidden='country_code']");
            var hiddenName = picker && picker.querySelector("[data-location-hidden='country_name']");

            if (input)      input.value      = country.flag + " " + country.name;
            if (hiddenCode) hiddenCode.value = country.code;
            if (hiddenName) hiddenName.value = country.name;

            picker && picker.classList.remove("is-open");

            // Cascade: load cities for selected country
            var form      = picker && picker.closest("form");
            var cityMenu  = form && form.querySelector("[data-location-role='city-menu']");
            if (cityMenu) loadCities(country.code, cityMenu);
          });

          menu.appendChild(btn);
        });
      });
    });
  }

  /* ─── City Picker ────────────────────────
     Called when a country is selected.
     Populates data-location-role="city-menu"
  ─────────────────────────────────────────── */
  function loadCities(countryCode, menuEl) {
    if (!menuEl) return;
    menuEl.innerHTML = '<div class="px-3 py-2 text-muted small">Loading…</div>';

    apiFetch(API.cities, { country_code: countryCode }).then(function (cities) {
      menuEl.innerHTML = "";
      if (!cities.length) {
        menuEl.innerHTML = '<div class="px-3 py-2 text-muted small">No cities found.</div>';
        return;
      }
      cities.forEach(function (city) {
        var btn = document.createElement("button");
        btn.type = "button";
        btn.className = "picker-option";
        btn.dataset.id   = city.id;
        btn.dataset.name = city.name;
        btn.innerHTML =
          '<i class="bi bi-building"></i>' +
          '<span>' + city.name + "</span>";

        btn.addEventListener("click", function () {
          var picker  = menuEl.closest(".custom-picker");
          var input   = picker && picker.querySelector(".picker-input");
          var hiddenId = picker && picker.querySelector("[data-location-hidden='city_id']");

          if (input)    input.value   = city.name;
          if (hiddenId) hiddenId.value = city.id;
          picker && picker.classList.remove("is-open");
        });

        menuEl.appendChild(btn);
      });
    });
  }

  /* ─── Airport Autocomplete ───────────────
     Listens on inputs with
     data-location-role="airport-search"
     and populates the sibling dropdown.
  ─────────────────────────────────────────── */
  function initAirportSearch() {
    var inputs = document.querySelectorAll("[data-location-role='airport-search']");

    inputs.forEach(function (input) {
      var debounceTimer = null;
      var dropdown = document.querySelector(
        "[data-location-role='airport-results'][data-for='" + input.id + "']"
      );
      if (!dropdown) return;

      input.addEventListener("input", function () {
        clearTimeout(debounceTimer);
        var q = input.value.trim();
        if (q.length < 2) { dropdown.innerHTML = ""; dropdown.style.display = "none"; return; }

        debounceTimer = setTimeout(function () {
          apiFetch(API.search, { q: q }).then(function (results) {
            dropdown.innerHTML = "";
            if (!results.length) { dropdown.style.display = "none"; return; }

            results.forEach(function (item) {
              var btn = document.createElement("button");
              btn.type = "button";
              btn.className = "picker-option";
              btn.innerHTML =
                '<i class="bi bi-airplane"></i>' +
                '<span>' + item.name + (item.iata ? " (" + item.iata + ")" : "") + "</span>" +
                (item.iata ? '<span class="picker-code">' + item.iata + "</span>" : "");

              btn.addEventListener("click", function () {
                input.value = item.name + (item.iata ? " (" + item.iata + ")" : "");
                var hiddenIata = document.querySelector("[data-location-hidden='iata'][data-for='" + input.id + "']");
                if (hiddenIata) hiddenIata.value = item.iata || "";
                dropdown.innerHTML = "";
                dropdown.style.display = "none";
              });

              dropdown.appendChild(btn);
            });
            dropdown.style.display = "block";
          });
        }, 300); // 300ms debounce
      });

      // Close on outside click
      document.addEventListener("click", function (e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
          dropdown.innerHTML = "";
          dropdown.style.display = "none";
        }
      });
    });
  }

  /* ─── Init ───────────────────────────────
     Run after DOM is ready
  ─────────────────────────────────────────── */
  document.addEventListener("DOMContentLoaded", function () {
    loadCountries();
    initAirportSearch();
  });

})();
