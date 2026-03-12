<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    public function show(string $code): JsonResponse
    {
        $booking = Booking::where('code', $code)->first();

        if (! $booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.',
            ], 404);
        }

        $data = [
            'code'         => $booking->code,
            'status'       => $booking->status,
            'object_model' => $booking->object_model,
            'total'        => $booking->total,
            'pay_now'      => $booking->pay_now,
            'paid'         => $booking->paid,
            'currency'     => $booking->currency,
            'first_name'   => $booking->first_name,
            'last_name'    => $booking->last_name,
            'email'        => $booking->email,
            'phone'        => $booking->phone,
            'created_at'   => $booking->created_at?->toISOString(),
        ];

        // Include object-specific meta
        if ($booking->object_model === 'flight') {
            $data['flight']     = $booking->getJsonMeta('flight_details');
            $data['passengers'] = $booking->getJsonMeta('flight_passengers');
        }

        return response()->json([
            'success' => true,
            'booking' => $data,
        ]);
    }
}
