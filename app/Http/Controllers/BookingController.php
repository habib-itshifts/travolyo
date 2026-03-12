<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function show(string $code): View
    {
        $booking = Booking::where('code', $code)->firstOrFail();

        return view('bookings.show', compact('booking'));
    }
}
