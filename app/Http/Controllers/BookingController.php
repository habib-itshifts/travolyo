<?php

namespace App\Http\Controllers;

use App\Enums\BookingObjectModelEnum;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function show(string $code): View|RedirectResponse
    {
        $booking = Booking::where('code', $code)->firstOrFail();

        if ($booking->object_model === BookingObjectModelEnum::Flight->value) {
            $passengers = $booking->getJsonMeta('flight_passengers') ?: [];
            $orderRef = $booking->getMeta('flight_pnr') ?: '';

            return view('flight::flights.confirmation', compact('booking', 'passengers', 'orderRef'));
        }

        if ($booking->object_model === BookingObjectModelEnum::Activity->value) {
            return redirect()->route('activities.booking.detail', ['code' => $booking->code]);
        }

        return view('bookings.show', compact('booking'));
    }
}
