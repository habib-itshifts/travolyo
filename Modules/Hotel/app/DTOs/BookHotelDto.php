<?php

namespace Modules\Hotel\DTOs;

class BookHotelDto
{
    public function __construct(
        public readonly int     $hotelRoomId,
        public readonly string  $checkIn,
        public readonly string  $checkOut,
        public readonly int     $adults,
        public readonly int     $children,
        public readonly string  $firstName,
        public readonly string  $lastName,
        public readonly string  $email,
        public readonly string  $phone,
        public readonly ?string $specialRequests = null,
        public readonly array   $extraServices = [],
        public readonly string  $currency = 'USD',
        public readonly ?int    $customerId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            hotelRoomId:     (int) $data['hotel_room_id'],
            checkIn:         $data['check_in'],
            checkOut:        $data['check_out'],
            adults:          (int) $data['adults'],
            children:        (int) ($data['children'] ?? 0),
            firstName:       $data['first_name'],
            lastName:        $data['last_name'],
            email:           $data['email'],
            phone:           $data['phone'],
            specialRequests: $data['special_requests'] ?? null,
            extraServices:   $data['extra_services'] ?? [],
            currency:        $data['currency'] ?? 'USD',
            customerId:      $data['customer_id'] ?? null,
        );
    }

    public function nights(): int
    {
        return (int) now()->parse($this->checkIn)->diffInDays($this->checkOut);
    }
}
