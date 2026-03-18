<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\Booking;
use App\Enums\BookingObjectModelEnum;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $type   = $request->input('type', 'all');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Booking::with(['customer', 'vendor'])
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
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($q2) => $q2->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $bookings = $query->paginate(20)->withQueryString();

        $counts = [
            'all'    => Booking::count(),
            'flight' => Booking::where('object_model', BookingObjectModelEnum::Flight->value)->count(),
            'hotel'  => Booking::where('object_model', BookingObjectModelEnum::Hotel->value)->count(),
        ];

        return view('admin::bookings.index', compact('bookings', 'counts', 'type', 'status', 'search'));
    }
}
