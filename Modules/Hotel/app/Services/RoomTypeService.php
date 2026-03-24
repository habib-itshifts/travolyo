<?php

namespace Modules\Hotel\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Modules\Hotel\Models\RoomType;

class RoomTypeService
{
    public function list(array $filters = [], ?int $userId = null): LengthAwarePaginator
    {
        return RoomType::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when(isset($filters['is_active']), fn ($q) => $q->where('is_active', $filters['is_active']))
            ->latest()
            ->paginate(20)
            ->withQueryString();
    }

    public function store(array $data): RoomType
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['bed_configuration'] = $this->normalizeBedConfig($data['bed_configuration'] ?? []);
        return RoomType::create($data);
    }

    public function update(RoomType $roomType, array $data): RoomType
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['bed_configuration'] = $this->normalizeBedConfig($data['bed_configuration'] ?? []);
        $roomType->update($data);
        return $roomType;
    }

    public function delete(RoomType $roomType): void
    {
        $roomType->delete();
    }

    protected function normalizeBedConfig(array|string $config): array
    {
        if (is_string($config)) {
            $config = json_decode($config, true) ?? [];
        }

        return collect($config)
            ->filter(fn ($qty) => (int) $qty > 0)
            ->mapWithKeys(fn ($qty, $type) => [trim($type) => (int) $qty])
            ->all();
    }
}
