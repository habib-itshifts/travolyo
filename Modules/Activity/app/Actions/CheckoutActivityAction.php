<?php

namespace Modules\Activity\Actions;

use App\Models\Booking;
use App\Models\User;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\Cache;
use Modules\Activity\DTOs\CheckoutActivityDto;
use Modules\Activity\Exceptions\ActivityException;
use Modules\Activity\Models\Activity;
use Modules\Activity\Models\ActivityBookingPassenger;

class CheckoutActivityAction
{
    public function handle(CheckoutActivityDto $dto): array
    {
        $cached = Cache::get('activity_checkout_' . $dto->checkoutToken);

        if (empty($cached)) {
            throw ActivityException::sessionExpired();
        }

        $activity = Activity::find((int) $cached['offer_id']);
        $provider = $cached['provider'] ?? 'local';

        // Resolve vendor + commission (local activities only)
        $vendorId       = null;
        $commissionType = null;
        $commissionRate = null;

        if ($provider === 'local' && $activity?->author_id) {
            $vendorId = $activity->author_id;
            $vendor   = User::find($vendorId);
            if ($vendor) {
                $commissionType = $vendor->vendor_commission_type;
                $commissionRate = $vendor->vendor_commission_amount;
            }
        }

        $participants = (int) $cached['participants'];
        $unitPrice    = (float) $cached['unit_price'];
        $totalPrice   = (float) $cached['total_price'];

        // Normalize passengers
        $passengers = array_map(
            fn (array $p, int $i) => $this->normalizePassenger($p, $i),
            array_values($dto->passengers),
            array_keys(array_values($dto->passengers))
        );
        $leadPassenger = $passengers[0] ?? [];

        $booking = Booking::create([
            'object_model'      => 'activity',
            'object_id'         => $provider === 'local' ? (int) $cached['offer_id'] : null,
            'author_id'         => $dto->customerId,
            'customer_id'       => $dto->customerId,
            'vendor_id'         => $vendorId,
            'status'            => 'draft',
            'start_date'        => $cached['activity_date'],
            'total_guests'      => $participants,
            'currency'          => $cached['currency'],
            'total'             => $totalPrice,
            'pay_now'           => $totalPrice,
            'paid'              => 0,
            'commission_type'   => $commissionType,
            'commission'        => $commissionRate,
            'first_name'        => (string) ($leadPassenger['first_name'] ?? ''),
            'last_name'         => (string) ($leadPassenger['last_name'] ?? ''),
            'email'             => $dto->contactEmail,
            'phone'             => $dto->contactPhone,
            'customer_notes'    => $dto->specialRequests,
            'source'            => $provider,
            'platform'          => Booking::detectPlatform(),
        ]);

        // Apply commission
        if ($commissionType && $commissionRate) {
            $booking->applyCommission();
            $booking->save();
        }

        // Store activity meta
        $booking->addMeta('booking_type', 'activity_' . $provider);
        $booking->addMeta('activity_id', (int) $cached['offer_id']);
        $booking->addMeta('activity_title', $cached['activity_title'] ?? '');
        $booking->addMeta('activity_slug', $cached['activity_slug'] ?? '');
        $booking->addMeta('activity_image_id', (int) ($cached['activity_image_id'] ?? 0));
        $booking->addMeta('activity_city', $cached['city'] ?? '');
        $booking->addMeta('activity_country', $cached['country'] ?? '');
        $booking->addMeta('activity_category', $cached['category'] ?? '');
        $booking->addMeta('activity_duration', $cached['duration'] ?? '');
        $booking->addMeta('activity_date', $cached['activity_date']);
        $booking->addMeta('activity_participants', $participants);
        $booking->addMeta('activity_unit_price', $unitPrice);
        $booking->addMeta('activity_currency', $cached['currency']);
        $booking->addMeta('payment_gateway', $dto->paymentGateway);
        $booking->addMeta('activity_passengers', $passengers);

        if ($dto->specialRequests) {
            $booking->addMeta('activity_special_requests', $dto->specialRequests);
        }

        // Create ActivityBookingPassenger record for lead passenger
        if ($provider === 'local') {
            ActivityBookingPassenger::create([
                'booking_id'           => $booking->id,
                'activity_id'          => (int) $cached['offer_id'],
                'title'                => (string) ($leadPassenger['title'] ?? ''),
                'first_name'           => (string) ($leadPassenger['first_name'] ?? ''),
                'last_name'            => (string) ($leadPassenger['last_name'] ?? ''),
                'dob'                  => (string) ($leadPassenger['dob'] ?? ''),
                'nationality'          => (string) ($leadPassenger['nationality'] ?? ''),
                'gender'               => (string) ($leadPassenger['gender'] ?? ''),
                'passport_number'      => (string) ($leadPassenger['passport'] ?? ''),
                'passport_expiry_date' => (string) ($leadPassenger['passport_expiry'] ?? ''),
                'contact_email'        => $dto->contactEmail,
                'contact_phone'        => $dto->contactPhone,
                'participants'         => $participants,
                'activity_date'        => $cached['activity_date'],
                'special_requests'     => $dto->specialRequests ?? '',
                'payment_gateway'      => $dto->paymentGateway,
            ]);
        }

        $result = PaymentService::gateway($dto->paymentGateway)->initiate($booking);

        Cache::forget('activity_checkout_' . $dto->checkoutToken);

        return ['url' => $result['url'], 'booking_code' => $booking->code];
    }

    private function normalizePassenger(array $passenger, int $index): array
    {
        return [
            'title'           => (string) ($passenger['title'] ?? 'Mr'),
            'first_name'      => (string) ($passenger['first_name'] ?? ''),
            'last_name'       => (string) ($passenger['last_name'] ?? ''),
            'dob'             => (string) ($passenger['dob'] ?? ''),
            'nationality'     => strtoupper((string) ($passenger['nationality'] ?? '')),
            'gender'          => (string) ($passenger['gender'] ?? 'M'),
            'passport'        => (string) ($passenger['passport'] ?? $passenger['passport_number'] ?? ''),
            'passport_expiry' => (string) ($passenger['passport_expiry'] ?? $passenger['passport_expiry_date'] ?? ''),
            'label'           => $index === 0 ? 'Lead Passenger' : 'Passenger ' . ($index + 1),
        ];
    }
}
