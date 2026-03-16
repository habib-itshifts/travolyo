@extends('layouts.master')

@section('title', 'Blogs - Travolyo')

@push('styles')
<style>
    .blog-hero {
        background: linear-gradient(135deg, #0f766e 0%, #164e63 100%);
        color: #fff;
        padding: 88px 0 72px;
    }
    .blog-page-section {
        padding: 72px 0;
        background: #f7fafc;
    }
    .blog-card {
        height: 100%;
        border: 0;
        border-radius: 24px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .blog-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 24px 55px rgba(15, 23, 42, 0.12);
    }
    .blog-card__image {
        width: 100%;
        height: 230px;
        object-fit: cover;
        background: linear-gradient(135deg, #cbd5e1, #e2e8f0);
    }
    .blog-card__meta {
        font-size: .82rem;
        color: #64748b;
        letter-spacing: .02em;
    }
    .blog-card__title {
        color: #0f172a;
        font-size: 1.2rem;
        font-weight: 700;
        line-height: 1.35;
    }
    .blog-card__excerpt {
        color: #475569;
        font-size: .95rem;
        line-height: 1.7;
    }
    .blog-empty {
        background: #fff;
        border-radius: 24px;
        padding: 56px 24px;
        text-align: center;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
    }
    .blog-filter-card {
        background: #fff;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
    }
    .blog-sidebar-card {
        background: #fff;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
    }
    .blog-tag-link {
        display: inline-flex;
        align-items: center;
        padding: .45rem .85rem;
        border-radius: 999px;
        background: #f1f5f9;
        color: #0f172a;
        text-decoration: none;
        font-size: .85rem;
        margin: 0 .45rem .55rem 0;
    }
</style>
@endpush

@section('content')
<section class="blog-hero">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <p class="text-uppercase fw-semibold mb-3" style="letter-spacing:.22em;font-size:.82rem;opacity:.8;">Travolyo Stories</p>
                <h1 class="display-5 fw-bold mb-3">Travel blogs, tips, and destination stories</h1>
                <p class="mb-0" style="font-size:1.05rem;opacity:.88;">
                    Fresh travel inspiration from the Travolyo team, all in one place.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="blog-page-section">
    <div class="container">
        <div class="blog-filter-card mb-4">
            <form method="GET" action="{{ route('blogs.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-5">
                        <label class="form-label small text-uppercase fw-semibold">Search</label>
                        <input type="text" name="s" value="{{ request('s') }}" class="form-control" placeholder="Search blogs...">
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label small text-uppercase fw-semibold">Category</label>
                        <select name="category" class="form-select">
                            <option value="">All categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label small text-uppercase fw-semibold">Tag</label>
                        <select name="tag" class="form-select">
                            <option value="">All tags</option>
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->slug }}" {{ request('tag') === $tag->slug ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100">Filter</button>
                        <a href="{{ route('blogs.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                @if ($blogs->count())
                    <div class="row g-4">
                        @foreach ($blogs as $blog)
                            <div class="col-md-6">
                                <article class="blog-card">
                                    @if ($blog->image_url)
                                        <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="blog-card__image">
                                    @else
                                        <div class="blog-card__image d-flex align-items-center justify-content-center text-muted">
                                            No image
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <div class="blog-card__meta mb-2">
                                            {{ ($blog->published_at ?? $blog->created_at)?->format('d M Y') }}
                                            @if ($blog->category)
                                                <span class="mx-1">|</span>{{ $blog->category->name }}
                                            @endif
                                        </div>
                                        <h2 class="blog-card__title mb-3">{{ $blog->title }}</h2>
                                        <p class="blog-card__excerpt mb-2">
                                            By {{ $blog->author?->name ?? 'Travolyo Team' }}
                                        </p>
                                        <p class="blog-card__excerpt mb-4">
                                            {{ \Illuminate\Support\Str::limit($blog->excerpt ?: strip_tags((string) $blog->content), 145) }}
                                        </p>
                                        <a href="{{ route('blogs.show', $blog) }}" class="btn btn-dark rounded-pill px-4">
                                            Read More
                                        </a>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5">
                        {{ $blogs->links() }}
                    </div>
                @else
                    <div class="blog-empty">
                        <h2 class="fw-bold mb-2">No blogs published yet</h2>
                        <p class="text-muted mb-0">Once you publish blogs from the admin panel, they will appear here.</p>
                    </div>
                @endif
            </div>

            <div class="col-xl-4">
                <div class="blog-sidebar-card mb-4">
                    <h3 class="h5 fw-bold mb-3">Categories</h3>
                    <div class="d-grid gap-2">
                        @foreach ($categories as $category)
                            <a href="{{ route('blogs.index', ['category' => $category->slug]) }}" class="text-decoration-none text-dark">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="blog-sidebar-card">
                    <h3 class="h5 fw-bold mb-3">Tags</h3>
                    <div>
                        @foreach ($tags as $tag)
                            <a href="{{ route('blogs.index', ['tag' => $tag->slug]) }}" class="blog-tag-link">{{ $tag->name }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
