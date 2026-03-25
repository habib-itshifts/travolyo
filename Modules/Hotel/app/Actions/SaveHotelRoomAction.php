<?php

namespace Modules\Hotel\Actions;

use Modules\Admin\Models\MediaFile;
use Modules\Hotel\Models\HotelRoom;

/**
 * SaveHotelRoomAction
 *
 * Single entry-point for both creating and updating a HotelRoom record.
 * Shared by the Admin and Vendor panels.
 *
 * With the new RoomType-based structure, hotel_rooms is simplified:
 * hotel_id, room_type_id, image_id, gallery, floor, quantity, base_price, is_active, sort_order
 */
class SaveHotelRoomAction
{
    public function execute(array $data, ?HotelRoom $room = null): HotelRoom
    {
        $payload = $this->buildPayload($data);

        if ($room) {
            $room->update($payload);
        } else {
            $room = HotelRoom::create($payload);
        }

        $room->amenities()->sync($data['amenity_ids'] ?? []);

        return $room;
    }

    private function buildPayload(array $data): array
    {
        [$data['image_id'], $data['gallery']] = $this->resolveMedia(
            $data['image_id'] ?? null,
            $data['gallery']  ?? null,
        );

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active']  = (bool) ($data['is_active'] ?? false);

        // Only keep columns that exist on hotel_rooms
        return array_intersect_key($data, array_flip([
            'hotel_id', 'room_type_id', 'image_id', 'gallery',
            'floor', 'quantity', 'price_sgl_bb', 'price_dbl_bb',
            'extra_bed_price', 'child_price', 'child_breakfast',
            'is_active', 'sort_order',
        ]));
    }

    private function resolveMedia(mixed $imageId, ?string $gallery): array
    {
        $imageId    = (int) $imageId > 0 ? (int) $imageId : null;
        $galleryIds = $this->parseGalleryIds($gallery);

        $allIds = array_values(array_unique(array_filter(array_merge(
            $imageId ? [$imageId] : [],
            $galleryIds,
        ))));

        $existingIds = $allIds
            ? MediaFile::query()->whereIn('id', $allIds)->pluck('id')->all()
            : [];

        $resolvedImageId = in_array($imageId, $existingIds, true) ? $imageId : null;

        $resolvedGallery = collect($galleryIds)
            ->filter(fn (int $id) => in_array($id, $existingIds, true))
            ->implode(',');

        return [$resolvedImageId, $resolvedGallery];
    }

    private function parseGalleryIds(?string $value): array
    {
        return collect(explode(',', (string) $value))
            ->map(fn ($id) => (int) trim($id))
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }
}
