<x-admin::layouts.master title="Media Library">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Media Library</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Media Library</h5>
        </div>
        <button type="button" class="btn btn-primary btn-sm" data-open-media-browser data-media-target="standalone" data-media-multiple="true">
            Open Media Browser
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body">
            <p class="text-muted mb-0" style="font-size:13px;">
                Browse uploaded images, create folders, and upload new files from the media browser.
            </p>
        </div>
    </div>

    @include('admin::media.partials.browser-modal')
</x-admin::layouts.master>
