<?php

namespace Modules\Space\Actions;

use App\Models\Booking;
use Modules\Space\DTOs\SpaceOrderDto;
use Modules\Space\Models\Space;

class GetSpaceOrderAction
{
    public function handle(string $bookingCode): SpaceOrderDto
    {
        $booking = Booking::where('code', $bookingCode)
            ->where('object_model', 'space')
            ->firstOrFail();

        $spaceDetails = $booking->getMeta('space_details') ?? [];
        $space = Space::find($booking->object_id);

        $images = [];
        if ($space) {
            if ($space->featured_image_url) {
                $images[] = $space->featured_image_url;
            }
            $images = array_merge($images, $space->gallery_urls ?? []);
        }

        return new SpaceOrderDto(
            orderId:         $booking->code,
            spaceName:       $spaceDetails['space_name'] ?? ($space->name ?? 'N/A'),
            spaceType:       $spaceDetails['space_type'] ?? ($space->type ?? 'apartment'),
            city:            $spaceDetails['city'] ?? ($space->city ?? ''),
            country:         $spaceDetails['country'] ?? ($space->country ?? ''),
            address:         $spaceDetails['address'] ?? ($space->address ?? null),
            checkIn:         $booking->start_date->toDateString(),
            checkOut:        $booking->end_date->toDateString(),
            nights:          (int) $booking->start_date->diffInDays($booking->end_date),
            guests:          $booking->total_guests ?? 1,
            pricePerNight:   (float) ($spaceDetails['price_per_night'] ?? 0),
            cleaningFee:     (float) ($spaceDetails['cleaning_fee'] ?? 0),
            serviceFee:      (float) ($spaceDetails['service_fee'] ?? 0),
            totalPrice:      (float) $booking->total,
            currency:        $booking->currency ?? 'USD',
            status:          $booking->status,
            guestName:       trim(($booking->first_name ?? '') . ' ' . ($booking->last_name ?? '')),
            guestEmail:      $booking->email,
            specialRequests: $booking->customer_notes,
            images:          $images,
        );
    }
}
