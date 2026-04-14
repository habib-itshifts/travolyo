<?php

namespace Modules\Admin\Http\Controllers;

use App\Enums\BookingObjectModelEnum;
use App\Enums\VendorStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Hotel\Models\Hotel;
use Modules\Activity\Models\Activity;

class AdminCrmController extends Controller
{
    public function index()
    {
        $selectedCurrency = strtoupper((string) session('currency', \App\Models\Currency::defaultCode()));
        $completedStatuses = [Booking::COMPLETED, Booking::PAID];
        $failedStatuses = [Booking::BOOKING_FAILED, Booking::CANCELLED];

        $hotelBookings = Booking::query()
            ->where('object_model', BookingObjectModelEnum::Hotel->value)
            ->get(['currency', 'total', 'vendor_amount', 'commission_amount', 'status']);

        $totalRevenue = $this->convertGroupedAmount(
            $hotelBookings->groupBy(fn (Booking $booking) => strtoupper((string) ($booking->currency ?: $selectedCurrency))),
            'total',
            $selectedCurrency
        );

        $totalCommission = $this->convertGroupedAmount(
            $hotelBookings->groupBy(fn (Booking $booking) => strtoupper((string) ($booking->currency ?: $selectedCurrency))),
            'commission_amount',
            $selectedCurrency
        );

        $totalBookings = Booking::query()->count();
        $activeVendors = User::query()
            ->whereNotNull('vendor_status')
            ->whereIn('vendor_status', [VendorStatusEnum::Verified->value, VendorStatusEnum::Approved->value])
            ->count();
        $pendingVendors = User::query()
            ->whereNotNull('vendor_status')
            ->where('vendor_status', VendorStatusEnum::Pending->value)
            ->count();

        $failedBookings = Booking::query()->whereIn('status', $failedStatuses)->count();

        $stats = [
            'currency' => $selectedCurrency,
            'total_revenue' => $totalRevenue,
            'total_commission' => $totalCommission,
            'total_bookings' => $totalBookings,
            'pending_vendors' => $pendingVendors,
            'active_vendors' => $activeVendors,
            'failed_bookings' => $failedBookings,
        ];

        $recentBookings = Booking::query()
            ->with(['vendor:id,name,business_name', 'customer:id,name,email'])
            ->latest()
            ->take(10)
            ->get(['id', 'code', 'object_model', 'currency', 'total', 'commission_amount', 'vendor_amount', 'status', 'vendor_id', 'customer_id', 'created_at'])
            ->map(function (Booking $booking) use ($selectedCurrency) {
                $sourceCurrency = strtoupper((string) ($booking->currency ?: $selectedCurrency));
                $amount = (float) $booking->total;
                $commission = (float) $booking->commission_amount;

                if ($sourceCurrency !== $selectedCurrency) {
                    $amount = (float) currency($amount, $sourceCurrency, $selectedCurrency, false);
                    $commission = (float) currency($commission, $sourceCurrency, $selectedCurrency, false);
                }

                return [
                    'code' => $booking->code ?? 'N/A',
                    'vendor' => $booking->vendor?->name ?: 'Vendor',
                    'vendor_business' => $booking->vendor?->business_name ?: '—',
                    'customer' => $booking->customer?->name ?: 'Customer',
                    'object' => ucfirst((string) $booking->object_model),
                    'total' => number_format($amount, 0),
                    'commission' => number_format($commission, 0),
                    'status' => $booking->status,
                    'date' => optional($booking->created_at)->format('d M Y'),
                ];
            });

        $vendorBreakdown = User::query()
            ->whereNotNull('vendor_status')
            ->withCount([
                'bookingsAsVendor as hotel_bookings_count' => function ($query) {
                    $query->where('object_model', BookingObjectModelEnum::Hotel->value)
                        ->whereIn('status', [Booking::COMPLETED, Booking::PAID, Booking::CONFIRMED]);
                },
            ])
            ->withSum([
                'bookingsAsVendor as hotel_commission_total' => function ($query) {
                    $query->where('object_model', BookingObjectModelEnum::Hotel->value)
                        ->whereIn('status', [Booking::COMPLETED, Booking::PAID, Booking::CONFIRMED]);
                },
            ], 'commission_amount')
            ->orderByDesc('hotel_commission_total')
            ->take(5)
            ->get(['id', 'name', 'business_name', 'vendor_status', 'created_at']);

        $monthlyTrendLabels = [];
        $monthlyRevenue = [];
        $monthlyCommission = [];
        $sixMonths = Booking::query()->where('created_at', '>=', now()->subMonths(5)->startOfMonth())->get(['currency', 'total', 'commission_amount', 'created_at', 'object_model', 'status']);

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $key = $month->format('Y-m');
            $monthlyTrendLabels[] = strtoupper($month->format('M'));
            $monthRows = $sixMonths->filter(fn ($b) => optional($b->created_at)->format('Y-m') === $key);
            $monthlyRevenue[] = $this->convertGroupedAmount($monthRows->groupBy(fn (Booking $booking) => strtoupper((string) ($booking->currency ?: $selectedCurrency))), 'total', $selectedCurrency);
            $monthlyCommission[] = $this->convertGroupedAmount($monthRows->groupBy(fn (Booking $booking) => strtoupper((string) ($booking->currency ?: $selectedCurrency))), 'commission_amount', $selectedCurrency);
        }

        return view('admin::crm-dashboard', compact(
            'stats',
            'recentBookings',
            'vendorBreakdown',
            'monthlyTrendLabels',
            'monthlyRevenue',
            'monthlyCommission'
        ));
    }

    private function convertGroupedAmount($groupedBookings, string $field, string $targetCurrency): float
    {
        return (float) $groupedBookings->reduce(function (float $carry, $items, string $currency) use ($field, $targetCurrency) {
            $amount = (float) $items->sum($field);

            if ($amount <= 0) {
                return $carry;
            }

            if ($currency === $targetCurrency) {
                return $carry + $amount;
            }

            return $carry + (float) currency($amount, $currency, $targetCurrency, false);
        }, 0.0);
    }
}
