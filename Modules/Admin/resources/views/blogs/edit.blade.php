<x-admin::layouts.master title="Edit Blog">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}" class="text-decoration-none">Blogs</a></li>
                    <li class="breadcrumb-item active">{{ $blog->title }}</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Edit: {{ $blog->title }}</h5>
        </div>
        @if ($blog->status === 'publish')
            <a href="{{ route('blogs.show', $blog) }}" target="_blank"
               class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" style="font-size:12px;">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View Blog
            </a>
        @endif
    </div>

    @include('admin::blogs._form')
</x-admin::layouts.master>
