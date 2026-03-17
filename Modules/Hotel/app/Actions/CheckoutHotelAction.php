<?php

namespace Modules\Hotel\Actions;

use App\Models\Booking;
use App\Models\User;
use App\Services\Payment\PaymentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Modules\Hotel\DTOs\CheckoutHotelDto;
use Modules\Hotel\Enums\BookingRoomStatusEnum;
use Modules\Hotel\Exceptions\HotelException;
use Modules\Hotel\Models\BookingRoom;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;

class CheckoutHotelAction
{
    public function handle(CheckoutHotelDto $dto): array
    {
        $hotel = Cache::get('hotel_checkout_' . $dto->checkoutToken);

        if (empty($hotel)) {
            throw new \RuntimeException('Hotel session expired. Please search and select your room again.');
        }

        $checkIn  = Carbon::parse($hotel['check_in']);
        $checkOut = Carbon::parse($hotel['check_out']);

        if ($checkOut->lte($checkIn)) {
            throw HotelException::invalidDates();
        }

        $nights   = $checkIn->diffInDays($checkOut);
        $provider = $hotel['provider'] ?? 'local';

        // Resolve vendor + commission from the hotel's owner (local hotels only)
        $vendorId         = null;
        $commissionType   = null;
        $commissionRate   = null;

        if ($provider === 'local' && ! empty($hotel['offer_id'])) {
            $hotelModel = Hotel::find((int) $hotel['offer_id']);
            if ($hotelModel?->author_id) {
                $vendorId = $hotelModel->author_id;
                $vendor   = User::find($vendorId);
                if ($vendor) {
                    $commissionType = $vendor->vendor_commission_type;
                    $commissionRate = $vendor->vendor_commission_amount;
                }
            }
        }

        $booking = Booking::create([
            'object_model'    => 'hotel',
            'object_id'       => $provider === 'local' ? (int) ($hotel['offer_id'] ?? null) : null,
            'author_id'       => $dto->customerId,   // self-service: customer is the author
            'customer_id'     => $dto->customerId,
            'vendor_id'       => $vendorId,
            'status'          => 'draft',
            'start_date'      => $checkIn,
            'end_date'        => $checkOut,
            'total_guests'    => ($hotel['adults'] ?? 1) + ($hotel['children'] ?? 0),
            'currency'        => $hotel['currency'],
            'total'           => $hotel['total_price'],
            'pay_now'         => $hotel['total_price'],
            'paid'            => 0,
            'commission_type'   => $commissionType,
            'commission'        => $commissionRate,
            'first_name'      => $dto->firstName,
            'last_name'       => $dto->lastName,
            'email'           => $dto->email,
            'phone'           => $dto->phone,
            'customer_notes'  => $dto->specialRequests,
        ]);

        // Calculate commission_amount and vendor_amount now that total is set
        if ($commissionType && $commissionRate) {
            $booking->applyCommission();
            $booking->save();
        }

        // Store hotel details in booking meta for confirmation page
        $booking->addMeta('hotel_details', $hotel);
        $booking->addMeta('payment_gateway', $dto->paymentGateway);

        if ($dto->specialRequests) {
            $booking->addMeta('special_requests', $dto->specialRequests);
        }

        if (! empty($dto->extraServices)) {
            $booking->addMeta('extra_services', $dto->extraServices);
        }

        // Create BookingRoom record only for local hotel rooms (B2B rooms have no local DB record)
        if ($provider === 'local') {
            $room = HotelRoom::find((int) $hotel['room_id']);

            if ($room) {
                BookingRoom::create([
                    'booking_id'       => $booking->id,
                    'hotel_room_id'    => $room->id,
                    'check_in'         => $checkIn->toDateString(),
                    'check_out'        => $checkOut->toDateString(),
                    'nights'           => $nights,
                    'adults'           => $hotel['adults'],
                    'children'         => $hotel['children'],
                    'unit_price'       => $hotel['unit_price'],
                    'total_price'      => $hotel['total_price'],
                    'extra_services'   => $dto->extraServices ?: null,
                    'special_requests' => $dto->specialRequests,
                    'status'           => BookingRoomStatusEnum::Pending->value,
                ]);
            }
        }

        $result = PaymentService::gateway($dto->paymentGateway)->initiate($booking);

        Cache::forget('hotel_checkout_' . $dto->checkoutToken);

        return ['url' => $result['url']];
    }
}
