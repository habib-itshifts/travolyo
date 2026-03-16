@extends('layouts.master')

@section('title', $blog->title . ' - Travolyo')

@push('styles')
<style>
    .blog-detail-hero {
        padding: 72px 0 36px;
        background: linear-gradient(180deg, #eff6ff 0%, #ffffff 100%);
    }
    .blog-detail-card {
        background: #fff;
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
    }
    .blog-detail-image {
        width: 100%;
        max-height: 460px;
        object-fit: cover;
        background: linear-gradient(135deg, #cbd5e1, #e2e8f0);
    }
    .blog-detail-content {
        color: #334155;
        font-size: 1rem;
        line-height: 1.9;
        white-space: pre-line;
    }
    .related-blog-card {
        border: 0;
        border-radius: 22px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 16px 38px rgba(15, 23, 42, 0.07);
        height: 100%;
    }
    .related-blog-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }
    .blog-meta-pill {
        display: inline-flex;
        align-items: center;
        padding: .4rem .85rem;
        border-radius: 999px;
        background: #e2e8f0;
        color: #0f172a;
        text-decoration: none;
        font-size: .85rem;
        margin: 0 .45rem .55rem 0;
    }
    .blog-sidebar {
        background: #fff;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 16px 38px rgba(15, 23, 42, 0.07);
    }
</style>
@endpush

@section('content')
<section class="blog-detail-hero">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-9">
                <a href="{{ route('blogs.index') }}" class="text-decoration-none small fw-semibold text-uppercase" style="letter-spacing:.18em;">
                    Back To Blogs
                </a>
                <h1 class="display-5 fw-bold text-dark mt-3 mb-3">{{ $blog->title }}</h1>
                <p class="text-muted mb-0">
                    Published {{ ($blog->published_at ?? $blog->created_at)?->format('d M Y') }}
                    @if ($blog->author)
                        <span class="mx-2">|</span>By {{ $blog->author->name }}
                    @endif
                </p>
            </div>
        </div>
    </div>
</section>

<section class="pb-5">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-xl-8">
                <article class="blog-detail-card">
                    @if ($blog->image_url)
                        <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="blog-detail-image">
                    @endif

                    <div class="p-4 p-md-5">
                        <div class="mb-4">
                            @if ($blog->category)
                                <a href="{{ route('blogs.index', ['category' => $blog->category->slug]) }}" class="blog-meta-pill">
                                    {{ $blog->category->name }}
                                </a>
                            @endif
                            @foreach ($blog->tags as $tag)
                                <a href="{{ route('blogs.index', ['tag' => $tag->slug]) }}" class="blog-meta-pill">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>

                        @if ($blog->excerpt)
                            <p class="lead text-dark fw-medium mb-4">{{ $blog->excerpt }}</p>
                        @endif

                        <div class="blog-detail-content">
                            {{ $blog->content }}
                        </div>
                    </div>
                </article>
            </div>

            <div class="col-xl-4">
                <div class="blog-sidebar mb-4">
                    <h2 class="h5 fw-bold mb-3">Categories</h2>
                    <div class="d-grid gap-2">
                        @foreach ($categories as $category)
                            <a href="{{ route('blogs.index', ['category' => $category->slug]) }}" class="text-decoration-none text-dark">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="blog-sidebar mb-4">
                    <h2 class="h5 fw-bold mb-3">Tags</h2>
                    <div>
                        @foreach ($tags as $tag)
                            <a href="{{ route('blogs.index', ['tag' => $tag->slug]) }}" class="blog-meta-pill">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="blog-sidebar">
                    <h2 class="h5 fw-bold mb-3">Recent Blogs</h2>
                    <div class="d-grid gap-3">
                        @foreach ($recentBlogs as $recentBlog)
                            <a href="{{ route('blogs.show', $recentBlog) }}" class="text-decoration-none text-dark">
                                <div class="fw-semibold">{{ $recentBlog->title }}</div>
                                <div class="small text-muted">
                                    {{ ($recentBlog->published_at ?? $recentBlog->created_at)?->format('d M Y') }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if ($relatedBlogs->isNotEmpty())
            <div class="row justify-content-center mt-5">
                <div class="col-xl-12">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h2 class="fw-bold mb-0">More Blogs</h2>
                        <a href="{{ route('blogs.index') }}" class="text-decoration-none fw-semibold">View All</a>
                    </div>

                    <div class="row g-4">
                        @foreach ($relatedBlogs as $relatedBlog)
                            <div class="col-md-4">
                                <article class="related-blog-card">
                                    @if ($relatedBlog->image_url)
                                        <img src="{{ $relatedBlog->image_url }}" alt="{{ $relatedBlog->title }}">
                                    @endif
                                    <div class="p-4">
                                        <div class="text-muted small mb-2">
                                            {{ ($relatedBlog->published_at ?? $relatedBlog->created_at)?->format('d M Y') }}
                                        </div>
                                        <h3 class="h6 fw-bold mb-3">{{ $relatedBlog->title }}</h3>
                                        <a href="{{ route('blogs.show', $relatedBlog) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                            Read More
                                        </a>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
