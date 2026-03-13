<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Models\Currency;
use Modules\Admin\Models\MediaFile;
use Modules\Hotel\Models\Amenity;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;

class HotelRoomController extends Controller
{
    protected function rules(?HotelRoom $room = null): array
    {
        return [
            'hotel_id' => ['required', 'integer', 'exists:hotels,id'],
            'name' => ['required', 'string', 'max:191'],
            'slug' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('hotel_rooms', 'slug')
                    ->ignore($room?->id)
                    ->where(fn ($query) => $query->where('hotel_id', request('hotel_id'))),
            ],
            'room_type' => ['required', 'string', 'max:50'],
            'currency' => ['required', 'string', 'size:3', Rule::in(array_keys(Currency::supported()))],
            'image_id' => ['nullable', 'integer', 'exists:media_files,id'],
            'gallery' => ['nullable', 'string'],
            'bed_configuration_text' => ['nullable', 'string', 'max:255'],
            'max_adults' => ['required', 'integer', 'min:1', 'max:20'],
            'max_children' => ['nullable', 'integer', 'min:0', 'max:20'],
            'max_occupancy' => ['required', 'integer', 'min:1', 'max:20'],
            'size_sqm' => ['nullable', 'numeric', 'min:0'],
            'floor' => ['nullable', 'string', 'max:20'],
            'view_type' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'extra_adult_price' => ['nullable', 'numeric', 'min:0'],
            'extra_child_price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'amenity_ids' => ['nullable', 'array'],
            'amenity_ids.*' => ['integer', 'exists:amenities,id'],
        ];
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

    protected function payload(Request $request, array $validated): array
    {
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $supportedCurrencies = array_keys(Currency::supported());
        $validated['currency'] = strtoupper((string) ($validated['currency'] ?? Currency::defaultCode()));
        if (!in_array($validated['currency'], $supportedCurrencies, true)) {
            $validated['currency'] = Currency::defaultCode();
        }
        $imageId = (int) ($validated['image_id'] ?? 0);
        $galleryIds = $this->parseGalleryIds($validated['gallery'] ?? null);
        $mediaItems = MediaFile::query()->whereIn('id', array_values(array_unique(array_filter(array_merge(
            $imageId ? [$imageId] : [],
            $galleryIds
        )))))->pluck('id')->all();

        $validated['image_id'] = in_array($imageId, $mediaItems, true) ? $imageId : null;
        $validated['gallery'] = collect($galleryIds)
            ->filter(fn (int $id) => in_array($id, $mediaItems, true))
            ->implode(',');
        $validated['bed_configuration'] = collect(explode(',', (string) ($validated['bed_configuration_text'] ?? '')))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
        $validated['max_children'] = $validated['max_children'] ?? 0;
        $validated['extra_adult_price'] = $validated['extra_adult_price'] ?? 0;
        $validated['extra_child_price'] = $validated['extra_child_price'] ?? 0;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        unset($validated['bed_configuration_text']);

        return $validated;
    }

    public function index(Request $request): View
    {
        $hotels = Hotel::query()->orderBy('name')->get(['id', 'name']);
        $rooms = HotelRoom::query()
            ->with(['hotel'])
            ->when($request->filled('hotel_id'), fn ($query) => $query->where('hotel_id', (int) $request->hotel_id))
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($inner) use ($request) {
                    $inner->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('room_type', 'like', '%' . $request->search . '%')
                        ->orWhere('slug', 'like', '%' . $request->search . '%');
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin::hotel-rooms.index', compact('rooms', 'hotels'));
    }

    public function create(Request $request): View
    {
        $hotels = Hotel::query()->orderBy('name')->get(['id', 'name']);
        $amenities = Amenity::forRooms()->active()->orderBy('category')->orderBy('sort_order')->get();
        $currencies = Currency::supported();
        $selectedHotelId = $request->integer('hotel_id') ?: null;
        $lockedHotelId = $selectedHotelId;

        return view('admin::hotel-rooms.create', compact('hotels', 'amenities', 'currencies', 'selectedHotelId', 'lockedHotelId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated = $this->payload($request, $validated);

        $room = HotelRoom::create($validated);
        $room->amenities()->sync($request->input('amenity_ids', []));

        return redirect()
            ->route('admin.hotel-rooms.index', ['hotel_id' => $room->hotel_id])
            ->with('success', 'Room created successfully.');
    }

    public function edit(HotelRoom $hotelRoom): View
    {
        $hotelRoom->load('amenities');
        $hotels = Hotel::query()->orderBy('name')->get(['id', 'name']);
        $amenities = Amenity::forRooms()->active()->orderBy('category')->orderBy('sort_order')->get();
        $currencies = Currency::supported();
        $selectedHotelId = $hotelRoom->hotel_id;
        $lockedHotelId = null;

        return view('admin::hotel-rooms.edit', compact('hotelRoom', 'hotels', 'amenities', 'currencies', 'selectedHotelId', 'lockedHotelId'));
    }

    public function update(Request $request, HotelRoom $hotelRoom): RedirectResponse
    {
        $validated = $request->validate($this->rules($hotelRoom));
        $validated = $this->payload($request, $validated);

        $hotelRoom->update($validated);
        $hotelRoom->amenities()->sync($request->input('amenity_ids', []));

        return redirect()
            ->route('admin.hotel-rooms.index', ['hotel_id' => $hotelRoom->hotel_id])
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(HotelRoom $hotelRoom): RedirectResponse
    {
        $hotelId = $hotelRoom->hotel_id;
        $hotelRoom->delete();

        return redirect()
            ->route('admin.hotel-rooms.index', ['hotel_id' => $hotelId])
            ->with('success', 'Room deleted successfully.');
    }
}
