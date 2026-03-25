<?php

namespace Modules\Activity\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Activity\DTOs\ActivityOrderDto;

/** @mixin ActivityOrderDto */
class ActivityOrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'order_id'          => $this->orderId,
            'provider'          => $this->provider->value,
            'activity_title'    => $this->activityTitle,
            'category'          => $this->category,
            'city'              => $this->city,
            'activity_date'     => $this->activityDate,
            'participants'      => $this->participants,
            'duration'          => $this->duration,
            'unit_price'        => $this->unitPrice,
            'total_price'       => $this->totalPrice,
            'currency'          => $this->currency,
            'status'            => $this->status,
            'guest'             => [
                'first_name' => $this->guestFirstName,
                'last_name'  => $this->guestLastName,
                'email'      => $this->contactEmail,
                'phone'      => $this->contactPhone,
            ],
            'special_requests'  => $this->specialRequests,
            'passengers'        => $this->passengers,
        ];
    }
}
