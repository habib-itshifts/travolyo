<?php

namespace Modules\Hotel\Actions;

use App\Models\Booking;
use App\Services\Payment\PaymentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Modules\Hotel\DTOs\CheckoutHotelDto;
use Modules\Hotel\Enums\BookingRoomStatusEnum;
use Modules\Hotel\Exceptions\HotelException;
use Modules\Hotel\Models\BookingRoom;
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

        $nights = $checkIn->diffInDays($checkOut);

        $room = HotelRoom::findOrFail((int) $hotel['room_id']);

        $booking = Booking::create([
            'code'           => 'TRV-' . now()->format('Y') . '-' . strtoupper(\Illuminate\Support\Str::random(6)),
            'object_model'   => 'hotel',
            'customer_id'    => $dto->customerId,
            'status'         => 'draft',
            'total'          => $hotel['total_price'],
            'pay_now'        => $hotel['total_price'],
            'paid'           => 0,
            'currency'       => $hotel['currency'],
            'first_name'     => $dto->firstName,
            'last_name'      => $dto->lastName,
            'email'          => $dto->email,
            'phone'          => $dto->phone,
            'customer_notes' => $dto->specialRequests,
        ]);

        BookingRoom::create([
            'booking_id'      => $booking->id,
            'hotel_room_id'   => $room->id,
            'check_in'        => $checkIn->toDateString(),
            'check_out'       => $checkOut->toDateString(),
            'nights'          => $nights,
            'adults'          => $hotel['adults'],
            'children'        => $hotel['children'],
            'unit_price'      => $hotel['unit_price'],
            'total_price'     => $hotel['total_price'],
            'extra_services'  => $dto->extraServices ?: null,
            'special_requests'=> $dto->specialRequests,
            'status'          => BookingRoomStatusEnum::Pending->value,
        ]);

        $result = PaymentService::gateway($dto->paymentGateway)->initiate($booking);

        Cache::forget('hotel_checkout_' . $dto->checkoutToken);

        return ['url' => $result['url']];
    }
}
