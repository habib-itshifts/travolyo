<?php

namespace Modules\Hotel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Hotel\Models\Hotel;

class SyncHyperguestHotelsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Allow up to 90 minutes — syncing thousands of hotels takes time
    public int $timeout = 5400;

    // Don't retry — if it fails, the next nightly run will catch up
    public int $tries = 1;

    private array $headers = [
        'Accept-Encoding' => 'gzip, deflate',
        'Accept'          => 'application/json',
        'Authorization'   => 'Bearer 720c616825804c4498f1f21a1d128d4f',
    ];

    public function handle(): void
    {
        Log::info('[HyperguestSync] Starting hotel sync...');

        // ── Step 1: Load the full hotel list from Hyperguest static API ──────────
        ini_set('memory_limit', '512M');

        $hotels = Http::withHeaders($this->headers)
            ->acceptJson()
            ->timeout(60)
            ->get('https://hg-static.hyperguest.com/hotels.json')
            ->throw()
            ->json();

        $hotels = array_slice($hotels , 0,1);    

        Log::info('[HyperguestSync] Loaded ' . count($hotels) . ' hotels from static list.');

        // ── Step 2: Process each hotel — fetch property-static and upsert into DB ─
        $synced  = 0;
        $skipped = 0;
        $failed  = 0;

        foreach ($hotels as $hotel) {
            $externalId = (string) ($hotel['hotel_id'] ?? '');

            if ($externalId === '') {
                $skipped++;
                continue;
            }

            try {
                $static = $this->fetchPropertyStatic($externalId);

                $this->upsertHotel($externalId, $hotel, $static);

                $synced++;
            } catch (\Throwable $e) {
                $failed++;
                Log::warning("[HyperguestSync] Failed hotel {$externalId}: " . $e->getMessage());
            }

            // Small pause every 50 hotels to avoid hammering the API
            if ($synced % 50 === 0) {
                usleep(500000); // 0.5s
            }
        }

        Log::info("[HyperguestSync] Done. Synced: {$synced}, Skipped: {$skipped}, Failed: {$failed}");
    }

    // ── Step 2a: Fetch address + image URLs from property-static endpoint ────────
    private function fetchPropertyStatic(string $hotelId): array
    {
        $response = Http::withHeaders($this->headers)
            ->acceptJson()
            ->timeout(10)
            ->get("https://hg-static.hyperguest.com/{$hotelId}/property-static.json");

        if (! $response->successful()) {
            return [];
        }

        $data = $response->json();

        return [
            'name'            => (string) ($data['name'] ?? ''),
            'description'     => data_get($data, 'descriptions.0.description'),
            'address'         => data_get($data, 'location.address', ''),
            'city'            => data_get($data, 'location.city.name', ''),
            'country'         => (string) ($data['location']['countryCode'] ?? ''),
            'postal_code'     => data_get($data, 'location.postcode'),
            'latitude'        => data_get($data, 'coordinates.latitude'),
            'longitude'       => data_get($data, 'coordinates.longitude'),
            'star_rating'     => isset($data['rating']) ? (int) $data['rating'] : null,
            'check_in_time'   => data_get($data, 'settings.checkIn'),
            'check_out_time'  => data_get($data, 'settings.checkOut'),
            'email'           => data_get($data, 'contact.email'),
            'phone'           => data_get($data, 'contact.phone'),
            'website'         => data_get($data, 'contact.website'),
            // Image URLs stored directly — no local download
            'featured_image_url' => data_get($data, 'images.0.uri'),
            'gallery_urls'    => collect($data['images'] ?? [])
                                    ->sortBy('priority')
                                    ->pluck('uri')
                                    ->values()
                                    ->all(),
        ];
    }

    // ── Step 2b: Upsert hotel into the DB using source + external_id as the key ──
    private function upsertHotel(string $externalId, array $staticList, array $staticDetail): void
    {
        $name = $staticDetail['name']
            ?: (string) ($staticList['name'] ?? "Hotel {$externalId}");

        $city    = strtolower(trim($staticDetail['city'] ?: (string) ($staticList['city'] ?? '')));
        $country = strtoupper(substr($staticDetail['country'] ?: (string) ($staticList['country'] ?? 'XX'), 0, 2));

        Hotel::updateOrCreate(
            // Match on source + external_id (unique pair — no duplicates)
            [
                'source'      => 'hyperguest',
                'external_id' => $externalId,
            ],
            [
                'name'                => $name,
                'slug'                => $this->uniqueSlug($name, $externalId),
                'description'         => $staticDetail['description'],
                'address'             => $staticDetail['address'],
                'city'                => $city,
                'country'             => $country,
                'postal_code'         => $staticDetail['postal_code'],
                'latitude'            => $staticDetail['latitude'],
                'longitude'           => $staticDetail['longitude'],
                'star_rating'         => $staticDetail['star_rating'],
                'check_in_time'       => $staticDetail['check_in_time'] ?? '14:00',
                'check_out_time'      => $staticDetail['check_out_time'] ?? '12:00',
                'email'               => $staticDetail['email'],
                'phone'               => $staticDetail['phone'],
                'website'             => $staticDetail['website'],
                'featured_image_url'  => $staticDetail['featured_image_url'],
                'gallery_urls'        => $staticDetail['gallery_urls'],
                // External hotels are hidden from admin CMS, active for search
                'status'              => 'active',
                'is_hidden'           => true,
                'external_synced_at'  => now(),
            ]
        );
    }

    private function uniqueSlug(string $name, string $externalId): string
    {
        $base = Str::slug($name) ?: 'hotel';
        $slug = $base . '-hg-' . $externalId;

        return $slug;
    }
}
