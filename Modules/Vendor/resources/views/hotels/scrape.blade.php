<x-vendor::layouts.master title="Scrape Hotel">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Scrape Hotel</h5>
            <p class="text-muted mb-0" style="font-size:13px;">Import your hotel listing from a public URL and save it as a draft</p>
        </div>
        <a href="{{ route('vendor.hotels.index') }}" class="btn btn-sm btn-outline-secondary">Back to Hotels</a>
    </div>

    @include('hotel::scraping.form', [
        'action' => route('vendor.hotels.scraping.store'),
        'cancelUrl' => route('vendor.hotels.index'),
        'submitLabel' => 'Scrape and Save Draft',
    ])

</x-vendor::layouts.master>
