<x-admin::layouts.master title="Blog Tags">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Blog Tags</h5>
            <p class="text-muted mb-0" style="font-size:13px;">Manage tags used by blog posts.</p>
        </div>
        <a href="{{ route('admin.blog-tags.create') }}" class="btn btn-sm text-white"
           style="background:var(--clr-primary);border-radius:var(--radius-btn);">Add Tag</a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.blog-tags.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control form-control-sm" placeholder="Search tag name...">
                    </div>
                    <div class="col-md-6 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-dark px-3">Filter</button>
                        <a href="{{ route('admin.blog-tags.index') }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
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
                            <th class="py-3">Content</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tags as $tag)
                            <tr>
                                <td class="ps-4 text-muted">{{ $tag->id }}</td>
                                <td class="fw-semibold text-dark">{{ $tag->name }}</td>
                                <td class="text-muted">{{ $tag->slug }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($tag->content, 60) ?: '-' }}</td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('admin.blog-tags.edit', $tag) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form method="POST" action="{{ route('admin.blog-tags.destroy', $tag) }}"
                                              onsubmit="return confirm('Delete {{ addslashes($tag->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">No tags found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($tags->hasPages())
                <div class="px-4 py-3 border-top">{{ $tags->links() }}</div>
            @endif
        </div>
    </div>
</x-admin::layouts.master>
