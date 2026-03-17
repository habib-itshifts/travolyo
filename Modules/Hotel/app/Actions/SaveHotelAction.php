<?php

namespace Modules\Hotel\Actions;

use Illuminate\Support\Str;
use Modules\Admin\Models\MediaFile;
use Modules\Hotel\Models\Hotel;

/**
 * SaveHotelAction
 *
 * Single entry-point for both creating and updating a Hotel record.
 * Shared by the Admin and Vendor panels — the $isVendor flag enforces
 * role-specific restrictions at the business-logic layer rather than
 * inside individual controllers.
 *
 * Responsibilities:
 *  - Generate slug if not provided
 *  - Set owner (author_id) on creation
 *  - Enforce vendor restrictions: status always "draft", featured/sort_order untouched
 *  - Resolve media IDs → verified DB records → file-path strings
 *  - Normalise JSON repeater fields (policies, nearby_places, extra_prices)
 *  - Persist Hotel and sync amenities / services pivot tables
 */
class SaveHotelAction
{
    /**
     * Create or update a hotel.
     *
     * @param  array      $data      Already-validated input from a FormRequest.
     *                               Must include all fillable hotel fields plus
     *                               optional 'amenity_ids' and 'service_ids' arrays.
     * @param  bool       $isVendor  When true, status is forced to "draft" and
     *                               is_featured / sort_order are not changed by the
     *                               request — they stay at their existing values.
     * @param  Hotel|null $hotel     Pass an existing Hotel to update; null to create.
     * @return Hotel                 The persisted (created or updated) Hotel instance.
     */
    public function execute(array $data, bool $isVendor, ?Hotel $hotel = null): Hotel
    {
        $payload = $this->buildPayload($data, $isVendor, $hotel);

        if ($hotel) {
            // UPDATE — apply the cleaned payload to the existing record.
            $hotel->update($payload);
        } else {
            // CREATE — insert a new row.
            $hotel = Hotel::create($payload);
        }

        // Sync many-to-many relationships (empty array = detach all).
        $hotel->amenities()->sync($data['amenity_ids'] ?? []);
        $hotel->services()->sync($data['service_ids']  ?? []);

        return $hotel;
    }

    // -------------------------------------------------------------------------
    // Payload preparation
    // -------------------------------------------------------------------------

    /**
     * Build the final array of column values to persist.
     *
     * Applies business rules (vendor restrictions, slug generation),
     * resolves media references, and normalises repeater JSON fields.
     */
    private function buildPayload(array $data, bool $isVendor, ?Hotel $existing): array
    {
        // Auto-generate slug from the hotel name if the user left it blank.
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        // Stamp the creator on new records only — never change owner on update.
        if (! $existing) {
            $data['author_id'] = auth()->id();
        }

        if ($isVendor) {
            // Vendors submit hotels for admin review; they can never publish
            // directly, mark as featured, or control the display sort order.
            $data['status']      = 'draft';
            $data['is_featured'] = false;
            $data['sort_order']  = $existing?->sort_order ?? 0;
        } else {
            // Checkboxes do not submit a value when unchecked, so cast to bool
            // explicitly so the column is always set correctly.
            $data['is_featured'] = (bool) ($data['is_featured'] ?? false);
        }

        // Resolve and validate every media ID against the database, then
        // compute the corresponding public URL paths stored alongside the ID.
        [
            $data['image_id'],
            $data['banner_image_id'],
            $data['gallery'],
            $data['featured_image_url'],
            $data['banner_image_url'],
            $data['gallery_urls'],
        ] = $this->resolveMedia(
            $data['image_id']        ?? null,
            $data['banner_image_id'] ?? null,
            $data['gallery']         ?? null,
            $data['featured_image_url'] ?? null,
            $data['banner_image_url'] ?? null,
            $data['gallery_urls'] ?? null,
        );

        // Convert the raw repeater arrays into clean, normalised JSON-safe arrays.
        $data['policies']      = $this->normalizeRepeater($data['policies']      ?? null, ['title', 'content']);
        $data['nearby_places'] = $this->normalizeRepeater($data['nearby_places'] ?? null, ['name', 'content', 'type'], ['value']);
        $data['extra_prices']  = $this->normalizeRepeater($data['extra_prices']  ?? null, ['name', 'type'], ['price'], ['per_person']);

        return $data;
    }

    // -------------------------------------------------------------------------
    // Media helpers
    // -------------------------------------------------------------------------

    /**
     * Resolve raw media IDs into verified database records and derive file paths.
     *
     * We look up all referenced IDs in a single query, discard any that do not
     * exist in media_files, and compute the public "uploads/..." path for each
     * valid ID. This prevents orphaned references from being saved.
     *
     * @param  mixed       $imageId       Raw featured-image ID (may be string/null).
     * @param  mixed       $bannerImageId Raw banner-image ID.
     * @param  string|null $gallery       Comma-separated string of gallery IDs.
     * @return array [imageId, bannerImageId, galleryStr, featuredUrl, bannerUrl, galleryUrls]
     */
    private function resolveMedia(
        mixed $imageId,
        mixed $bannerImageId,
        ?string $gallery,
        ?string $featuredImageUrl = null,
        ?string $bannerImageUrl = null,
        mixed $galleryUrls = null,
    ): array
    {
        // Normalise every incoming ID to a positive int or null.
        $imageId       = (int) $imageId > 0       ? (int) $imageId       : null;
        $bannerImageId = (int) $bannerImageId > 0 ? (int) $bannerImageId : null;
        $galleryIds    = $this->parseGalleryIds($gallery);
        $externalGalleryUrls = $this->normalizeGalleryUrls($galleryUrls);

        // Collect all referenced IDs so we can hit the DB in one query.
        $allIds = array_values(array_unique(array_filter(array_merge(
            $imageId       ? [$imageId]       : [],
            $bannerImageId ? [$bannerImageId] : [],
            $galleryIds,
        ))));

        // Fetch only the IDs that actually exist; key by ID for O(1) lookup.
        $mediaItems = $allIds
            ? MediaFile::query()->whereIn('id', $allIds)->get()->keyBy('id')
            : collect();

        // Helper closure: convert a media ID to its "uploads/..." public path.
        $path = fn (?int $id): ?string => ($id && $mediaItems->has($id))
            ? 'uploads/' . ltrim($mediaItems->get($id)->file_path, '/')
            : null;

        // Only keep IDs that actually exist in the database.
        $resolvedImage   = $mediaItems->has($imageId)       ? $imageId       : null;
        $resolvedBanner  = $mediaItems->has($bannerImageId) ? $bannerImageId : null;
        $resolvedGallery = array_values(array_filter($galleryIds, fn ($id) => $mediaItems->has($id)));

        return [
            $resolvedImage,                                                  // image_id (int|null)
            $resolvedBanner,                                                 // banner_image_id (int|null)
            $resolvedGallery ? implode(',', $resolvedGallery) : null,       // gallery (comma string|null)
            $path($resolvedImage) ?: $this->normalizeUrl($featuredImageUrl), // featured_image_url
            $path($resolvedBanner) ?: $this->normalizeUrl($bannerImageUrl),  // banner_image_url
            array_values(array_filter(array_map($path, $resolvedGallery))) ?: $externalGalleryUrls, // gallery_urls (array)
        ];
    }

    private function normalizeGalleryUrls(mixed $value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : explode(',', $value);
        }

        return collect(is_array($value) ? $value : [])
            ->map(fn ($url) => $this->normalizeUrl($url))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeUrl(mixed $value): ?string
    {
        $url = trim((string) $value);

        return $url !== '' && filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
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

    // -------------------------------------------------------------------------
    // Repeater normalisation
    // -------------------------------------------------------------------------

    /**
     * Clean and normalise a repeater-field array (policies, nearby_places, extra_prices).
     *
     * Each repeater row is a small associative array. We:
     *  - Trim string fields
     *  - Cast numeric fields to float (empty string → null)
     *  - Cast boolean fields
     *  - Discard rows where every field is empty/null/false (user left the row blank)
     *
     * @param  array|null $items         Raw array of repeater rows from the request.
     * @param  array      $fields        String fields to extract and trim.
     * @param  array      $numericFields Fields to cast as float.
     * @param  array      $booleanFields Fields to cast as bool.
     * @return array                     Clean, indexed array of non-empty rows.
     */
    private function normalizeRepeater(
        ?array $items,
        array $fields,
        array $numericFields = [],
        array $booleanFields = [],
    ): array {
        return collect($items ?? [])
            ->map(function ($item) use ($fields, $numericFields, $booleanFields) {
                $row = [];

                foreach ($fields as $f) {
                    $row[$f] = trim((string) ($item[$f] ?? ''));
                }
                foreach ($numericFields as $f) {
                    // Preserve null for blank numeric inputs instead of casting to 0.
                    $row[$f] = ($item[$f] ?? '') === '' ? null : (float) $item[$f];
                }
                foreach ($booleanFields as $f) {
                    $row[$f] = ! empty($item[$f]);
                }

                // If every value in this row is empty/null/false the user left
                // the repeater row blank — discard it entirely.
                $hasValue = collect($row)->contains(
                    fn ($v) => is_bool($v) ? $v : ($v !== null && $v !== '')
                );

                return $hasValue ? $row : null;
            })
            ->filter()   // Remove null (blank) rows
            ->values()   // Re-index the array
            ->all();
    }
}
