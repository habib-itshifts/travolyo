<?php

namespace Modules\Space\Actions;

use Illuminate\Support\Str;
use Modules\Admin\Models\MediaFile;
use Modules\Space\Models\Space;

/**
 * SaveSpaceAction
 *
 * Single entry-point for both creating and updating a Space listing.
 * Shared by the Admin and Vendor panels — the $isVendor flag enforces
 * role-specific restrictions at the business-logic layer.
 */
class SaveSpaceAction
{
    public function execute(array $data, bool $isVendor, ?Space $space = null): Space
    {
        $payload = $this->buildPayload($data, $isVendor, $space);

        if ($space) {
            $space->update($payload);
        } else {
            $space = Space::create($payload);
        }

        $space->amenities()->sync($data['amenity_ids'] ?? []);

        return $space;
    }

    private function buildPayload(array $data, bool $isVendor, ?Space $existing): array
    {
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        if (! $existing) {
            $data['author_id'] = auth()->id();
        }

        if ($isVendor) {
            $data['status']      = 'draft';
            $data['is_featured'] = false;
            $data['sort_order']  = $existing?->sort_order ?? 0;
        } else {
            $data['is_featured'] = (bool) ($data['is_featured'] ?? false);
        }

        [
            $data['image_id'],
            $data['banner_image_id'],
            $data['gallery'],
            $data['featured_image_url'],
            $data['banner_image_url'],
            $data['gallery_urls'],
        ] = $this->resolveMedia(
            $data['image_id']           ?? null,
            $data['banner_image_id']    ?? null,
            $data['gallery']            ?? null,
            $data['featured_image_url'] ?? null,
            $data['banner_image_url']   ?? null,
            $data['gallery_urls']       ?? null,
        );

        $data['house_rules']  = $this->normalizeRepeater($data['house_rules'] ?? null, ['title', 'content']);
        $data['extra_prices'] = $this->normalizeRepeater($data['extra_prices'] ?? null, ['name', 'type'], ['price'], ['per_person']);

        return $data;
    }

    // -------------------------------------------------------------------------
    // Media helpers (same pattern as SaveHotelAction)
    // -------------------------------------------------------------------------

    private function resolveMedia(
        mixed $imageId,
        mixed $bannerImageId,
        ?string $gallery,
        ?string $featuredImageUrl = null,
        ?string $bannerImageUrl = null,
        mixed $galleryUrls = null,
    ): array {
        $imageId       = (int) $imageId > 0       ? (int) $imageId       : null;
        $bannerImageId = (int) $bannerImageId > 0 ? (int) $bannerImageId : null;
        $galleryIds    = $this->parseGalleryIds($gallery);

        $allIds = array_values(array_unique(array_filter(array_merge(
            $imageId       ? [$imageId]       : [],
            $bannerImageId ? [$bannerImageId] : [],
            $galleryIds,
        ))));

        $mediaItems = $allIds
            ? MediaFile::query()->whereIn('id', $allIds)->get()->keyBy('id')
            : collect();

        $path = fn (?int $id): ?string => ($id && $mediaItems->has($id))
            ? 'uploads/' . ltrim($mediaItems->get($id)->file_path, '/')
            : null;

        $resolvedImage   = $mediaItems->has($imageId)       ? $imageId       : null;
        $resolvedBanner  = $mediaItems->has($bannerImageId) ? $bannerImageId : null;
        $resolvedGallery = array_values(array_filter($galleryIds, fn ($id) => $mediaItems->has($id)));

        return [
            $resolvedImage,
            $resolvedBanner,
            $resolvedGallery ? implode(',', $resolvedGallery) : null,
            $path($resolvedImage) ?: $this->normalizeUrl($featuredImageUrl),
            $path($resolvedBanner) ?: $this->normalizeUrl($bannerImageUrl),
            array_values(array_filter(array_map($path, $resolvedGallery))),
        ];
    }

    private function normalizeUrl(mixed $value): ?string
    {
        $url = trim((string) $value);

        return $url !== '' && filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
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

    // -------------------------------------------------------------------------
    // Repeater normalisation
    // -------------------------------------------------------------------------

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
                    $row[$f] = ($item[$f] ?? '') === '' ? null : (float) $item[$f];
                }
                foreach ($booleanFields as $f) {
                    $row[$f] = ! empty($item[$f]);
                }

                $hasValue = collect($row)->contains(
                    fn ($v) => is_bool($v) ? $v : ($v !== null && $v !== '')
                );

                return $hasValue ? $row : null;
            })
            ->filter()
            ->values()
            ->all();
    }
}
