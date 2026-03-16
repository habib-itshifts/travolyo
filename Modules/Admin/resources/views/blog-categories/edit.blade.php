<x-admin::layouts.master title="Edit Blog Category">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.blog-categories.index') }}" class="text-decoration-none">Blog Categories</a></li>
                    <li class="breadcrumb-item active">{{ $category->name }}</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Edit: {{ $category->name }}</h5>
        </div>
    </div>

    @include('admin::blog-categories._form')
</x-admin::layouts.master>
