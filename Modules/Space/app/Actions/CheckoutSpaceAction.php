<?php

namespace Modules\Space\Actions;

use App\Models\Booking;
use App\Models\User;
use App\Services\Payment\PaymentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Modules\Space\DTOs\CheckoutSpaceDto;
use Modules\Space\Exceptions\SpaceException;
use Modules\Space\Models\Space;

class CheckoutSpaceAction
{
    public function handle(CheckoutSpaceDto $dto): array
    {
        $cached = Cache::get('space_checkout_' . $dto->checkoutToken);

        if (empty($cached)) {
            throw SpaceException::checkoutExpired();
        }

        $checkIn  = Carbon::parse($cached['check_in']);
        $checkOut = Carbon::parse($cached['check_out']);

        if ($checkOut->lte($checkIn)) {
            throw SpaceException::invalidDates();
        }

        // Re-validate availability
        $space = Space::active()->find($cached['space_id']);
        if (! $space || ! $space->isAvailableForDates($cached['check_in'], $cached['check_out'])) {
            throw SpaceException::unavailable();
        }

        // Resolve vendor + commission
        $vendorId       = null;
        $commissionType = null;
        $commissionRate = null;

        if ($space->author_id) {
            $vendorId = $space->author_id;
            $vendor   = User::find($vendorId);
            if ($vendor) {
                $commissionType = $vendor->vendor_commission_type;
                $commissionRate = $vendor->vendor_commission_amount;
            }
        }

        // Store guest details for post-payment processing
        $cached['guest_details'] = [
            'first_name' => $dto->firstName,
            'last_name'  => $dto->lastName,
            'email'      => $dto->email,
            'phone'      => $dto->phone,
        ];

        $cached['special_requests'] = $dto->specialRequests;
        $cached['extra_services']   = $dto->extraServices;

        // Create draft booking
        $booking = Booking::create([
            'object_model'    => 'space',
            'object_id'       => $cached['space_id'],
            'author_id'       => $dto->customerId,
            'customer_id'     => $dto->customerId,
            'vendor_id'       => $vendorId,
            'status'          => 'draft',
            'start_date'      => $checkIn,
            'end_date'        => $checkOut,
            'total_guests'    => $cached['guests'],
            'currency'        => $cached['currency'],
            'total'           => $cached['total_price'],
            'pay_now'         => $cached['total_price'],
            'paid'            => 0,
            'commission_type'   => $commissionType,
            'commission'        => $commissionRate,
            'first_name'      => $dto->firstName,
            'last_name'       => $dto->lastName,
            'email'           => $dto->email,
            'phone'           => $dto->phone,
            'customer_notes'  => $dto->specialRequests,
            'source'          => 'local',
            'platform'        => Booking::detectPlatform(),
        ]);

        if ($commissionType && $commissionRate) {
            $booking->applyCommission();
            $booking->save();
        }

        $booking->addMeta('space_details', $cached);
        $booking->addMeta('payment_gateway', $dto->paymentGateway);

        if ($dto->specialRequests) {
            $booking->addMeta('special_requests', $dto->specialRequests);
        }

        if (! empty($dto->extraServices)) {
            $booking->addMeta('extra_services', $dto->extraServices);
        }

        $result = PaymentService::gateway($dto->paymentGateway)->initiate($booking);

        Cache::forget('space_checkout_' . $dto->checkoutToken);

        return ['url' => $result['url']];
    }
}
