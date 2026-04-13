<?php

namespace Modules\Space\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Space\DTOs\SpaceOrderDto;

/** @mixin SpaceOrderDto */
class SpaceOrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'order_id'        => $this->orderId,
            'space_name'      => $this->spaceName,
            'space_type'      => $this->spaceType,
            'city'            => $this->city,
            'country'         => $this->country,
            'address'         => $this->address,
            'check_in'        => $this->checkIn,
            'check_out'       => $this->checkOut,
            'nights'          => $this->nights,
            'guests'          => $this->guests,
            'price_per_night' => $this->pricePerNight,
            'cleaning_fee'    => $this->cleaningFee,
            'service_fee'     => $this->serviceFee,
            'total_price'     => $this->totalPrice,
            'currency'        => $this->currency,
            'status'          => $this->status,
            'guest_name'      => $this->guestName,
            'guest_email'     => $this->guestEmail,
            'special_requests' => $this->specialRequests,
            'images'          => $this->images,
        ];
    }
}
