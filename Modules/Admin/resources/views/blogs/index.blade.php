<x-admin::layouts.master title="Blogs">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Blogs</h5>
            <p class="text-muted mb-0" style="font-size:13px;">Manage blog posts for the website.</p>
        </div>
        <a href="{{ route('admin.blogs.create') }}" class="btn btn-sm text-white d-inline-flex align-items-center gap-2"
           style="background:var(--clr-primary);border-radius:var(--radius-btn);">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add New Blog
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.blogs.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control form-control-sm" placeholder="Search by title, slug, excerpt...">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="publish" {{ request('status') === 'publish' ? 'selected' : '' }}>Publish</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="category" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ (string) request('category') === (string) $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-dark px-3">Filter</button>
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
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
                            <th class="ps-4 py-3" style="width:70px;">#</th>
                            <th class="py-3">Blog</th>
                            <th class="py-3">Slug</th>
                            <th class="py-3">Category / Author</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Published / Tags</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($blogs as $blog)
                            <tr>
                                <td class="ps-4 text-muted">{{ $blog->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 overflow-hidden flex-shrink-0 border bg-light"
                                             style="width:64px;height:48px;">
                                            @if ($blog->image_url)
                                                <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="w-100 h-100" style="object-fit:cover;">
                                            @else
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted" style="font-size:11px;">
                                                    No Image
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="mb-1 fw-semibold text-dark">{{ $blog->title }}</p>
                                            <p class="mb-0 text-muted" style="font-size:11px;">
                                                {{ \Illuminate\Support\Str::limit($blog->excerpt ?: strip_tags((string) $blog->content), 80) }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $blog->slug }}</td>
                                <td>
                                    <div>{{ $blog->category?->name ?? '-' }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $blog->author?->name ?? '-' }}</div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill {{ $blog->status === 'publish' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                        {{ $blog->status === 'publish' ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="text-muted">
                                    {{ $blog->published_at?->format('d M Y, h:i A') ?? '-' }}
                                    @if ($blog->tags->isNotEmpty())
                                        <div class="mt-1 d-flex flex-wrap gap-1">
                                            @foreach ($blog->tags->take(3) as $tag)
                                                <span class="badge bg-light text-dark border">{{ $tag->name }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        @if ($blog->status === 'publish')
                                            <a href="{{ route('blogs.show', $blog) }}" target="_blank"
                                               class="btn btn-sm btn-outline-secondary" title="View">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.blogs.destroy', $blog) }}"
                                              onsubmit="return confirm('Delete {{ addslashes($blog->title) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <p class="mb-0">No blogs found.</p>
                                    <a href="{{ route('admin.blogs.create') }}" class="btn btn-sm mt-2"
                                       style="background:var(--clr-primary);color:#fff;">Create First Blog</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($blogs->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $blogs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin::layouts.master>
