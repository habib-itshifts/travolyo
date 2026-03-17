<?php

namespace Modules\Activity\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Modules\Activity\Actions\SaveActivityAction;
use Modules\Activity\Models\Activity;

/**
 * ActivityService
 *
 * Central place for all Activity business logic and queries.
 * Both Admin and Vendor controllers inject this service — no
 * query or helper logic lives in the controllers themselves.
 *
 * Write operations delegate to SaveActivityAction so all the
 * slug/media/vendor-restriction rules stay in one place.
 */
class ActivityService
{
    public function __construct(private readonly SaveActivityAction $saveActivity) {}

    // -------------------------------------------------------------------------
    // Queries
    // -------------------------------------------------------------------------

    /**
     * Data bag for the admin index view.
     * Returns all activities (including vendor-submitted pending ones).
     */
    public function adminIndex(Request $request): array
    {
        $activities = Activity::query()
            ->with(['author', 'image'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->query('search'));
                $query->where(function ($q) use ($search) {
                    $q->where('title',    'like', '%'.$search.'%')
                      ->orWhere('slug',     'like', '%'.$search.'%')
                      ->orWhere('city',     'like', '%'.$search.'%')
                      ->orWhere('category', 'like', '%'.$search.'%');
                });
            })
            ->when($request->filled('status'),   fn ($q) => $q->where('status',   $request->query('status')))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->query('category')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return [
            'activities' => $activities,
            'categories' => $this->getDistinctCategories(),
        ];
    }

    /**
     * Data bag for the vendor index view.
     * Scoped to activities owned by the given vendor — vendors never see each other's.
     */
    public function vendorIndex(Request $request, int $vendorId): array
    {
        $activities = Activity::query()
            ->where('author_id', $vendorId)
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%'.$request->query('search').'%'))
            ->when($request->filled('status'),  fn ($q) => $q->where('status', $request->query('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return compact('activities');
    }

    /**
     * Data bag for the create view.
     * Pass withUsers: true for the admin form (author selector).
     */
    public function createData(bool $withUsers = false): array
    {
        $data = ['categoryOptions' => Activity::CATEGORY_OPTIONS];

        if ($withUsers) {
            $data['users'] = User::query()->orderBy('name')->get();
        }

        return $data;
    }

    /**
     * Data bag for the edit view.
     * Appends the activity's current category to the options list if it is
     * not already in the predefined set (handles legacy / custom categories).
     * Pass withUsers: true for the admin form (author selector).
     */
    public function editData(Activity $activity, bool $withUsers = false): array
    {
        $options = Activity::CATEGORY_OPTIONS;

        if ($activity->category && ! in_array($activity->category, $options, true)) {
            $options[] = $activity->category;
        }

        $data = [
            'activity'        => $activity,
            'categoryOptions' => $options,
        ];

        if ($withUsers) {
            $data['users'] = User::query()->orderBy('name')->get();
        }

        return $data;
    }

    // -------------------------------------------------------------------------
    // Writes
    // -------------------------------------------------------------------------

    /**
     * Create or update an activity.
     * Delegates to SaveActivityAction — all slug/media/vendor rules live there.
     */
    public function save(array $data, bool $isVendor, ?Activity $activity = null): Activity
    {
        return $this->saveActivity->execute($data, isVendor: $isVendor, activity: $activity);
    }

    /**
     * Soft-delete an activity and return its title for the flash message.
     */
    public function delete(Activity $activity): string
    {
        $title = $activity->title;
        $activity->delete();

        return $title;
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function getDistinctCategories(): Collection
    {
        return Activity::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }
}
