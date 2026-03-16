<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Admin\Models\MediaFile;
use Modules\Hotel\Models\Amenity;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\Service;

class HotelController extends Controller
{
    protected function hotelValidationRules(?int $hotelId = null): array
    {
        return [
            'name'              => ['required', 'string', 'max:191'],
            'slug'              => ['nullable', 'string', 'max:191', 'unique:hotels,slug' . ($hotelId ? ',' . $hotelId : '')],
            'star_rating'       => ['nullable', 'integer', 'between:1,5'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description'       => ['nullable', 'string'],
            'image_id'          => ['nullable', 'integer', 'exists:media_files,id'],
            'banner_image_id'   => ['nullable', 'integer', 'exists:media_files,id'],
            'gallery'           => ['nullable', 'string'],
            'video_url'         => ['nullable', 'url', 'max:2048'],
            'address'           => ['required', 'string', 'max:255'],
            'city'              => ['required', 'string', 'max:100'],
            'state'             => ['nullable', 'string', 'max:100'],
            'country'           => ['required', 'string', 'size:2'],
            'postal_code'       => ['nullable', 'string', 'max:20'],
            'latitude'          => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'         => ['nullable', 'numeric', 'between:-180,180'],
            'email'             => ['nullable', 'email', 'max:191'],
            'phone'             => ['nullable', 'string', 'max:50'],
            'website'           => ['nullable', 'url', 'max:255'],
            'check_in_time'     => ['nullable', 'string', 'max:5'],
            'check_out_time'    => ['nullable', 'string', 'max:5'],
            'base_price'        => ['nullable', 'numeric', 'min:0'],
            'sale_price'        => ['nullable', 'numeric', 'min:0'],
            'min_day_before_booking' => ['nullable', 'integer', 'min:0'],
            'min_day_stays'     => ['nullable', 'integer', 'min:0'],
            'related_hotel_ids' => ['nullable', 'string', 'max:255'],
            'payment_methods'   => ['nullable', 'array'],
            'languages_spoken'  => ['nullable', 'array'],
            'policies'          => ['nullable', 'array'],
            'policies.*.title'  => ['nullable', 'string', 'max:150'],
            'policies.*.content'=> ['nullable', 'string', 'max:1000'],
            'nearby_places'           => ['nullable', 'array'],
            'nearby_places.*.name'    => ['nullable', 'string', 'max:150'],
            'nearby_places.*.content' => ['nullable', 'string', 'max:500'],
            'nearby_places.*.value'   => ['nullable', 'numeric', 'min:0'],
            'nearby_places.*.type'    => ['nullable', 'in:m,km'],
            'extra_prices'              => ['nullable', 'array'],
            'extra_prices.*.name'       => ['nullable', 'string', 'max:150'],
            'extra_prices.*.price'      => ['nullable', 'numeric', 'min:0'],
            'extra_prices.*.type'       => ['nullable', 'in:one_time,per_day'],
            'extra_prices.*.per_person' => ['nullable'],
            'status'            => ['required', 'in:draft,active,inactive,suspended'],
            'is_featured'       => ['nullable', 'boolean'],
            'sort_order'        => ['nullable', 'integer', 'min:0'],
            'amenity_ids'       => ['nullable', 'array'],
            'amenity_ids.*'     => ['integer', 'exists:amenities,id'],
            'service_ids'       => ['nullable', 'array'],
            'service_ids.*'     => ['integer', 'exists:services,id'],
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
            ->filter()
            ->values()
            ->all();
    }

    protected function mediaPath(?MediaFile $media): ?string
    {
        return $media ? 'uploads/' . ltrim($media->file_path, '/') : null;
    }

    protected function normalizeRepeater(?array $items, array $fields, array $numericFields = [], array $booleanFields = []): array
    {
        $items = $items ?? [];

        return collect($items)
            ->map(function ($item) use ($fields, $numericFields, $booleanFields) {
                $row = [];
                foreach ($fields as $field) {
                    $row[$field] = trim((string) ($item[$field] ?? ''));
                }
                foreach ($numericFields as $field) {
                    $row[$field] = ($item[$field] ?? '') === '' ? null : (float) $item[$field];
                }
                foreach ($booleanFields as $field) {
                    $row[$field] = ! empty($item[$field]);
                }

                $hasValue = collect($row)->contains(function ($value) {
                    if (is_bool($value)) {
                        return $value;
                    }

                    return $value !== null && $value !== '';
                });

                return $hasValue ? $row : null;
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function prepareHotelPayload(Request $request, array $validated, ?Hotel $hotel = null): array
    {
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $validated['is_featured'] = $request->boolean('is_featured');

        if (! $hotel) {
            $validated['user_id'] = auth()->id();
        }

        $imageId = $this->normalizeMediaId($request->input('image_id', $hotel?->image_id));
        $bannerImageId = $this->normalizeMediaId($request->input('banner_image_id', $hotel?->banner_image_id));
        $galleryIds = $this->parseGalleryIds($request->input('gallery', $hotel?->gallery));

        $selectedIds = array_values(array_unique(array_filter(array_merge(
            $imageId ? [$imageId] : [],
            $bannerImageId ? [$bannerImageId] : [],
            $galleryIds
        ))));
        $mediaItems = MediaFile::query()
            ->whereIn('id', $selectedIds)
            ->get()
            ->keyBy('id');

        $galleryIds = array_values(array_filter($galleryIds, fn (int $id) => $mediaItems->has($id)));
        $validated['image_id'] = $mediaItems->has($imageId) ? $imageId : null;
        $validated['banner_image_id'] = $mediaItems->has($bannerImageId) ? $bannerImageId : null;
        $validated['gallery'] = $galleryIds ? implode(',', $galleryIds) : null;
        $validated['featured_image_url'] = $this->mediaPath($mediaItems->get($validated['image_id']));
        $validated['banner_image_url'] = $this->mediaPath($mediaItems->get($validated['banner_image_id']));
        $validated['gallery_urls'] = array_values(array_filter(array_map(
            fn (int $id) => $this->mediaPath($mediaItems->get($id)),
            $galleryIds
        )));
        $validated['policies'] = $this->normalizeRepeater($request->input('policies'), ['title', 'content']);
        $validated['nearby_places'] = $this->normalizeRepeater($request->input('nearby_places'), ['name', 'content', 'type'], ['value']);
        $validated['extra_prices'] = $this->normalizeRepeater($request->input('extra_prices'), ['name', 'type'], ['price'], ['per_person']);

        return $validated;
    }

    public function index(Request $request): View
    {
        $hotels = Hotel::withTrashed()
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->city, fn ($q) => $q->where('city', 'like', '%' . $request->city . '%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin::hotels.index', compact('hotels'));
    }

    public function create(): View
    {
        $amenities = Amenity::active()->orderBy('category')->orderBy('sort_order')->get();
        $services  = Service::active()->orderBy('category')->orderBy('sort_order')->get();

        return view('admin::hotels.create', compact('amenities', 'services'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->hotelValidationRules());
        $validated = $this->prepareHotelPayload($request, $validated);

        $hotel = Hotel::create($validated);

        if (! empty($validated['amenity_ids'])) {
            $hotel->amenities()->sync($validated['amenity_ids']);
        }

        if (! empty($validated['service_ids'])) {
            $hotel->services()->sync($validated['service_ids']);
        }

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel "' . $hotel->name . '" created successfully.');
    }

    public function show(int $id): View
    {
        $hotel = Hotel::with(['amenities', 'services', 'rooms', 'deals'])->findOrFail($id);

        return view('admin::hotels.show', compact('hotel'));
    }

    public function edit(int $id): View
    {
        $hotel     = Hotel::with(['amenities', 'services'])->findOrFail($id);
        $amenities = Amenity::active()->orderBy('category')->orderBy('sort_order')->get();
        $services  = Service::active()->orderBy('category')->orderBy('sort_order')->get();

        return view('admin::hotels.edit', compact('hotel', 'amenities', 'services'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $hotel = Hotel::findOrFail($id);

        $validated = $request->validate($this->hotelValidationRules($id));
        $validated = $this->prepareHotelPayload($request, $validated, $hotel);

        $hotel->update($validated);
        $hotel->amenities()->sync($validated['amenity_ids'] ?? []);
        $hotel->services()->sync($validated['service_ids'] ?? []);

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel "' . $hotel->name . '" updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->delete();

        return back()->with('success', 'Hotel "' . $hotel->name . '" deleted.');
    }

    public function restore(int $id): RedirectResponse
    {
        Hotel::withTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Hotel restored successfully.');
    }
}
