<?php

namespace Modules\Admin\Http\Controllers;

use App\Enums\BookingObjectModelEnum;
use App\Enums\VendorStatusEnum;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\Service;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $selectedCurrency = strtoupper((string) session('currency', \App\Models\Currency::defaultCode()));
        $completedStatuses = [Booking::COMPLETED, Booking::PAID];
        $pendingStatuses = [Booking::DRAFT, Booking::UNPAID];
        $inProgressStatuses = [Booking::CONFIRMED];
        $failedStatuses = [Booking::BOOKING_FAILED, Booking::CANCELLED];
        $cancelledStatuses = [Booking::CANCELLED];

        $completedBookings = Booking::query()
            ->whereIn('status', $completedStatuses)
            ->get(['currency', 'total', 'vendor_amount']);

        $revenue = $this->convertGroupedAmount($completedBookings->groupBy(fn (Booking $booking) => strtoupper((string) ($booking->currency ?: $selectedCurrency))), 'total', $selectedCurrency);
        $profit = $this->convertGroupedAmount($completedBookings->groupBy(fn (Booking $booking) => strtoupper((string) ($booking->currency ?: $selectedCurrency))), 'vendor_amount', $selectedCurrency);

        $stats = [
            'currency' => $selectedCurrency,
            'revenue' => $revenue,
            'profit' => $profit,
            'profit_margin' => $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0.0,
            'bookings' => Booking::query()->count(),
            'pending_bookings' => Booking::query()->whereIn('status', $pendingStatuses)->count(),
            'in_progress_payments' => Booking::query()
                ->whereNotIn('status', array_merge($completedStatuses, $failedStatuses))
                ->where(function ($query) use ($pendingStatuses, $inProgressStatuses) {
                    $query->whereIn('status', $inProgressStatuses)
                        ->orWhereHas('payment', fn ($payment) => $payment->where('status', 'draft'));
                })
                ->count(),
            'failed_payments' => Booking::query()
                ->whereIn('status', $failedStatuses)
                ->orWhereHas('payment', fn ($payment) => $payment->where('status', 'failed'))
                ->count(),
            'cancelled_bookings' => Booking::query()
                ->whereIn('status', $cancelledStatuses)
                ->count(),
        ];

        $revenueBreakdownTypes = [
            'hotel' => [
                'label' => 'Hotel Revenue',
                'icon' => 'hotel',
                'color' => '#0ea5e9',
                'statuses' => $completedStatuses,
                'object_models' => [BookingObjectModelEnum::Hotel->value],
            ],
            'activity' => [
                'label' => 'Activity Revenue',
                'icon' => 'activity',
                'color' => '#334155',
                'statuses' => $completedStatuses,
                'object_models' => [BookingObjectModelEnum::Activity->value],
            ],
            'flight' => [
                'label' => 'Flight Revenue',
                'icon' => 'flight',
                'color' => '#f59e0b',
                'statuses' => $completedStatuses,
                'object_models' => [BookingObjectModelEnum::Flight->value],
            ],
        ];

        $revenueBreakdown = [];
        foreach ($revenueBreakdownTypes as $key => $config) {
            $rows = Booking::query()
                ->whereIn('status', $config['statuses'])
                ->whereIn('object_model', $config['object_models'])
                ->get(['currency', 'total'])
                ->groupBy(fn (Booking $booking) => strtoupper((string) ($booking->currency ?: $selectedCurrency)));

            $amount = $this->convertGroupedAmount($rows, 'total', $selectedCurrency);
            $count = Booking::query()
                ->whereIn('status', $config['statuses'])
                ->whereIn('object_model', $config['object_models'])
                ->count();

            $revenueBreakdown[$key] = [
                'label' => $config['label'],
                'icon' => $config['icon'],
                'color' => $config['color'],
                'amount' => $amount,
                'count' => $count,
                'share' => $revenue > 0 ? round(($amount / $revenue) * 100, 1) : 0.0,
            ];
        }

        $bookingStatusBreakdownTypes = [
            [
                'label' => 'Hotels',
                'icon' => 'hotel',
                'color' => '#0ea5e9',
                'object_models' => [BookingObjectModelEnum::Hotel->value],
            ],
            [
                'label' => 'Activities',
                'icon' => 'activity',
                'color' => '#14b8a6',
                'object_models' => [BookingObjectModelEnum::Activity->value],
            ],
            [
                'label' => 'Flights',
                'icon' => 'flight',
                'color' => '#f59e0b',
                'object_models' => [BookingObjectModelEnum::Flight->value],
            ],
        ];

        $bookingStatusBreakdown = [];
        foreach ($bookingStatusBreakdownTypes as $config) {
            $baseQuery = Booking::query()->whereIn('object_model', $config['object_models']);
            $pending = (clone $baseQuery)->whereIn('status', $pendingStatuses)->count();
            $confirmed = (clone $baseQuery)
                ->whereIn('status', [Booking::CONFIRMED, Booking::COMPLETED, Booking::PAID])
                ->count();
            $cancelled = (clone $baseQuery)->whereIn('status', [Booking::CANCELLED])->count();
            $total = (clone $baseQuery)->count();

            $bookingStatusBreakdown[] = [
                'label' => $config['label'],
                'icon' => $config['icon'],
                'color' => $config['color'],
                'pending' => $pending,
                'confirmed' => $confirmed,
                'cancelled' => $cancelled,
                'total' => $total,
            ];
        }

        $revenueBreakdown['profit'] = [
            'label' => 'Total Profit',
            'icon' => 'profit',
            'color' => '#10b981',
            'amount' => $profit,
            'count' => Booking::query()->whereIn('status', $completedStatuses)->count(),
            'share' => $stats['profit_margin'],
            'meta' => 'Profit margin ' . number_format($stats['profit_margin'], 1) . '% on ' . $selectedCurrency . ' ' . number_format($revenue, 0),
        ];

        $bookingsChart = Booking::query()
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->get(['currency', 'total', 'paid', 'status', 'created_at'])
            ->groupBy(fn (Booking $booking) => optional($booking->created_at)->toDateString());

        $chartLabels = [];
        $chartRevenue = [];
        $chartEarnings = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $key = $day->toDateString();
            $row = $bookingsChart->get($key);

            $chartLabels[] = $day->format('M d');
            $chartRevenue[] = $this->convertGroupedAmount(
                ($row ?? collect())->groupBy(fn (Booking $booking) => strtoupper((string) ($booking->currency ?: $selectedCurrency))),
                'total',
                $selectedCurrency
            );
            $chartEarnings[] = $this->convertGroupedAmount(
                ($row ?? collect())->whereIn('status', $completedStatuses)->groupBy(fn (Booking $booking) => strtoupper((string) ($booking->currency ?: $selectedCurrency))),
                'paid',
                $selectedCurrency
            );
        }

        $recentBookings = Booking::query()
            ->with(['customer', 'vendor'])
            ->whereIn('status', $completedStatuses)
            ->latest()
            ->take(4)
            ->get(['id', 'code', 'object_model', 'currency', 'total', 'status', 'created_at'])
            ->map(function (Booking $booking) use ($selectedCurrency) {
                $label = match ($booking->object_model) {
                    BookingObjectModelEnum::Flight->value => 'Flight',
                    BookingObjectModelEnum::Hotel->value => 'Hotel',
                    BookingObjectModelEnum::Activity->value => 'Activity',
                    BookingObjectModelEnum::Tour->value => 'Tour',
                    BookingObjectModelEnum::Car->value => 'Car',
                    default => 'Booking',
                };

                $sourceCurrency = strtoupper((string) ($booking->currency ?: $selectedCurrency));
                $amount = (float) $booking->total;

                if ($sourceCurrency !== $selectedCurrency && $amount > 0) {
                    $amount = (float) currency($amount, $sourceCurrency, $selectedCurrency, false);
                }

                return [
                    'id' => $booking->id,
                    'item' => $label . ': ' . ($booking->code ?? 'N/A'),
                    'total' => number_format($amount, 0),
                    'currency' => $selectedCurrency,
                    'status' => strtoupper(Booking::COMPLETED),
                    'date' => optional($booking->created_at)->format('M d'),
                ];
            });

        $summary = [
            'hotels' => Hotel::query()->count(),
            'pending_vendors' => User::query()->where('user_type', UserType::Vendor->value)->where('vendor_status', VendorStatusEnum::Pending->value)->count(),
            'verified_vendors' => User::query()->where('user_type', UserType::Vendor->value)->where('vendor_status', VendorStatusEnum::Verified->value)->count(),
        ];

        $paymentMethodCounts = Payment::query()
            ->select('payment_gateway', DB::raw('count(*) as total'))
            ->whereIn('payment_gateway', ['stripe', 'ngenius'])
            ->where('status', 'completed')
            ->groupBy('payment_gateway')
            ->pluck('total', 'payment_gateway');

        $paymentMethodTotal = max(1, (int) $paymentMethodCounts->sum());

        $paymentMethods = [
            [
                'key' => 'stripe',
                'label' => 'Credit Card (Stripe)',
                'subtext' => 'Visa, Mastercard, Amex',
                'color' => '#14b8a6',
                'count' => (int) ($paymentMethodCounts['stripe'] ?? 0),
            ],
            [
                'key' => 'ngenius',
                'label' => 'N-Genius',
                'subtext' => 'Hosted checkout by Network International',
                'color' => '#f59e0b',
                'count' => (int) ($paymentMethodCounts['ngenius'] ?? 0),
            ],
        ];

        foreach ($paymentMethods as &$method) {
            $method['share'] = round(($method['count'] / $paymentMethodTotal) * 100, 1);
        }
        unset($method);

        return view('admin::dashboard', compact('stats', 'chartLabels', 'chartRevenue', 'chartEarnings', 'recentBookings', 'summary', 'revenueBreakdown', 'paymentMethods', 'bookingStatusBreakdown'));
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
