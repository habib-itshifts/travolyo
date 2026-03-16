<x-admin::layouts.master title="Edit Blog Tag">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.blog-tags.index') }}" class="text-decoration-none">Blog Tags</a></li>
                    <li class="breadcrumb-item active">{{ $tag->name }}</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Edit: {{ $tag->name }}</h5>
        </div>
    </div>

    @include('admin::blog-tags._form')
</x-admin::layouts.master>
