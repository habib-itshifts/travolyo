@php
    $isEdit = isset($blog);
    $action = $isEdit ? route('admin.blogs.update', $blog) : route('admin.blogs.store');
    $imageId = old('image_id', $blog->image_id ?? '');
    $imageUrl = null;
    $activeTagIds = collect(old('tag_ids', $selectedTagIds ?? []))
        ->map(fn ($id) => (int) $id)
        ->all();

    if ($imageId) {
        $media = \Modules\Admin\Models\MediaFile::find((int) $imageId);
        $imageUrl = $media?->url;
    }
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="blogTitle" value="{{ old('title', $blog->title ?? '') }}"
                                   class="form-control @error('title') is-invalid @enderror" placeholder="Enter blog title">
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Slug</label>
                            <input type="text" name="slug" id="blogSlug" value="{{ old('slug', $blog->slug ?? '') }}"
                                   class="form-control @error('slug') is-invalid @enderror" placeholder="auto-generated from title">
                            <div class="form-text">Leave empty to auto-generate.</div>
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Short Excerpt</label>
                            <textarea name="excerpt" rows="3" class="form-control @error('excerpt') is-invalid @enderror"
                                      placeholder="Short summary for blog cards and listing pages">{{ old('excerpt', $blog->excerpt ?? '') }}</textarea>
                            @error('excerpt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Content</label>
                            <textarea name="content" rows="16" class="form-control @error('content') is-invalid @enderror"
                                      placeholder="Write your blog content here...">{{ old('content', $blog->content ?? '') }}</textarea>
                            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                <div class="mb-4">
                    <label class="form-label fw-semibold" style="font-size:13px;">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="draft" {{ old('status', $blog->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="publish" {{ old('status', $blog->status ?? '') === 'publish' ? 'selected' : '' }}>Publish</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Author</label>
                        <select name="author_id" class="form-select @error('author_id') is-invalid @enderror">
                            <option value="">Select user</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ (string) old('author_id', $blog->author_id ?? auth()->id()) === (string) $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('author_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Category</label>
                        <div class="d-flex gap-2">
                            <select name="cat_id" class="form-select @error('cat_id') is-invalid @enderror">
                                <option value="">Please select</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ (string) old('cat_id', $blog->cat_id ?? '') === (string) $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-sm btn-outline-secondary flex-shrink-0">New</a>
                        </div>
                        @error('cat_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Tags</label>
                        <select name="tag_ids[]" class="form-select @error('tag_ids') is-invalid @enderror" multiple size="5">
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}" {{ in_array($tag->id, $activeTagIds, true) ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Hold Ctrl to select multiple existing tags.</div>
                        @error('tag_ids') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">New Tags</label>
                        <input type="text" name="tag_names" value="{{ old('tag_names') }}"
                               class="form-control @error('tag_names') is-invalid @enderror" placeholder="e.g. adventure, beach, visa tips">
                        <div class="form-text">Comma separated. New tags will be created automatically.</div>
                        @error('tag_names') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Featured Image</label>
                        <input type="hidden" name="image_id" id="featured-image-id" value="{{ $imageId }}">
                        <div class="media-picker-card" data-open-media-browser data-media-target="featured" data-media-multiple="false">
                            <div class="media-picker-preview" id="featured-image-preview">
                                @if ($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="Blog Image" class="img-fluid rounded border" style="max-height: 180px;">
                                @else
                                    <div class="media-picker-empty">
                                        <div class="media-picker-icon">&#128247;</div>
                                        <span class="btn btn-primary btn-sm">Upload image</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @error('image_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="card-footer bg-white border-top d-flex gap-2">
                    <button type="submit" class="btn btn-sm flex-fill text-white fw-semibold"
                            style="background:var(--clr-primary);border-radius:var(--radius-btn);">
                        {{ $isEdit ? 'Save Changes' : 'Create Blog' }}
                    </button>
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-sm btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>

@include('admin::media.partials.browser-modal')

@push('styles')
<style>
    .media-picker-card {
        border: 1px solid #dbe3ef;
        border-radius: 12px;
        background: #f8fafc;
        min-height: 210px;
        padding: 18px;
        cursor: pointer;
        transition: .2s ease;
    }
    .media-picker-card:hover {
        border-color: var(--clr-primary);
        box-shadow: 0 0 0 3px rgba(58, 181, 212, 0.12);
    }
    .media-picker-preview {
        min-height: 170px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .media-picker-preview img {
        max-width: 100%;
        max-height: 170px;
        object-fit: cover;
    }
    .media-picker-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        width: 100%;
        color: #94a3b8;
    }
    .media-picker-icon {
        font-size: 54px;
        line-height: 1;
    }
</style>
@endpush

@push('scripts')
<script>
    (() => {
        const titleInput = document.getElementById('blogTitle');
        const slugInput = document.getElementById('blogSlug');

        function slugify(value) {
            return String(value || '')
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        titleInput?.addEventListener('input', () => {
            if (!slugInput || slugInput.dataset.touched === '1') {
                return;
            }

            slugInput.value = slugify(titleInput.value);
        });

        slugInput?.addEventListener('input', () => {
            slugInput.dataset.touched = slugInput.value ? '1' : '0';
        });
    })();
</script>
@endpush
