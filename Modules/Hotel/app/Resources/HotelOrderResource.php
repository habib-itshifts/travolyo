<?php

namespace Modules\Hotel\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Hotel\DTOs\HotelOrderDto;

/** @mixin HotelOrderDto */
class HotelOrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'order_id'        => $this->orderId,
            'provider'        => $this->provider->value,
            'hotel_name'      => $this->hotelName,
            'room_name'       => $this->roomName,
            'check_in'        => $this->checkIn,
            'check_out'       => $this->checkOut,
            'nights'          => $this->nights,
            'adults'          => $this->adults,
            'children'        => $this->children,
            'total_price'     => $this->totalPrice,
            'currency'        => $this->currency,
            'status'          => $this->status,
            'guest'           => [
                'first_name' => $this->guestFirstName,
                'last_name'  => $this->guestLastName,
                'email'      => $this->guestEmail,
                'phone'      => $this->guestPhone,
            ],
            'special_requests'=> $this->specialRequests,
        ];
    }
}
