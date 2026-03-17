<?php

namespace Modules\Activity\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Activity\Models\Activity;
use Modules\Admin\Models\MediaFile;

class ActivityController extends Controller
{
    protected function rules(?Activity $activity = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:activities,slug'.($activity ? ','.$activity->id : '')],
            'category' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'price_per_person' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
            'duration' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'extra_information' => ['nullable', 'array'],
            'extra_information.*' => ['nullable', 'string', 'max:255'],
            'gallery' => ['nullable', 'string'],
            'image_id' => ['nullable', 'integer', 'exists:media_files,id'],
            'author_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['required', 'in:publish,draft'],
        ];
    }

    protected function normalizeMediaId(mixed $value): ?int
    {
        $id = (int) $value;

        return $id > 0 ? $id : null;
    }

    protected function parseGalleryIds(?string $value): array
    {
        return collect(explode(',', (string) $value))
            ->map(fn ($id) => (int) trim($id))
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    protected function normalizeExtraInformation(array $items = []): array
    {
        return collect($items)
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }

    protected function preparePayload(Request $request, array $validated, ?Activity $activity = null): array
    {
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

        $imageId = $this->normalizeMediaId($request->input('image_id', $activity?->image_id));
        $galleryIds = $this->parseGalleryIds($request->input('gallery', $activity?->gallery));
        $selectedIds = array_values(array_unique(array_filter(array_merge(
            $imageId ? [$imageId] : [],
            $galleryIds
        ))));
        $mediaItems = MediaFile::query()->whereIn('id', $selectedIds)->get()->keyBy('id');

        $validated['image_id'] = $mediaItems->has($imageId) ? $imageId : null;
        $validated['gallery'] = collect($galleryIds)
            ->filter(fn (int $id) => $mediaItems->has($id))
            ->implode(',');
        $validated['instant_confirmation'] = $request->boolean('instant_confirmation');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['email_flyer_enabled'] = $request->boolean('email_flyer_enabled');
        $validated['extra_information'] = $this->normalizeExtraInformation($request->input('extra_information', []));
        $validated['author_id'] = $validated['author_id'] ?? auth()->id();
        $validated['update_user'] = auth()->id();

        if (! $activity) {
            $validated['create_user'] = auth()->id();
        }

        return $validated;
    }

    public function index(Request $request): View
    {
        $activities = Activity::query()
            ->with(['author', 'image'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->query('search'));
                $query->where(function ($activityQuery) use ($search) {
                    $activityQuery
                        ->where('title', 'like', '%'.$search.'%')
                        ->orWhere('slug', 'like', '%'.$search.'%')
                        ->orWhere('city', 'like', '%'.$search.'%')
                        ->orWhere('category', 'like', '%'.$search.'%');
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->query('category')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $categories = Activity::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin::activities.index', [
            'activities' => $activities,
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('admin::activities.create', [
            'users' => User::query()->orderBy('name')->get(),
            'categoryOptions' => Activity::CATEGORY_OPTIONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated = $this->preparePayload($request, $validated);

        $activity = Activity::create($validated);

        return redirect()->route('admin.activities.index')
            ->with('success', 'Activity "'.$activity->title.'" created successfully.');
    }

    public function edit(Activity $activity): View
    {
        $categoryOptions = Activity::CATEGORY_OPTIONS;
        if ($activity->category && ! in_array($activity->category, $categoryOptions, true)) {
            $categoryOptions[] = $activity->category;
        }

        return view('admin::activities.edit', [
            'activity' => $activity,
            'users' => User::query()->orderBy('name')->get(),
            'categoryOptions' => $categoryOptions,
        ]);
    }

    public function update(Request $request, Activity $activity): RedirectResponse
    {
        $validated = $request->validate($this->rules($activity));
        $validated = $this->preparePayload($request, $validated, $activity);

        $activity->update($validated);

        return redirect()->route('admin.activities.index')
            ->with('success', 'Activity "'.$activity->title.'" updated successfully.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $title = $activity->title;
        $activity->delete();

        return back()->with('success', 'Activity "'.$title.'" deleted.');
    }
}
