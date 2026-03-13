<?php

namespace Modules\Hotel\Actions;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Modules\Hotel\DTOs\BookHotelDto;
use Modules\Hotel\Enums\BookingRoomStatusEnum;
use Modules\Hotel\Exceptions\HotelException;
use Modules\Hotel\Models\BookingRoom;
use Modules\Hotel\Models\HotelRoom;

class BookHotelAction
{
    public function execute(BookHotelDto $dto): Booking
    {
        $checkIn  = Carbon::parse($dto->checkIn);
        $checkOut = Carbon::parse($dto->checkOut);

        if ($checkOut->lte($checkIn)) {
            throw HotelException::invalidDates();
        }

        $room = HotelRoom::active()->find($dto->hotelRoomId);
        if (! $room) {
            throw HotelException::roomNotFound($dto->hotelRoomId);
        }

        $nights     = $checkIn->diffInDays($checkOut);
        $totalPrice = $room->calculatePrice($nights, $dto->adults, $dto->children);

        $booking = Booking::create([
            'code'            => 'TRV-' . now()->format('Y') . '-' . strtoupper(Str::random(6)),
            'object_model'    => 'hotel',
            'customer_id'     => $dto->customerId,
            'status'          => 'pending',
            'total'           => $totalPrice,
            'pay_now'         => $totalPrice,
            'paid'            => 0,
            'currency'        => $dto->currency,
            'first_name'      => $dto->firstName,
            'last_name'       => $dto->lastName,
            'email'           => $dto->email,
            'phone'           => $dto->phone,
            'customer_notes'  => $dto->specialRequests,
        ]);

        BookingRoom::create([
            'booking_id'      => $booking->id,
            'hotel_room_id'   => $room->id,
            'check_in'        => $checkIn->toDateString(),
            'check_out'       => $checkOut->toDateString(),
            'nights'          => $nights,
            'adults'          => $dto->adults,
            'children'        => $dto->children,
            'unit_price'      => $room->base_price,
            'total_price'     => $totalPrice,
            'extra_services'  => $dto->extraServices ?: null,
            'special_requests'=> $dto->specialRequests,
            'status'          => BookingRoomStatusEnum::Pending->value,
        ]);

        return $booking->load('bookingRooms.room.hotel');
    }
}
