<?php

namespace Modules\Activity\Actions;

use Illuminate\Support\Str;
use Modules\Activity\Models\Activity;
use Modules\Admin\Models\MediaFile;

/**
 * SaveActivityAction
 *
 * Single entry-point for both creating and updating an Activity record.
 * Shared by the Admin and Vendor panels — the $isVendor flag enforces
 * role-specific restrictions at the business-logic layer rather than
 * inside individual controllers.
 *
 * Responsibilities:
 *  - Generate slug if not provided
 *  - Set author_id / create_user / update_user
 *  - Enforce vendor restrictions: status always "pending", is_active forced false
 *  - Resolve media IDs → verified DB records
 *  - Normalise extra_information array
 *  - Cast boolean fields
 */
class SaveActivityAction
{
    /**
     * Create or update an activity.
     *
     * @param  array         $data      Already-validated input from a FormRequest.
     * @param  bool          $isVendor  When true, status is forced to "pending"
     *                                  and is_active is forced to false — vendor
     *                                  submissions always require admin approval.
     * @param  Activity|null $activity  Pass an existing Activity to update; null to create.
     * @return Activity                 The persisted (created or updated) Activity instance.
     */
    public function execute(array $data, bool $isVendor, ?Activity $activity = null): Activity
    {
        $payload = $this->buildPayload($data, $isVendor, $activity);

        if ($activity) {
            $activity->update($payload);
        } else {
            $activity = Activity::create($payload);
        }

        return $activity;
    }

    // -------------------------------------------------------------------------
    // Payload preparation
    // -------------------------------------------------------------------------

    private function buildPayload(array $data, bool $isVendor, ?Activity $existing): array
    {
        // Auto-generate slug from title if the user left it blank.
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        // Stamp the creator on new records only — never change owner on update.
        if (! $existing) {
            $data['author_id']   = $data['author_id'] ?? auth()->id();
            $data['create_user'] = auth()->id();
        }

        $data['update_user'] = auth()->id();

        if ($isVendor) {
            // Vendor-submitted activities always go into the pending queue.
            // Admin must approve (set status = publish) before they go live.
            $data['status']    = 'pending';
            $data['is_active'] = false;
        } else {
            // Checkboxes do not submit a value when unchecked — cast explicitly.
            $data['is_active'] = (bool) ($data['is_active'] ?? false);
        }

        // Cast boolean fields that are not affected by the vendor restriction.
        $data['instant_confirmation'] = (bool) ($data['instant_confirmation'] ?? false);
        $data['email_flyer_enabled']  = (bool) ($data['email_flyer_enabled']  ?? false);

        // Resolve and validate image / gallery IDs against the database.
        [
            $data['image_id'],
            $data['gallery'],
        ] = $this->resolveMedia(
            $data['image_id'] ?? null,
            $data['gallery']  ?? null,
        );

        // Normalise the extra_information repeater (filter blank items).
        $data['extra_information'] = $this->normalizeExtraInformation(
            $data['extra_information'] ?? []
        );

        return $data;
    }

    // -------------------------------------------------------------------------
    // Media helpers
    // -------------------------------------------------------------------------

    /**
     * Resolve raw media IDs into verified database records.
     * Discards any IDs that do not exist in media_files.
     *
     * @return array [imageId, galleryCommaSeparated]
     */
    private function resolveMedia(mixed $imageId, ?string $gallery): array
    {
        $imageId    = (int) $imageId > 0 ? (int) $imageId : null;
        $galleryIds = $this->parseGalleryIds($gallery);

        $allIds = array_values(array_unique(array_filter(array_merge(
            $imageId ? [$imageId] : [],
            $galleryIds,
        ))));

        $mediaItems = $allIds
            ? MediaFile::query()->whereIn('id', $allIds)->get()->keyBy('id')
            : collect();

        $resolvedImage   = $mediaItems->has($imageId) ? $imageId : null;
        $resolvedGallery = array_values(array_filter($galleryIds, fn ($id) => $mediaItems->has($id)));

        return [
            $resolvedImage,
            $resolvedGallery ? implode(',', $resolvedGallery) : null,
        ];
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

    private function normalizeExtraInformation(array $items = []): array
    {
        return collect($items)
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }
}
