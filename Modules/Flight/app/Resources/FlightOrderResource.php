<?php

namespace Modules\Flight\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Flight\DTOs\FlightOrderDto;

/** @mixin FlightOrderDto */
class FlightOrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'order_id'           => $this->orderId,
            'provider'           => $this->provider->value,
            'status'             => $this->status->value,
            'origin'             => $this->origin,
            'destination'        => $this->destination,
            'departure_at'       => $this->departureAt,
            'total_amount'       => $this->totalAmount,
            'currency'           => $this->currency,
            'booking_reference'  => $this->bookingReference,
            'ticketing_deadline' => $this->ticketingDeadline,
            'passengers'         => collect($this->passengers)->map(fn ($p) => [
                'type'       => $p->type,
                'first_name' => $p->firstName,
                'last_name'  => $p->lastName,
                'email'      => $p->email,
                'phone'      => $p->phone,
            ])->values()->toArray(),
            'segments'           => $this->segments,
        ];
    }
}