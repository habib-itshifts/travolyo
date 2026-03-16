<x-admin::layouts.master title="Add Blog">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}" class="text-decoration-none">Blogs</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Add New Blog</h5>
        </div>
    </div>

    @include('admin::blogs._form')
</x-admin::layouts.master>
