<?php

namespace Modules\Customer\Http\Controllers;

use App\Models\Booking;
use App\Enums\BookingObjectModelEnum;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $customerId = auth()->id();
        $type       = $request->input('type', 'all');
        $status     = $request->input('status');
        $search     = $request->input('search');

        $query = Booking::with(['vendor'])
            ->where('customer_id', $customerId)
            ->orderByDesc('created_at');

        if ($type === 'flight') {
            $query->where('object_model', BookingObjectModelEnum::Flight->value);
        } elseif ($type === 'hotel') {
            $query->where('object_model', BookingObjectModelEnum::Hotel->value);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where('code', 'like', "%{$search}%");
        }

        $bookings = $query->paginate(15)->withQueryString();

        $base = Booking::where('customer_id', $customerId);
        $counts = [
            'all'    => (clone $base)->count(),
            'flight' => (clone $base)->where('object_model', BookingObjectModelEnum::Flight->value)->count(),
            'hotel'  => (clone $base)->where('object_model', BookingObjectModelEnum::Hotel->value)->count(),
        ];

        return view('customer::bookings.index', compact('bookings', 'counts', 'type', 'status', 'search'));
    }
}
