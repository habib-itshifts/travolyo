@php
    $isEdit = isset($tag);
    $action = $isEdit ? route('admin.blog-tags.update', $tag) : route('admin.blog-tags.store');
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13px;">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $tag->name ?? '') }}"
                           class="form-control @error('name') is-invalid @enderror" placeholder="Tag name">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13px;">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $tag->slug ?? '') }}"
                           class="form-control @error('slug') is-invalid @enderror" placeholder="auto-generated from name">
                    @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size:13px;">Content</label>
                    <textarea name="content" rows="4" class="form-control @error('content') is-invalid @enderror"
                              placeholder="Optional tag description">{{ old('content', $tag->content ?? '') }}</textarea>
                    @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
        <div class="card-footer bg-white border-top d-flex gap-2">
            <button type="submit" class="btn btn-sm flex-fill text-white fw-semibold"
                    style="background:var(--clr-primary);border-radius:var(--radius-btn);">
                {{ $isEdit ? 'Save Changes' : 'Create Tag' }}
            </button>
            <a href="{{ route('admin.blog-tags.index') }}" class="btn btn-sm btn-outline-secondary">Cancel</a>
        </div>
    </div>
</form>
