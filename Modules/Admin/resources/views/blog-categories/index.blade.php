<x-admin::layouts.master title="Blog Categories">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Blog Categories</h5>
            <p class="text-muted mb-0" style="font-size:13px;">Manage categories used by blog posts.</p>
        </div>
        <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-sm text-white"
           style="background:var(--clr-primary);border-radius:var(--radius-btn);">Add Category</a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.blog-categories.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control form-control-sm" placeholder="Search category name...">
                    </div>
                    <div class="col-md-6 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-dark px-3">Filter</button>
                        <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" style="font-size:13px;">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="ps-4 py-3">#</th>
                            <th class="py-3">Name</th>
                            <th class="py-3">Slug</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td class="ps-4 text-muted">{{ $category->id }}</td>
                                <td class="fw-semibold text-dark">{{ $category->name }}</td>
                                <td class="text-muted">{{ $category->slug }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ $category->status === 'publish' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                        {{ ucfirst($category->status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('admin.blog-categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form method="POST" action="{{ route('admin.blog-categories.destroy', $category) }}"
                                              onsubmit="return confirm('Delete {{ addslashes($category->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="px-4 py-3 border-top">{{ $categories->links() }}</div>
            @endif
        </div>
    </div>
</x-admin::layouts.master>
