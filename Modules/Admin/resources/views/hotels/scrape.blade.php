<x-admin::layouts.master title="Scrape Hotel">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Scrape Hotel</h5>
            <p class="text-muted mb-0" style="font-size:13px;">Import a hotel into the admin hotel module from a public URL</p>
        </div>
        <a href="{{ route('admin.hotels.index') }}" class="btn btn-sm btn-outline-secondary">Back to Hotels</a>
    </div>

    @include('hotel::scraping.form', [
        'action' => route('admin.hotels.scraping.store'),
        'cancelUrl' => route('admin.hotels.index'),
        'showStatus' => true,
        'statusOptions' => $statusOptions,
        'submitLabel' => 'Scrape and Import',
    ])

</x-admin::layouts.master>
