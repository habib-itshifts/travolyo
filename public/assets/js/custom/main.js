/* ═══════════════════════════════════════════
   TRAVOLYO – main.js
   Interactive behaviour for landing page
════════════════════════════════════════════ */

(function () {
  "use strict";

  /* ─── Search Category Tabs ──────────────── */
  const tabBtns = document.querySelectorAll(".search-tab-btn[data-tab]");
  const subTabs = document.getElementById("flight-subtabs");
  const sharedSearchBtn = document.getElementById("sharedSearchBtn");
  const sharedSearchBtnText = document.getElementById("sharedSearchBtnText");
  const hotelSearchForm = document.getElementById("hotelSearchForm");
  const flightWidgetSearchForm = document.getElementById("flightWidgetSearchForm");
  const flightWidgetTripType = document.getElementById("flightWidgetTripType");
  const forms = {
    flights: document.getElementById("flightsForm"),
    hotels: document.getElementById("hotelsForm"),
    home: document.getElementById("genericForm"),
    events: document.getElementById("genericForm"),
  };

  function activateTab(tab) {
    // Active state
    tabBtns.forEach((b) => b.classList.remove("active"));
    const activeBtn = document.querySelector(`.search-tab-btn[data-tab="${tab}"]`);
    if (activeBtn) activeBtn.classList.add("active");

    // Show/hide flight sub-tabs
    if (subTabs) {
      subTabs.style.display = tab === "flights" ? "block" : "none";
    }

    // Show correct form
    Object.values(forms).forEach((f) => f && f.classList.add("d-none"));
    const target =
      tab === "hotels"
        ? forms.hotels
        : tab === "flights"
          ? forms.flights
          : forms.home;

    if (target) target.classList.remove("d-none");

    // Update shared search button label
    if (sharedSearchBtnText) {
      const labels = {
        hotels: "Search Hotels",
        flights: "Search Flights",
        home: "Search Homes",
        events: "Search Events",
      };
      sharedSearchBtnText.textContent = labels[tab] || "Search";
    }
  }

  tabBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      activateTab(btn.dataset.tab);
    });
  });

  // Initialize UI from active tab in HTML (Hotels on index)
  const defaultTab =
    document.querySelector(".search-tab-btn.active")?.dataset.tab || "hotels";
  activateTab(defaultTab);

  if (sharedSearchBtn) {
    sharedSearchBtn.addEventListener("click", () => {
      const activeTab =
        document.querySelector(".search-tab-btn.active")?.dataset.tab || "hotels";

      if (activeTab === "hotels" && hotelSearchForm) {
        if (hotelSearchForm.requestSubmit) hotelSearchForm.requestSubmit();
        else hotelSearchForm.submit();
        return;
      }

      if (activeTab === "flights" && flightWidgetSearchForm) {
        if (flightWidgetTripType) {
          const isRoundTrip = document.getElementById("roundTripBtn")?.classList.contains("active");
          flightWidgetTripType.value = isRoundTrip ? "round_trip" : "one_way";
        }
        if (flightWidgetSearchForm.requestSubmit) flightWidgetSearchForm.requestSubmit();
        else flightWidgetSearchForm.submit();
      }
    });
  }

  /* ─── Trip Type Buttons (One-way / Round-Trip) ── */
  const tripBtns = document.querySelectorAll(".trip-type-btn");
  const oneWayBtn = document.getElementById("oneWayBtn");
  const roundTripBtn = document.getElementById("roundTripBtn");

  // These classes MUST exist on the right fields in your HTML:
  // - One-way-only fields:  .flight-one-way-only
  // - Round-trip-only fields: .flight-round-trip-only
  //
  // Your desired layout:
  // One-way:
  //   Row1: From, To, Departure, Passengers
  //   Row2: Cabin (col-md-3)
  // Round-trip:
  //   Row1: From, To, Departure, Return
  //   Row2: Passengers + Cabin (centered)
  const oneWayOnlyFields = document.querySelectorAll(".flight-one-way-only");
  const roundTripOnlyFields = document.querySelectorAll(".flight-round-trip-only");

  const setTripTypeLayout = (type) => {
    const isRoundTrip = type === "round_trip";
    if (flightWidgetTripType) {
      flightWidgetTripType.value = isRoundTrip ? "round_trip" : "one_way";
    }

    oneWayOnlyFields.forEach((field) => {
      field.classList.toggle("d-none", isRoundTrip);
      field.querySelectorAll("input, select, textarea").forEach((el) => {
        if (el.name) el.disabled = isRoundTrip;
      });
    });

    roundTripOnlyFields.forEach((field) => {
      field.classList.toggle("d-none", !isRoundTrip);
      field.querySelectorAll("input, select, textarea").forEach((el) => {
        if (el.name) el.disabled = !isRoundTrip;
      });
    });
  };

  // Bind click for trip type buttons
  tripBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      tripBtns.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      if (btn === roundTripBtn) {
        setTripTypeLayout("round_trip");
      } else {
        setTripTypeLayout("one_way");
      }
    });
  });

  // Initial state (on load)
    if (roundTripBtn?.classList.contains("active")) {
        setTripTypeLayout("round_trip");
    } else if (oneWayBtn) {
        setTripTypeLayout("one_way");
    }

    /* ─── Flight Passenger Picker (Adult/Child/Infant) ── */
    const flightPaxPickers = document.querySelectorAll("[data-flight-pax-picker]");
    const closeAllFlightPax = () => {
        flightPaxPickers.forEach((picker) => {
            picker.classList.remove("is-open");
            const trigger = picker.querySelector(".flight-passenger-trigger");
            if (trigger) trigger.setAttribute("aria-expanded", "false");
        });
    };

    const updateFlightPaxSummary = (picker) => {
        const summary = picker.querySelector(".flight-passenger-summary");
        if (!summary) return;

        const getCount = (type, fallback = 0) => {
            const hidden = picker.querySelector(`input[name="${type}"]`);
            if (hidden) return parseInt(hidden.value || fallback, 10) || fallback;
            const row = picker.querySelector(`.flight-passenger-row[data-type="${type}"] .flight-passenger-val`);
            return row ? parseInt(row.textContent || fallback, 10) || fallback : fallback;
        };

        const adults = getCount("adults", 1);
        const children = getCount("children", 0);
        const infants = getCount("infants", 0);
        summary.value = `${adults} Adult ${children} Child ${infants} Infant`;
    };

    const renderFlightChildAges = (picker, count) => {
        const container = picker.querySelector(".flight-child-ages-container");
        if (!container) return;

        const isHomesPicker = picker.id === 'homesGuestsPicker';
        if (isHomesPicker) {
            container.innerHTML = "";
            return;
        }

        const ageOptions = Array.from({ length: 18 }, (_, i) => i);
        const prevSelects = container.querySelectorAll("[data-flight-child-age]");
        const prevValues = [];
        prevSelects.forEach((sel) => prevValues.push(sel.value));

        if (count === 0) {
            container.innerHTML = "";
            return;
        }

        let html = '<div class="room-divider"></div>';
        for (let i = 0; i < count; i++) {
            const prevVal = i < prevValues.length ? prevValues[i] : "";
            html += `<div class="child-age-row">
                <div class="guest-label">Child's age</div>
                <select class="child-age-select" data-flight-child-age data-child-index="${i}">
                    <option value="" disabled ${prevVal === "" ? "selected" : ""}>Age</option>
                    ${ageOptions.map((a) => `<option value="${a}" ${String(a) === prevVal ? "selected" : ""}>${a === 0 ? "< 1" : a}</option>`).join("")}
                </select>
            </div>`;
        }
        container.innerHTML = html;
    };

    flightPaxPickers.forEach((picker) => {
        const trigger = picker.querySelector(".flight-passenger-trigger");
        const menu = picker.querySelector(".flight-passenger-menu");
        if (!trigger || !menu) return;

        picker.querySelectorAll(".flight-passenger-row").forEach((row) => {
            const type = row.dataset.type;
            const hidden = type ? picker.querySelector(`input[name="${type}"]`) : null;
            const valueNode = row.querySelector(".flight-passenger-val");
            const min = parseInt(row.dataset.min || "0", 10);
            const max = parseInt(row.dataset.max || "9", 10);

            if (valueNode && hidden) {
                valueNode.textContent = String(parseInt(hidden.value || String(min), 10) || min);
            }

            row.querySelectorAll(".flight-passenger-btn").forEach((btn) => {
                btn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    if (!valueNode) return;
                    const delta = parseInt(btn.dataset.delta || "0", 10);
                    const current = parseInt(valueNode.textContent || "0", 10) || 0;
                    const next = Math.max(min, Math.min(max, current + delta));
                    valueNode.textContent = String(next);
                    if (hidden) hidden.value = String(next);
                    if (type === "children") {
                        renderFlightChildAges(picker, next);
                    }
                    updateFlightPaxSummary(picker);
                });
            });
        });

        const initialChildren = parseInt(
            (picker.querySelector('input[name="children"]') || {}).value || "0",
            10
        );
        if (initialChildren > 0) renderFlightChildAges(picker, initialChildren);

        trigger.addEventListener("click", (e) => {
            e.stopPropagation();
            const willOpen = !picker.classList.contains("is-open");
            closeAllFlightPax();
            if (willOpen) {
                picker.classList.add("is-open");
                trigger.setAttribute("aria-expanded", "true");
            }
        });

        trigger.addEventListener("keydown", (e) => {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                trigger.click();
            }
        });

        menu.addEventListener("click", (e) => e.stopPropagation());
        updateFlightPaxSummary(picker);
    });

    document.addEventListener("click", closeAllFlightPax);

  /* ─── Navbar scroll shadow ──────────────── */
  const navbar = document.querySelector(".navbar");
  window.addEventListener(
    "scroll",
    () => {
      if (!navbar) return;
      if (window.scrollY > 20) {
        navbar.classList.add("shadow");
      } else {
        navbar.classList.remove("shadow");
      }
    },
    { passive: true },
  );

  /* ─── Smooth scroll for anchor links ───── */
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", (e) => {
      const href = anchor.getAttribute("href");
      if (!href) return;
      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    });
  });
})();

(function ($) {
  if (!$ || !window.bootstrap) return;

  // Bridge legacy jQuery modal calls used in public/js/home.js
  if (!$.fn.modal) {
    $.fn.modal = function (action) {
      return this.each(function () {
        var modal = bootstrap.Modal.getOrCreateInstance(this);
        if (action === "show") modal.show();
        else if (action === "hide") modal.hide();
        else modal.toggle();
      });
    };
  }

  // Prevent legacy script crashes when optional plugins are not loaded
  if (!$.fn.tooltip) {
    $.fn.tooltip = function () {
      return this;
    };
  }
  if (!$.fn.owlCarousel) {
    $.fn.owlCarousel = function () {
      return this;
    };
  }
  if (!$.fn.daterangepicker) {
    $.fn.daterangepicker = function () {
      return this;
    };
  }

  // Support legacy attributes from old popup partial
  $(document).on("click", '[data-toggle="modal"][data-target]', function (e) {
    e.preventDefault();
    var target = $(this).attr("data-target");
    if (!target) return;
    var node = document.querySelector(target);
    if (node) bootstrap.Modal.getOrCreateInstance(node).show();
  });

  $(document).on("click", '[data-dismiss="modal"]', function () {
    var modalEl = this.closest(".modal");
    if (!modalEl) return;
    bootstrap.Modal.getOrCreateInstance(modalEl).hide();
  });
})(window.jQuery);