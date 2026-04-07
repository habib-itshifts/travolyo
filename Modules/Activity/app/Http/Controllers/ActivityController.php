<?php

namespace Modules\Activity\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\Payment\PaymentService;
use App\Services\Payment\NGeniusGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Activity\Models\Activity;
use Modules\Activity\Models\ActivityBookingPassenger;
use Modules\Admin\Models\MediaFile;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $params = [
            'destination'          => trim((string) $request->query('city', $request->query('destination', ''))),
            'city'                 => trim((string) $request->query('city', '')),
            'country'              => trim((string) $request->query('country', '')),
            'country_code'         => trim((string) $request->query('country_code', '')),
            'category'             => trim((string) $request->query('category', '')),
            'activity_date'        => (string) $request->query('activity_date', now()->format('Y-m-d')),
            'participants'         => max(1, (int) $request->query('participants', 1)),
            'price_max'            => $request->filled('price_max') ? (float) $request->query('price_max') : null,
            'instant_confirmation' => $request->boolean('instant_confirmation'),
            'sort_by'              => (string) $request->query('sort', 'recommended'),
        ];

        // Lightweight queries for the filter sidebar only
        $baseQuery  = Activity::query()->published();
        $categories = (clone $baseQuery)
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
        $maxPrice = (int) ceil((float) ((clone $baseQuery)->max('price_per_person') ?: 1000));
        $maxPrice = max($maxPrice, 1000);

        return view('activity::index', compact('params', 'categories', 'maxPrice'));
    }

    public function show(Activity $activity, Request $request): View
    {
        abort_unless($activity->status === 'publish' && $activity->is_active, 404);

        $activity->load(['image', 'author']);

        $relatedActivities = Activity::query()
            ->with('image')
            ->published()
            ->whereKeyNot($activity->id)
            ->when($activity->category, fn ($query) => $query->where('category', $activity->category))
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        if ($relatedActivities->count() < 3 && $activity->city) {
            $existingIds = $relatedActivities->pluck('id')->push($activity->id)->all();
            $cityActivities = Activity::query()
                ->with('image')
                ->published()
                ->whereNotIn('id', $existingIds)
                ->where('city', $activity->city)
                ->orderByDesc('id')
                ->limit(3 - $relatedActivities->count())
                ->get();

            $relatedActivities = $relatedActivities->concat($cityActivities);
        }

        $selectedDate = (string) $request->query('activity_date', now()->format('Y-m-d'));
        $selectedParticipants = $this->limitParticipants($activity, (int) $request->query('participants', 1));

        return view('activity::show', compact('activity', 'relatedActivities', 'selectedDate', 'selectedParticipants'));
    }

    public function checkout(Request $request, Activity $activity): View|RedirectResponse
    {
        abort_unless($activity->status === 'publish' && $activity->is_active, 404);

        $date = (string) $request->query('date', $request->query('activity_date', now()->format('Y-m-d')));
        $participants = $this->limitParticipants($activity, (int) $request->query('participants', 1));
        $displayCurrency = strtoupper((string) session('currency', $request->query('currency', $activity->currency ?: 'AED')));
        $baseCurrency = strtoupper((string) ($activity->currency ?: 'AED'));
        $basePrice = (float) ($activity->price_per_person ?: 0);
        $displayPrice = $displayCurrency === $baseCurrency
            ? $basePrice
            : (float) currency($basePrice, $baseCurrency, $displayCurrency, false);

        $booking = $this->resolveDraftBooking($activity);
        if ($booking) {
            $this->syncPendingPaymentStatus($booking);
            if (in_array($booking->status, [Booking::COMPLETED, Booking::CONFIRMED], true)) {
                $this->rememberCheckoutBooking($booking, $activity);
                return redirect()->to($booking->getDetailUrl())->with('success', 'Payment confirmed! Your booking is complete.');
            }

            $this->refreshDraftBooking($booking, $activity, $date, $participants);
            $this->rememberCheckoutBooking($booking, $activity);
        } else {
            session()->forget('activity_checkout');
        }

        $passenger = $booking
            ? ActivityBookingPassenger::query()->where('booking_id', $booking->id)->first()
            : null;
        $passengersData = $this->buildPassengerForms($booking, $passenger, $participants);

        return view('activity::checkout', [
            'activity' => $activity,
            'booking' => $booking,
            'date' => $date,
            'city' => (string) $request->query('city', $activity->city),
            'participants' => $participants,
            'passenger' => $passenger,
            'passengersData' => $passengersData,
            'displayCurrency' => $displayCurrency,
            'displayBaseCurrency' => $baseCurrency,
            'displayBasePrice' => $basePrice,
            'displayConvertedPrice' => $displayPrice,
        ]);
    }

    public function pay(Request $request, Activity $activity): RedirectResponse
    {
        abort_unless($activity->status === 'publish' && $activity->is_active, 404);

        $maxParticipants = max(1, (int) ($activity->max_participants ?: 20));
        $requestedParticipants = max(1, min($maxParticipants, (int) $request->input('participants', 1)));

        $validator = validator($request->all(), [
            'booking_id' => ['nullable', 'integer'],
            'contact_email' => ['required', 'email'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'payment_gateway' => ['required', Rule::in(PaymentService::available())],
            'activity_date' => ['required', 'date'],
            'participants' => ['required', 'integer', 'min:1', 'max:'.$maxParticipants],
            'passengers' => ['required', 'array', 'size:'.$requestedParticipants],
            'passengers.*.first_name' => ['required', 'string', 'max:100'],
            'passengers.*.last_name' => ['required', 'string', 'max:100'],
            'passengers.*.title' => ['nullable', 'string', 'max:20'],
            'passengers.*.dob' => ['required', 'date', 'before_or_equal:today'],
            'passengers.*.nationality' => ['required', 'string', 'max:5'],
            'passengers.*.gender' => ['required', 'in:M,F'],
            'passengers.*.passport' => ['required', 'string', 'max:50'],
            'passengers.*.passport_expiry' => ['required', 'date', 'after_or_equal:today'],
            'special_requests' => ['nullable', 'string'],
        ]);

        $validator->after(function ($validator) use ($request, $requestedParticipants) {
            if (count((array) $request->input('passengers', [])) !== $requestedParticipants) {
                $validator->errors()->add('passengers', 'Passenger details must match the selected participant count.');
            }
        });

        $validated = $validator->validate();

        $booking = $this->resolveDraftBooking($activity, (int) ($validated['booking_id'] ?? 0)) ?: $this->createDraftBooking(
            $activity,
            (string) $validated['activity_date'],
            (int) $validated['participants']
        );

        $this->applyCheckoutDataToBooking($booking, $activity, $validated);
        $this->rememberCheckoutBooking($booking, $activity);

        $payment = PaymentService::gateway((string) $validated['payment_gateway'])->initiate($booking);

        return redirect()->away((string) $payment['url']);
    }

    public function bookingDetail(string $code): View
    {
        $booking = Booking::query()
            ->where('code', $code)
            ->where('object_model', 'activity')
            ->firstOrFail();

        if (auth()->check() && $booking->customer_id && (int) $booking->customer_id !== (int) auth()->id()) {
            abort(403);
        }

        $this->syncPendingPaymentStatus($booking);
        $booking->refresh();

        $passenger = ActivityBookingPassenger::query()->where('booking_id', $booking->id)->first();
        $passengers = $this->storedPassengers($booking, $passenger);
        $imageUrl = null;
        $imageId = (int) $booking->getMeta('activity_image_id', 0);
        if ($imageId > 0) {
            $imageUrl = MediaFile::query()->find($imageId)?->url;
        }

        return view('activity::booking-detail', compact('booking', 'passenger', 'passengers', 'imageUrl'));
    }

    private function resolveDraftBooking(Activity $activity, int $bookingId = 0): ?Booking
    {
        $candidateIds = array_values(array_unique(array_filter([
            $bookingId,
            (int) data_get(session('activity_checkout'), 'booking_id', 0),
        ])));

        foreach ($candidateIds as $candidateId) {
            $booking = Booking::query()
                ->whereKey($candidateId)
                ->where('object_model', 'activity')
                ->where('status', Booking::DRAFT)
                ->first();

            if ($this->matchesActivityDraftBooking($booking, $activity)) {
                return $booking;
            }
        }

        if (! auth()->check()) {
            return null;
        }

        return Booking::query()
            ->where('object_model', 'activity')
            ->where('customer_id', auth()->id())
            ->where('status', Booking::DRAFT)
            ->latest('id')
            ->get()
            ->first(fn (Booking $booking) => $this->matchesActivityDraftBooking($booking, $activity));
    }

    private function createDraftBooking(Activity $activity, string $activityDate, int $participants): Booking
    {
        $participants = $this->limitParticipants($activity, $participants);
        $unitPrice = (float) ($activity->price_per_person ?: 0);
        $total = round($unitPrice * $participants, 2);

        $booking = new Booking();
        $booking->object_model = 'activity';
        $booking->customer_id = auth()->id();
        $booking->status = Booking::DRAFT;
        $booking->total = $total;
        $booking->pay_now = $total;
        $booking->paid = 0;
        $booking->currency = strtoupper((string) ($activity->currency ?: 'AED'));
        $booking->first_name = '';
        $booking->last_name = '';
        $booking->email = '';
        $booking->phone = '';
        $booking->customer_notes = '';
        $booking->save();

        $this->syncActivityMeta($booking, $activity, $activityDate, $participants, '');

        return $booking;
    }

    private function refreshDraftBooking(Booking $booking, Activity $activity, string $activityDate, int $participants): void
    {
        $participants = $this->limitParticipants($activity, $participants);
        $unitPrice = (float) ($activity->price_per_person ?: 0);
        $total = round($unitPrice * $participants, 2);

        $booking->total = $total;
        $booking->pay_now = $total;
        $booking->currency = strtoupper((string) ($activity->currency ?: 'AED'));
        $booking->save();

        $this->syncActivityMeta($booking, $activity, $activityDate, $participants, (string) $booking->getMeta('activity_special_requests', ''));
    }

    private function applyCheckoutDataToBooking(Booking $booking, Activity $activity, array $validated): void
    {
        $participants = $this->limitParticipants($activity, (int) $validated['participants']);
        $unitPrice = (float) ($activity->price_per_person ?: 0);
        $total = round($unitPrice * $participants, 2);
        $passengers = array_map(
            fn (array $passenger, int $index) => $this->normalizePassengerPayload($passenger, $index),
            array_values((array) ($validated['passengers'] ?? [])),
            array_keys(array_values((array) ($validated['passengers'] ?? [])))
        );
        $leadPassenger = $passengers[0] ?? [];

        $booking->total = $total;
        $booking->pay_now = $total;
        $booking->currency = strtoupper((string) ($activity->currency ?: 'AED'));
        $booking->first_name = (string) ($leadPassenger['first_name'] ?? '');
        $booking->last_name = (string) ($leadPassenger['last_name'] ?? '');
        $booking->email = (string) $validated['contact_email'];
        $booking->phone = (string) $validated['contact_phone'];
        $booking->customer_notes = (string) ($validated['special_requests'] ?? '');
        $booking->save();

        $this->syncActivityMeta(
            $booking,
            $activity,
            (string) $validated['activity_date'],
            $participants,
            (string) ($validated['special_requests'] ?? '')
        );
        $booking->updateMeta('payment_gateway', (string) $validated['payment_gateway']);
        $booking->updateMeta('activity_passengers', $passengers);

        ActivityBookingPassenger::query()->updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'activity_id' => $activity->id,
                'title' => (string) ($leadPassenger['title'] ?? ''),
                'first_name' => (string) ($leadPassenger['first_name'] ?? ''),
                'last_name' => (string) ($leadPassenger['last_name'] ?? ''),
                'dob' => (string) ($leadPassenger['dob'] ?? ''),
                'nationality' => (string) ($leadPassenger['nationality'] ?? ''),
                'gender' => (string) ($leadPassenger['gender'] ?? ''),
                'passport_number' => (string) ($leadPassenger['passport'] ?? ''),
                'passport_expiry_date' => (string) ($leadPassenger['passport_expiry'] ?? ''),
                'contact_email' => (string) $validated['contact_email'],
                'contact_phone' => (string) $validated['contact_phone'],
                'participants' => $participants,
                'activity_date' => (string) $validated['activity_date'],
                'special_requests' => (string) ($validated['special_requests'] ?? ''),
                'payment_gateway' => (string) $validated['payment_gateway'],
            ]
        );
    }

    private function syncActivityMeta(Booking $booking, Activity $activity, string $activityDate, int $participants, string $specialRequests): void
    {
        $booking->updateMeta('booking_type', 'activity_local');
        $booking->updateMeta('activity_id', $activity->id);
        $booking->updateMeta('activity_title', $activity->title);
        $booking->updateMeta('activity_slug', $activity->slug);
        $booking->updateMeta('activity_image_id', (int) ($activity->image_id ?: 0));
        $booking->updateMeta('activity_city', (string) ($activity->city ?: ''));
        $booking->updateMeta('activity_country', (string) ($activity->country ?: ''));
        $booking->updateMeta('activity_category', (string) ($activity->category ?: ''));
        $booking->updateMeta('activity_duration', (string) ($activity->duration ?: ''));
        $booking->updateMeta('activity_date', $activityDate);
        $booking->updateMeta('activity_participants', $participants);
        $booking->updateMeta('activity_unit_price', (float) ($activity->price_per_person ?: 0));
        $booking->updateMeta('activity_currency', strtoupper((string) ($activity->currency ?: 'AED')));
        $booking->updateMeta('activity_special_requests', $specialRequests);
    }

    private function buildCountrySearchTerms(string $country, string $countryCode = ''): array
    {
        $segments = preg_split('/[\s\-]+/', trim($country)) ?: [];
        $acronym = collect($segments)
            ->filter()
            ->map(fn (string $segment) => strtoupper(substr($segment, 0, 1)))
            ->implode('');

        return collect([$countryCode, $acronym, strtoupper(str_replace(' ', '', $country))])
            ->filter()
            ->map(fn (string $value) => strtoupper(trim($value)))
            ->unique()
            ->values()
            ->all();
    }

    private function limitParticipants(Activity $activity, int $participants): int
    {
        $participants = max(1, $participants);
        if (! $activity->max_participants) {
            return $participants;
        }

        return min((int) $activity->max_participants, $participants);
    }

    private function rememberCheckoutBooking(Booking $booking, Activity $activity): void
    {
        session([
            'activity_checkout' => [
                'booking_id' => $booking->id,
                'activity_id' => $activity->id,
            ],
        ]);
    }

    private function matchesActivityDraftBooking(?Booking $booking, Activity $activity): bool
    {
        if (! $booking) {
            return false;
        }

        if ($booking->object_model !== 'activity' || $booking->status !== Booking::DRAFT) {
            return false;
        }

        if (auth()->check() && $booking->customer_id && (int) $booking->customer_id !== (int) auth()->id()) {
            return false;
        }

        return (int) $booking->getMeta('activity_id', 0) === (int) $activity->id;
    }

    private function syncPendingPaymentStatus(Booking $booking): void
    {
        if (! in_array($booking->status, [Booking::DRAFT, Booking::UNPAID, Booking::BOOKING_FAILED], true)) {
            return;
        }

        if ((string) $booking->getMeta('payment_gateway', '') !== 'ngenius') {
            return;
        }

        if ((string) $booking->getMeta('ngenius_order_ref', '') === '') {
            return;
        }

        try {
            (new NGeniusGateway())->syncBookingStatus($booking);
        } catch (\Throwable $e) {
            \Log::warning('Activity booking payment sync failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function buildPassengerForms(?Booking $booking, ?ActivityBookingPassenger $passenger, int $participants): array
    {
        $submitted = old('passengers');
        $sourcePassengers = is_array($submitted) && ! empty($submitted)
            ? array_values($submitted)
            : $this->storedPassengers($booking, $passenger);

        $forms = [];
        for ($index = 0; $index < $participants; $index++) {
            $forms[] = $this->normalizePassengerPayload((array) ($sourcePassengers[$index] ?? []), $index);
        }

        return $forms;
    }

    private function storedPassengers(?Booking $booking, ?ActivityBookingPassenger $passenger = null): array
    {
        $stored = $booking ? $booking->getJsonMeta('activity_passengers') : [];
        $stored = is_array($stored) ? array_values(array_filter($stored, 'is_array')) : [];

        if (! empty($stored)) {
            return array_map(
                fn (array $item, int $index) => $this->normalizePassengerPayload($item, $index),
                $stored,
                array_keys($stored)
            );
        }

        if (! $passenger) {
            return [];
        }

        return [[
            'title' => (string) ($passenger->title ?: 'Mr'),
            'first_name' => (string) ($passenger->first_name ?: ''),
            'last_name' => (string) ($passenger->last_name ?: ''),
            'dob' => (string) ($passenger->dob ?: ''),
            'nationality' => (string) ($passenger->nationality ?: ''),
            'gender' => (string) ($passenger->gender ?: 'M'),
            'passport' => (string) ($passenger->passport_number ?: ''),
            'passport_expiry' => (string) ($passenger->passport_expiry_date ?: ''),
        ]];
    }

    private function normalizePassengerPayload(array $passenger, int $index): array
    {
        return [
            'title' => (string) ($passenger['title'] ?? 'Mr'),
            'first_name' => (string) ($passenger['first_name'] ?? ''),
            'last_name' => (string) ($passenger['last_name'] ?? ''),
            'dob' => (string) ($passenger['dob'] ?? ''),
            'nationality' => strtoupper((string) ($passenger['nationality'] ?? '')),
            'gender' => (string) ($passenger['gender'] ?? 'M'),
            'passport' => (string) ($passenger['passport'] ?? $passenger['passport_number'] ?? ''),
            'passport_expiry' => (string) ($passenger['passport_expiry'] ?? $passenger['passport_expiry_date'] ?? ''),
            'label' => $index === 0 ? 'Lead Passenger' : 'Passenger ' . ($index + 1),
        ];
    }
}
