@extends('layouts.master')

@section('title', 'Press - Travolyo')

@section('canonical', 'https://www.travolyo.com/press')

@push('styles')
<style>
    .press-page {
        background: #f7fafc;
        color: #0f172a;
    }
    .press-hero {
        position: relative;
        overflow: hidden;
        padding: 108px 0 88px;
        background:
            linear-gradient(180deg, rgba(15, 23, 42, 0.46) 0%, rgba(15, 23, 42, 0.28) 100%),
            url('{{ asset('assets/images/website/blogs/blog.jpg') }}') center/cover no-repeat;
        color: #fff;
    }
    .press-hero .container,
    .press-shell .container {
        position: relative;
        z-index: 1;
    }
    .press-eyebrow {
        display: inline-block;
        margin-bottom: 18px;
        font-size: .82rem;
        font-weight: 700;
        letter-spacing: .2em;
        text-transform: uppercase;
        opacity: .82;
    }
    .press-title {
        margin-bottom: 18px;
        font-size: clamp(2.4rem, 5vw, 4.4rem);
        font-weight: 800;
        line-height: 1.02;
    }
    .press-subtitle {
        max-width: 760px;
        margin: 0 auto;
        font-size: 1.08rem;
        line-height: 1.8;
        opacity: .92;
    }
    .press-shell {
        margin-top: -46px;
        padding: 0 0 84px;
    }
    .press-card {
        background: #fff;
        border-radius: 30px;
        padding: 34px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.10);
    }
    .press-section + .press-section {
        margin-top: 34px;
        padding-top: 34px;
        border-top: 1px solid #e2e8f0;
    }
    .press-section h2 {
        margin-bottom: 14px;
        font-size: 1.9rem;
        font-weight: 800;
        color: #0f172a;
    }
    .press-section p {
        margin-bottom: 14px;
        color: #475569;
        font-size: 1rem;
        line-height: 1.85;
    }
    .press-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(280px, .85fr);
        gap: 30px;
        align-items: start;
    }
    .press-side-note {
        border-radius: 24px;
        padding: 26px;
        background: linear-gradient(180deg, #ecfeff 0%, #f8fafc 100%);
        border: 1px solid rgba(34, 211, 238, 0.16);
    }
    .press-side-note h3 {
        margin-bottom: 12px;
        font-size: 1.12rem;
        font-weight: 800;
        color: #0f172a;
    }
    .press-side-note p {
        margin-bottom: 0;
        font-size: .98rem;
    }
    .press-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 22px;
    }
    .press-header h2 {
        margin-bottom: 0;
    }
    .press-view-all {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 0 18px;
        border-radius: 999px;
        background: #0f172a;
        color: #fff;
        text-decoration: none;
        font-weight: 600;
    }
    .press-view-all:hover {
        color: #fff;
    }
    .press-blog-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
    }
    .press-blog-card {
        overflow: hidden;
        border-radius: 24px;
        background: #fff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }
    .press-blog-media {
        display: block;
        width: 100%;
        aspect-ratio: 16 / 10;
        background: linear-gradient(135deg, #cbd5e1 0%, #e2e8f0 100%);
    }
    .press-blog-media img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }
    .press-blog-body {
        padding: 22px;
    }
    .press-blog-meta {
        margin-bottom: 10px;
        color: #64748b;
        font-size: .84rem;
        line-height: 1.6;
    }
    .press-blog-title {
        margin-bottom: 10px;
        font-size: 1.18rem;
        font-weight: 800;
        line-height: 1.35;
    }
    .press-blog-title a,
    .press-blog-link {
        text-decoration: none;
    }
    .press-blog-title a {
        color: #0f172a;
    }
    .press-blog-excerpt {
        margin-bottom: 16px;
        color: #475569;
        font-size: .96rem;
        line-height: 1.75;
    }
    .press-blog-link {
        color: #05a8c7;
        font-weight: 700;
    }
    .press-empty {
        padding: 30px 24px;
        border-radius: 22px;
        background: #f8fafc;
        color: #64748b;
        text-align: center;
    }
    .press-contact-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        padding: 26px 28px;
        border-radius: 24px;
        background: linear-gradient(135deg, #0f172a 0%, #164e63 100%);
    }
    .press-contact-box h3 {
        margin-bottom: 8px;
        color: #fff;
        font-size: 1.28rem;
        font-weight: 800;
    }
    .press-contact-box p,
    .press-contact-box a {
        margin-bottom: 0;
        color: rgba(255, 255, 255, 0.88);
    }
    .press-contact-box .press-contact-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        padding: 0 20px;
        border-radius: 999px;
        background: #fff;
        color: #0f172a;
        text-decoration: none;
        font-weight: 700;
        white-space: nowrap;
    }
    .press-contact-box .press-contact-action:hover {
        color: #0f172a;
    }
    @media (max-width: 991.98px) {
        .press-grid,
        .press-blog-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 767.98px) {
        .press-hero {
            padding: 84px 0 74px;
        }
        .press-shell {
            margin-top: -28px;
            padding-bottom: 60px;
        }
        .press-card {
            padding: 24px;
            border-radius: 24px;
        }
        .press-header,
        .press-contact-box {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="press-page">
    <section class="press-hero">
        <div class="container text-center">
            <span class="press-eyebrow">Travolyo Press</span>
            <h1 class="press-title">Press</h1>
            <p class="press-subtitle">Updates, stories, and announcements from Travolyo in one simple press hub.</p>
        </div>
    </section>

    <section class="press-shell">
        <div class="container">
            <div class="press-card">
                <div class="press-section">
                    <div class="press-grid">
                        <div>
                            <h2>About Travolyo</h2>
                            <p>Travolyo is a travel platform built to help people discover hotels, flights, activities, and destination inspiration with a cleaner and simpler booking experience.</p>
                            <p>We focus on practical design, useful content, and a smoother planning journey so travellers can compare options faster and book with more confidence.</p>
                        </div>
                        <aside class="press-side-note">
                            <h3>Media Enquiries</h3>
                            <p>For interviews, partnership conversations, or official brand requests, reach out to our team and we will connect you with the right person.</p>
                        </aside>
                    </div>
                </div>

                <div class="press-section">
                    <div class="press-header">
                        <h2>Latest Blogs</h2>
                        <a class="press-view-all" href="{{ route('blogs.index') }}">View All</a>
                    </div>

                    @if ($latestBlogs->isNotEmpty())
                        <div class="press-blog-grid">
                            @foreach ($latestBlogs as $blog)
                                <article class="press-blog-card">
                                    <a class="press-blog-media" href="{{ route('blogs.show', $blog) }}">
                                        @if ($blog->image_url)
                                            <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}">
                                        @endif
                                    </a>
                                    <div class="press-blog-body">
                                        <div class="press-blog-meta">
                                            {{ ($blog->published_at ?? $blog->created_at)?->format('d M Y') }}
                                            @if ($blog->category)
                                                <span class="mx-1">|</span>{{ $blog->category->name }}
                                            @endif
                                            <br>
                                            By {{ $blog->author?->name ?? 'Travolyo Team' }}
                                        </div>
                                        <h3 class="press-blog-title">
                                            <a href="{{ route('blogs.show', $blog) }}">{{ $blog->title }}</a>
                                        </h3>
                                        <p class="press-blog-excerpt">
                                            {{ \Illuminate\Support\Str::limit($blog->excerpt ?: strip_tags((string) $blog->content), 120) }}
                                        </p>
                                        <a class="press-blog-link" href="{{ route('blogs.show', $blog) }}">Read More</a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="press-empty">
                            No blog posts available yet.
                        </div>
                    @endif
                </div>

                <div class="press-section">
                    <div class="press-contact-box">
                        <div>
                            <h3>Contact Support</h3>
                            <p>Need more help? Reach out at <a href="mailto:support@travolyo.com">support@travolyo.com</a> or visit our contact page.</p>
                        </div>
                        <a class="press-contact-action" href="{{ route('contact') }}">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
