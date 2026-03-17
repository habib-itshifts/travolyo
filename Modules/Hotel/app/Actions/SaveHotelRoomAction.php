<?php

namespace Modules\Hotel\Actions;

use App\Models\Currency;
use Illuminate\Support\Str;
use Modules\Admin\Models\MediaFile;
use Modules\Hotel\Models\HotelRoom;

/**
 * SaveHotelRoomAction
 *
 * Single entry-point for both creating and updating a HotelRoom record.
 * Shared by the Admin and Vendor panels.
 *
 * Responsibilities:
 *  - Auto-generate slug from room name if not provided
 *  - Validate and normalise the currency code
 *  - Resolve media IDs (featured image, gallery) against the database
 *  - Convert the bed configuration from a comma-separated text string
 *    into a JSON-castable array
 *  - Apply sensible numeric defaults (extra prices, sort order)
 *  - Persist HotelRoom and sync the amenities pivot table
 */
class SaveHotelRoomAction
{
    /**
     * Create or update a hotel room.
     *
     * @param  array          $data  Already-validated input from a FormRequest.
     *                               Must include all fillable HotelRoom fields plus
     *                               optional 'amenity_ids' array and
     *                               'bed_configuration_text' (comma-separated string).
     * @param  HotelRoom|null $room  Existing room to update; null to create.
     * @return HotelRoom             The persisted (created or updated) HotelRoom instance.
     */
    public function execute(array $data, ?HotelRoom $room = null): HotelRoom
    {
        $payload = $this->buildPayload($data, $room);

        if ($room) {
            // UPDATE — apply payload to the existing record.
            $room->update($payload);
        } else {
            // CREATE — insert a new row.
            $room = HotelRoom::create($payload);
        }

        // Sync the many-to-many amenities relationship (empty array = detach all).
        $room->amenities()->sync($data['amenity_ids'] ?? []);

        return $room;
    }

    // -------------------------------------------------------------------------
    // Payload preparation
    // -------------------------------------------------------------------------

    /**
     * Build the final array of column values to persist.
     *
     * Handles slug generation, currency validation, media resolution,
     * bed-configuration parsing, and numeric defaults.
     */
    private function buildPayload(array $data, ?HotelRoom $existing): array
    {
        // Auto-generate slug from room name if the user left it blank.
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        // Normalise the currency code to uppercase and fall back to the system
        // default if the submitted code is not in the supported list.
        $supportedCurrencies = array_keys(Currency::supported());
        $data['currency']    = strtoupper((string) ($data['currency'] ?? Currency::defaultCode()));
        if (! in_array($data['currency'], $supportedCurrencies, true)) {
            $data['currency'] = Currency::defaultCode();
        }

        // Resolve media IDs against media_files — only keep IDs that exist.
        [$data['image_id'], $data['gallery']] = $this->resolveMedia(
            $data['image_id'] ?? null,
            $data['gallery']  ?? null,
        );

        // The form sends bed types as a single comma-separated text field
        // (e.g. "king bed, sofa bed"). Convert it to a JSON array for storage.
        $data['bed_configuration'] = collect(explode(',', (string) ($data['bed_configuration_text'] ?? '')))
            ->map(fn ($item) => trim($item))
            ->filter()          // Drop empty segments (double commas, trailing comma, etc.)
            ->values()
            ->all();

        // Remove the raw text field — it is not a database column.
        unset($data['bed_configuration_text']);

        // Apply defaults for optional numeric / boolean fields so no NULL slips
        // into columns that have a sensible zero default.
        $data['max_children']      = $data['max_children']      ?? 0;
        $data['extra_adult_price'] = $data['extra_adult_price'] ?? 0;
        $data['extra_child_price'] = $data['extra_child_price'] ?? 0;
        $data['sort_order']        = $data['sort_order']        ?? 0;

        // Checkbox: not submitted when unchecked, so cast explicitly to bool.
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        return $data;
    }

    // -------------------------------------------------------------------------
    // Media helpers
    // -------------------------------------------------------------------------

    /**
     * Resolve a featured-image ID and a gallery (comma-separated IDs) against
     * the media_files database table.
     *
     * Any ID that does not exist in the table is silently discarded.
     *
     * @param  mixed       $imageId  Raw featured-image ID (may be string/null).
     * @param  string|null $gallery  Comma-separated string of gallery media IDs.
     * @return array [imageId (int|null), gallery (comma-string)]
     */
    private function resolveMedia(mixed $imageId, ?string $gallery): array
    {
        $imageId    = (int) $imageId > 0 ? (int) $imageId : null;
        $galleryIds = $this->parseGalleryIds($gallery);

        // Gather every referenced ID so we can validate them in one query.
        $allIds = array_values(array_unique(array_filter(array_merge(
            $imageId ? [$imageId] : [],
            $galleryIds,
        ))));

        // Fetch only the IDs that exist in the database.
        $existingIds = $allIds
            ? MediaFile::query()->whereIn('id', $allIds)->pluck('id')->all()
            : [];

        $resolvedImageId = in_array($imageId, $existingIds, true) ? $imageId : null;

        // Keep only gallery IDs that exist; rebuild the comma-separated string.
        $resolvedGallery = collect($galleryIds)
            ->filter(fn (int $id) => in_array($id, $existingIds, true))
            ->implode(',');

        return [$resolvedImageId, $resolvedGallery];
    }

    /**
     * Parse a comma-separated string of gallery IDs into a clean array of
     * positive integers, de-duplicated and re-indexed.
     */
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
