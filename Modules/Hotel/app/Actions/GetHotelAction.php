<?php

namespace Modules\Hotel\Actions;

use Modules\Hotel\Exceptions\HotelException;
use Modules\Hotel\Models\Hotel;

class GetHotelAction
{
    public function execute(int|string $identifier): Hotel
    {
        $query = Hotel::query()->with([
            'amenities',
            'services',
            'rooms.amenities',
        ]);

        $hotel = is_int($identifier)
            ? $query->find($identifier)
            : $query->where('slug', $identifier)->first();

        if (! $hotel) {
            throw HotelException::notFound((int) $identifier);
        }

        return $hotel;
    }
}
