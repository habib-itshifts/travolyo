<x-admin::layouts.master title="Activities">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Activity Management</h5>
            <p class="text-muted mb-0" style="font-size:13px;">Manage all activities shown on the website.</p>
        </div>
        <a href="{{ route('admin.activities.create') }}" class="btn btn-sm text-white d-inline-flex align-items-center gap-2"
           style="background:var(--clr-primary);border-radius:var(--radius-btn);">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Activity
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.activities.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control form-control-sm" placeholder="Search by name, slug, city, category...">
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="publish" {{ request('status') === 'publish' ? 'selected' : '' }}>Publish</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-dark px-3">Search</button>
                        <a href="{{ route('admin.activities.index') }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
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
                            <th class="ps-4 py-3" style="width:80px;">#</th>
                            <th class="py-3">Activity Name</th>
                            <th class="py-3">Category</th>
                            <th class="py-3">City</th>
                            <th class="py-3">Price</th>
                            <th class="py-3">Max Participants</th>
                            <th class="py-3">Author</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activities as $activity)
                            <tr>
                                <td class="ps-4 text-muted">{{ $activity->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 overflow-hidden flex-shrink-0 border bg-light" style="width:64px;height:48px;">
                                            @if ($activity->image_url)
                                                <img src="{{ $activity->image_url }}" alt="{{ $activity->title }}" class="w-100 h-100" style="object-fit:cover;">
                                            @else
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted" style="font-size:11px;">No Image</div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="mb-1 fw-semibold text-dark">{{ $activity->title }}</p>
                                            <p class="mb-0 text-muted" style="font-size:11px;">{{ \Illuminate\Support\Str::limit(strip_tags((string) $activity->description), 70) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $activity->category ?: '-' }}</td>
                                <td>{{ $activity->city ?: '-' }}</td>
                                <td>{{ $activity->price_per_person ? ($activity->currency ?: 'AED') . ' ' . number_format((float) $activity->price_per_person, 2) : '-' }}</td>
                                <td>{{ $activity->max_participants ?: '-' }}</td>
                                <td>{{ $activity->author?->name ?? '-' }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ $activity->status === 'publish' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                        {{ ucfirst($activity->status) }}
                                    </span>
                                    <div class="mt-1">
                                        <span class="badge rounded-pill {{ $activity->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                            {{ $activity->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        @if ($activity->status === 'publish' && $activity->is_active)
                                            <a href="{{ route('activities.show', $activity) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="View">View</a>
                                        @endif
                                        <a href="{{ route('admin.activities.edit', $activity) }}" class="btn btn-sm btn-outline-primary" title="Edit">Edit</a>
                                        <form method="POST" action="{{ route('admin.activities.destroy', $activity) }}"
                                              onsubmit="return confirm('Delete {{ addslashes($activity->title) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <p class="mb-0">No activities found.</p>
                                    <a href="{{ route('admin.activities.create') }}" class="btn btn-sm mt-2"
                                       style="background:var(--clr-primary);color:#fff;">Create First Activity</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($activities->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $activities->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin::layouts.master>
