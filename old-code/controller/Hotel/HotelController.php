<?php

namespace App\Http\Controllers\Frontend\Hotel;

use App\Http\Controllers\Controller;
use App\Http\Dtos\Frontend\Hotel\RoomAvailabilityRequestDto;
use App\Http\Models\Hotel;
use App\Http\Services\Frontend\Hotel\HotelAvailabilityService;
use App\Http\Services\Frontend\Hotel\HotelSearchService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Modules\Activity\Models\Activity;
use Modules\Booking\Models\Booking;
use Modules\Hotel\Models\HotelRoom as ModuleHotelRoom;
use Modules\Location\Models\Location;

class HotelController extends Controller
{
    public function __construct(
        private HotelSearchService      $searchService,
        private HotelAvailabilityService $availabilityService,
    ) {}

    /**
     * Hotel search & listing page.
     * GET /hotels
     */
    public function index(Request $request)
    {
        if ($request->hasAny([
            'check_in', 'check_out', 'checkin', 'checkout',
            'adults', 'children', 'rooms', 'unit', 'guests',
            'location', 'country_code',
        ])) {
            $request->session()->put('hotel_search_context', [
                'checkin' => (string) $request->query('checkin', $request->query('check_in', '')),
                'checkout' => (string) $request->query('checkout', $request->query('check_out', '')),
                'adults' => max(1, (int) $request->query('adults', 1)),
                'children' => max(0, (int) $request->query('children', 0)),
                'rooms' => max(1, (int) $request->query('rooms', $request->query('unit', 1))),
                'unit' => max(1, (int) $request->query('unit', $request->query('rooms', 1))),
                'guests' => max(1, (int) $request->query('guests', ((int) $request->query('adults', 1) + (int) $request->query('children', 0)))),
                'location' => (string) $request->query('location', ''),
                'country_code' => (string) $request->query('country_code', ''),
            ]);
        }

        $hotels = $this->searchService->getListings($request);

        return view('frontend.hotels.hotel-search', compact('hotels'));
    }

    /**
     * Countries AJAX endpoint.
     * GET /hotels/countries
     */
    public function getCountries(): JsonResponse
    {
        $countries = $this->searchService->getCountryList();
        return response()->json(['countries' => $countries]);
    }

    /**
     * Cities AJAX endpoint.
     * GET /hotels/cities?country_code=XX
     */
    public function getCities(Request $request): JsonResponse
    {
        $countryCode = $request->input('country_code', '');
        $cities = $this->searchService->getCityList($countryCode ?: null);
        return response()->json(['cities' => $cities]);
    }

    /**
     * Hotel detail page.
     * GET /hotels/{slug}
     */
    public function detail(Request $request, string $slug)
    {
        $isB2BDeal = false;
        $hotel = Hotel::published()
            ->where('slug', $slug)
            ->with(['rooms.facilities', 'rooms.services', 'propertyType', 'facilities', 'services'])
            ->firstOrFail();

        $searchContext = (array) $request->session()->get('hotel_search_context', []);

        return view('frontend.hotels.hotel-detail', compact('hotel', 'isB2BDeal', 'searchContext'));
    }

    /**
     * B2B hotel deal page.
     * GET /hotels/b2b/deal/{code}
     */
    public function b2bDeal(Request $request, string $code): View
    {
        $isB2BDeal = true;
        $hotelName = (string) $request->query('name', 'Hotel');
        $checkIn = (string) $request->query('check_in', '');
        $checkOut = (string) $request->query('check_out', '');
        $adults = max(1, (int) $request->query('adults', 1));
        $children = max(0, (int) $request->query('children', 0));
        $rooms = max(1, (int) $request->query('unit', 1));
        $countryCode = (string) $request->query('country_code', 'AE');
        $location = (string) $request->query('location', '');

        $hotel = (object) [
            'code' => $code,
            'slug' => '',
            'title' => $hotelName,
            'name' => $hotelName,
            'address' => (string) $request->query('address', ''),
            'image_url' => (string) $request->query('image', asset('images/placeholder-hotel.jpg')),
            'gallery_urls' => [],
            'star_rate' => max(0, min(5, (int) $request->query('star_rate', 0))),
            'review_score' => 0,
            'is_featured' => false,
            'content' => (string) $request->query('description', ''),
            'propertyType' => collect(),
            'facilities' => collect(),
            'services' => collect(),
            'rooms' => collect(),
            'check_in_time' => null,
            'check_out_time' => null,
        ];

        $search = [
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'adults' => $adults,
            'children' => $children,
            'rooms' => $rooms,
            'country_code' => $countryCode,
            'location' => $location,
            'price' => (float) $request->query('price', 0),
        ];

        $b2bMeta = [
            'hotel_code' => $code,
            'hotel_name' => $hotelName,
            'city_code' => $location,
            'star_rate' => max(0, min(5, (int) $request->query('star_rate', 0))),
            'address' => (string) $request->query('address', ''),
            'hotel_image_url' => (string) $request->query('image', ''),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'adults' => $adults,
            'children' => $children,
            'rooms' => $rooms,
            'nationality' => $countryCode,
            'rooms_url' => route('hotel.rooms'),
            'reserve_url' => route('hotel.b2b.add_to_cart'),
            'search_url' => route('hotel.search'),
            'checkout_form_url' => route('frontend.hotels.b2b.checkout'),
        ];

        return view('frontend.hotels.hotel-detail', compact('hotel', 'search', 'b2bMeta', 'isB2BDeal'));
    }

    /**
     * B2B checkout page rendered with the same frontend checkout blade as local flow.
     * GET /hotels/b2b/checkout
     */
    public function b2bCheckout(Request $request): View|RedirectResponse
    {
        $request->validate([
            'hotel_code' => 'required|string',
            'hotel_name' => 'required|string',
            'checkin' => 'required|date',
            'checkout' => 'required|date|after:checkin',
            'guests' => 'nullable|integer|min:1|max:20',
            'adults' => 'nullable|integer|min:1|max:20',
            'children' => 'nullable|integer|min:0|max:20',
            'rooms' => 'nullable|integer|min:1|max:20',
            'occupancy' => 'nullable|integer|min:1|max:20',
            'price_per_night' => 'nullable|numeric|min:0',
            'total_price' => 'nullable|numeric|min:0',
        ]);

        $checkIn = Carbon::parse((string) $request->query('checkin'))->startOfDay();
        $checkOut = Carbon::parse((string) $request->query('checkout'))->startOfDay();
        $nights = max(1, $checkIn->diffInDays($checkOut));

        $pricePerNight = (float) $request->query('price_per_night', 0);
        $fallbackTotal = $pricePerNight * $nights;
        $totalPrice = (float) $request->query('total_price', $fallbackTotal);

        $hotelCode = (string) $request->query('hotel_code');
        $checkinDate = $checkIn->format('Y-m-d');
        $checkoutDate = $checkOut->format('Y-m-d');
        $adults = max(1, (int) $request->query('adults', $request->query('guests', 1)));
        $children = max(0, (int) $request->query('children', 0));
        $rooms = max(1, (int) $request->query('rooms', 1));
        $occupancy = max(1, (int) $request->query('occupancy', (int) ceil(($adults + $children) / $rooms)));

        $hotel = (object) [
            'code' => $hotelCode,
            'slug' => null,
            'title' => (string) $request->query('hotel_name'),
            'address' => (string) $request->query('address', ''),
            'star_rate' => (float) $request->query('star_rate', 0),
            'is_featured' => false,
        ];

        $room = (object) [
            'id' => (string) $request->query('room_id', 'b2b-room'),
            'title' => (string) $request->query('room_type_name', 'Room'),
        ];

        $dto = (object) [
            'check_in' => $checkinDate,
            'check_out' => $checkoutDate,
            'adults' => $adults,
            'children' => $children,
        ];

        $availabilityResult = (object) [
            'nights' => $nights,
            'price_per_night' => $pricePerNight,
            'total_price' => $totalPrice,
        ];

        $backUrl = route('frontend.hotels.b2b.deal', $hotelCode) . '?' . http_build_query(array_filter([
            'location' => (string) $request->query('city_code', ''),
            'check_in' => $checkinDate,
            'check_out' => $checkoutDate,
            'adults' => $adults,
            'children' => $children,
            'unit' => $rooms,
            'country_code' => (string) $request->query('nationality', 'AE'),
            'name' => (string) $request->query('hotel_name', ''),
            'address' => (string) $request->query('address', ''),
            'image' => (string) $request->query('hotel_image_url', ''),
            'star_rate' => (string) $request->query('star_rate', '0'),
            'price' => (string) $pricePerNight,
        ], static fn($v) => $v !== ''));

        $isB2BCheckout = true;
        $b2bCheckoutMeta = [
            'reserve_url' => route('hotel.b2b.add_to_cart'),
            'hotel_code' => $hotelCode,
            'hotel_name' => (string) $request->query('hotel_name', ''),
            'address' => (string) $request->query('address', ''),
            'city_code' => (string) $request->query('city_code', ''),
            'hotel_image_url' => (string) $request->query('hotel_image_url', ''),
            'check_in' => $checkinDate,
            'check_out' => $checkoutDate,
            'adults' => $adults,
            'children' => $children,
            'rooms' => $rooms,
            'occupancy' => $occupancy,
            'nationality' => (string) $request->query('nationality', 'AE'),
            'agreement_code' => (string) $request->query('agreement_code', ''),
            'room_type_code' => (string) $request->query('room_type_code', ''),
            'room_type_name' => (string) $request->query('room_type_name', ''),
            'meal_basis_code' => (string) $request->query('meal_basis_code', ''),
            'meal_basis_name' => (string) $request->query('meal_basis_name', ''),
            'search_number' => (string) $request->query('search_number', ''),
            'token_id' => (string) $request->query('token_id', ''),
            'rate_key' => (string) $request->query('rate_key', ''),
            'agreement_price' => (string) $request->query('agreement_price', ''),
            'currency' => (string) $request->query('currency', 'USD'),
            'total_price' => $totalPrice,
        ];

        return view('frontend.hotels.hotel-checkout', compact(
            'hotel',
            'room',
            'dto',
            'availabilityResult',
            'isB2BCheckout',
            'b2bCheckoutMeta',
            'backUrl'
        ));
    }

    /**
     * Room availability check.
     * POST /hotels/{slug}/availability
     *
     * Returns JSON: { success: true, data: [ RoomAvailabilityDto[] ] }
     */
    public function checkAvailability(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'checkin'  => 'required|date|after_or_equal:today',
            'checkout' => 'required|date|after:checkin',
            'guests'   => 'sometimes|integer|min:1|max:20',
            'children' => 'sometimes|integer|min:0|max:10',
        ]);

        $hotel = Hotel::published()->where('slug', $slug)->firstOrFail();

        $dto     = RoomAvailabilityRequestDto::fromRequest($request);
        $results = $this->availabilityService->checkHotelRooms($hotel->id, $dto);

        return response()->json([
            'success' => true,
            'data'    => array_map(fn($r) => $r->toArray(), $results),
        ]);
    }

    /**
     * Hotel checkout page.
     * GET /hotels/{slug}/checkout?room=&checkin=&checkout=&guests=
     *
     * Re-validates availability server-side before showing the form.
     */
    public function checkout(Request $request, string $slug): View|RedirectResponse
    {
        $request->validate([
            'room'    => 'required|integer|min:1',
            'checkin' => 'required|date|after_or_equal:today',
            'checkout'=> 'required|date|after:checkin',
            'guests'  => 'sometimes|integer|min:1|max:20',
            'adults'  => 'sometimes|integer|min:1|max:20',
            'children'=> 'sometimes|integer|min:0|max:20',
            'rooms'   => 'sometimes|integer|min:1|max:20',
        ]);

        $hotel = Hotel::published()->where('slug', $slug)->firstOrFail();

        // Load room via module model (has isAvailableAt logic)
        $room = ModuleHotelRoom::where('id', $request->input('room'))
            ->where('parent_id', $hotel->id)
            ->where('status', 'publish')
            ->firstOrFail();

        // Server-side availability re-check
        $dto              = RoomAvailabilityRequestDto::fromRequest($request);
        $availabilityResult = $this->availabilityService->checkRoom($room, $dto);

        if (! $availabilityResult->available) {
            return redirect()
                ->route('frontend.hotels.detail', $slug)
                ->with('error', 'Sorry, that room is no longer available for the selected dates. Please choose another.');
        }

        return view('frontend.hotels.hotel-checkout', compact('hotel', 'room', 'availabilityResult', 'dto'));
    }

    /**
     * Process guest details and store booking in session.
     * POST /hotels/{slug}/checkout
     */
    public function processCheckout(Request $request, string $slug): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|max:255',
            'phone'      => 'required|string|max:30',
            'room_id'    => 'required|integer',
            'checkin'    => 'required|date',
            'checkout'   => 'required|date|after:checkin',
            'guests'     => 'required|integer|min:1',
            'nights'     => 'required|integer|min:1',
            'total_price'=> 'required|numeric|min:0',
        ]);

        session(['hotel_booking' => array_merge($validated, ['hotel_slug' => $slug])]);

        // TODO: redirect to payment gateway once payment is wired
        return redirect()
            ->route('frontend.hotels.detail', $slug)
            ->with('success', 'Booking details saved. Payment integration coming soon.');
    }

    /**
     * Process selected payment gateway for hotel booking draft.
     * POST /hotels/checkout/pay
     */
    public function pay(Request $request)
    {
        $validated = $request->validate([
            'booking_code' => 'required|string',
            'payment_gateway' => 'required|string',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
        ]);

        $booking = Booking::query()->where('code', $validated['booking_code'])->first();
        if (!$booking) {
            return response()->json(['message' => 'Booking not found. Please try again.'], 422);
        }

        if (!in_array((string) $booking->object_model, ['hotel', 'hotel_b2b'], true)) {
            return response()->json(['message' => 'Invalid booking type for hotel payment.'], 422);
        }

        if (auth()->check() && $booking->customer_id && (int) $booking->customer_id !== (int) auth()->id()) {
            return response()->json(['message' => 'Unauthorized booking access.'], 403);
        }

        $booking->first_name = $validated['first_name'];
        $booking->last_name = $validated['last_name'];
        $booking->email = $validated['email'];
        $booking->phone = $validated['phone'];
        $booking->gateway = $validated['payment_gateway'];
        // Hotel drafts created from custom UI can miss pay_now/deposit values.
        if (empty($booking->pay_now) || (float) $booking->pay_now <= 0) {
            $fallbackTotal = (float) ($booking->total_before_fees ?: $booking->total);
            $booking->pay_now = $fallbackTotal;
        }
        if (empty($booking->total) || (float) $booking->total <= 0) {
            $booking->total = (float) $booking->pay_now;
        }
        if (empty($booking->currency)) {
            $booking->currency = (string) ($booking->getMeta('currency', setting_item('currency_main', 'USD')));
        }
        $booking->save();

        if (empty($booking->pay_now) || (float) $booking->pay_now <= 0) {
            return response()->json(['message' => 'Booking amount is invalid for payment.'], 422);
        }

        $gateways = get_payment_gateways();
        $gatewayKey = $validated['payment_gateway'];
        if (empty($gateways[$gatewayKey]) || !class_exists($gateways[$gatewayKey])) {
            return response()->json(['message' => 'Payment gateway not found.'], 422);
        }

        /** @var \Modules\Booking\Gateways\BaseGateway $gatewayObj */
        $gatewayObj = new $gateways[$gatewayKey]($gatewayKey);
        if (!$gatewayObj->isAvailable()) {
            return response()->json(['message' => 'Payment gateway is not available.'], 422);
        }

        try {
            $service = $booking->service;
            return $gatewayObj->process($request, $booking, $service);
        } catch (\Throwable $e) {
            Log::error('Hotel checkout gateway error', [
                'booking_id' => $booking->id,
                'gateway' => $gatewayKey,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => $e->getMessage() ?: 'Unable to initiate payment. Please try again.'], 422);
        }
    }

    /**
     * Frontend redesigned booking detail page (local + B2B).
     * GET /hotels/booking/{code}
     */
    public function bookingDetail(string $code): View|RedirectResponse
    {
        $booking = Booking::query()
            ->where('code', $code)
            ->whereIn('object_model', ['hotel', 'hotel_b2b'])
            ->firstOrFail();

        if ($booking->status === Booking::DRAFT || $booking->status === Booking::UNPAID) {
            return redirect($booking->getCheckoutUrl(false));
        }

        if (auth()->check() && $booking->customer_id && (int) $booking->customer_id !== (int) auth()->id()) {
            abort(403);
        }

        $service = $booking->service;
        $checkIn = $booking->start_date ? Carbon::parse($booking->start_date) : null;
        $checkOut = $booking->end_date ? Carbon::parse($booking->end_date) : null;

        $adults = (int) $booking->getMeta('adults', (int) $booking->total_guests ?: 1);
        $children = (int) $booking->getMeta('children', 0);
        $rooms = (int) $booking->getMeta('rooms', 1);
        $nights = (int) $booking->getMeta('nights', ($checkIn && $checkOut ? max(1, $checkIn->diffInDays($checkOut)) : 1));

        $extraPriceTotal = (float) $booking->getMeta('extra_price_total', 0);
        $extraPriceItems = (array) ($booking->getMeta('extra_price_items', []) ?: []);

        $subtotal = (float) ($booking->total_before_fees ?: $booking->total ?: 0);
        $total = (float) ($booking->total ?: $subtotal);
        $roomSubtotal = max(0, $subtotal - $extraPriceTotal);
        $taxes = max(0, $total - $subtotal);
        $pricePerNight = $nights > 0 && $roomSubtotal > 0 ? ($roomSubtotal / $nights) : ($nights > 0 ? ($subtotal / $nights) : $subtotal);

        $bookingData = [
            'code' => (string) $booking->code,
            'status' => (string) $booking->status,
            'payment_status' => (string) optional($booking->payment)->status,
            'gateway' => (string) ($booking->gateway ?: '-'),
            'is_b2b' => $booking->object_model === 'hotel_b2b',
            'hotel_name' => (string) $booking->getMeta('hotel_name', $service->title ?? 'Hotel'),
            'hotel_address' => (string) $booking->getMeta('hotel_address', $service->address ?? ''),
            'hotel_city' => (string) $booking->getMeta('hotel_city_code', ''),
            'room_type' => (string) $booking->getMeta('room_type_name', __('Room')),
            'meal_basis' => (string) $booking->getMeta('meal_basis_name', 'Room Only'),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'adults' => $adults,
            'children' => $children,
            'rooms' => $rooms,
            'nights' => $nights,
            'subtotal' => $subtotal,
            'taxes' => $taxes,
            'total' => $total,
            'price_per_night' => $pricePerNight,
            'supplier_reference' => (string) $booking->getMeta('hotel_b2b_booking_reference', ''),
            'supplier_booking_code' => (string) $booking->getMeta('hotel_b2b_booking_code', ''),
            'supplier_status' => (string) $booking->getMeta('hotel_b2b_booking_status', ''),
            'extra_price_total' => $extraPriceTotal,
            'extra_price_items' => $extraPriceItems,
        ];

        $eligibleStatuses = ['completed', 'paid', 'confirmed'];
        $canShowActivities = in_array(strtolower((string) $booking->status), $eligibleStatuses, true);
        $activityDateForSearch = $booking->created_at
            ? Carbon::parse($booking->created_at)->format('Y-m-d')
            : now()->format('Y-m-d');

        $activityCity = $this->resolveActivityCityFromBooking($booking, $bookingData, $service);
        $nearbyActivities = collect();
        $viewMoreActivitiesUrl = route('frontend.activities.index');
        if ($canShowActivities && $activityCity !== '') {
            $nearbyActivities = Activity::query()
                ->where('status', 'publish')
                ->where('is_active', 1)
                ->where(function ($q) use ($activityCity) {
                    $q->where('city', 'like', '%' . $activityCity . '%')
                        ->orWhere('address', 'like', '%' . $activityCity . '%')
                        ->orWhere('title', 'like', '%' . $activityCity . '%');
                })
                ->orderByDesc('id')
                ->take(3)
                ->get();

            $viewMoreActivitiesUrl = route('frontend.activities.index', [
                'city' => $activityCity,
                'activity_date' => $activityDateForSearch,
            ]);
        }

        return view('frontend.hotels.booking-detail', compact(
            'booking',
            'bookingData',
            'service',
            'nearbyActivities',
            'activityCity',
            'viewMoreActivitiesUrl',
            'canShowActivities'
        ));
    }

    private function resolveActivityCityFromBooking(Booking $booking, array $bookingData, mixed $service): string
    {
        $candidates = [];

        $hotelCityName = trim((string) $booking->getMeta('hotel_city_name', ''));
        if ($hotelCityName !== '') {
            $candidates[] = $hotelCityName;
        }

        $hotelCityCode = trim((string) $booking->getMeta('hotel_city_code', ''));
        if ($hotelCityCode !== '' && !preg_match('/^[A-Z]{2,6}$/', $hotelCityCode)) {
            $candidates[] = $hotelCityCode;
        }

        if ((string) $booking->object_model === 'hotel' && (int) $booking->object_id > 0) {
            $hotel = Hotel::query()->find((int) $booking->object_id);
            if (!empty($hotel) && !empty($hotel->location_id)) {
                $location = Location::query()->find((int) $hotel->location_id);
                if (!empty($location)) {
                    $locationName = trim((string) ($location->translate()->name ?? $location->name ?? ''));
                    if ($locationName !== '') {
                        $candidates[] = $locationName;
                    }
                }
            }
        }

        $addressCity = $this->extractCityFromAddress((string) ($bookingData['hotel_address'] ?? ''));
        if ($addressCity !== '') {
            $candidates[] = $addressCity;
        }

        $serviceAddressCity = $this->extractCityFromAddress((string) data_get($service, 'address', ''));
        if ($serviceAddressCity !== '') {
            $candidates[] = $serviceAddressCity;
        }

        return trim((string) collect($candidates)->filter()->first());
    }

    private function extractCityFromAddress(string $address): string
    {
        $address = trim($address);
        if ($address === '') {
            return '';
        }

        $parts = collect(explode(',', $address))
            ->map(fn ($part) => trim((string) $part))
            ->filter(fn ($part) => $part !== '')
            ->values();

        if ($parts->isEmpty()) {
            return '';
        }

        // Most addresses are "street, city, country": prefer second segment, else first.
        if ($parts->count() >= 2) {
            return (string) $parts->get(1);
        }

        return (string) $parts->first();
    }
}
