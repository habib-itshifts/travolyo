<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function press(): View
    {
        $latestBlogs = Blog::query()
            ->with(['image', 'author', 'category'])
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        return view('website.press', compact('latestBlogs'));
    }

    public function index(): View
    {
        $query = Blog::query()
            ->with(['image', 'author', 'category', 'tags'])
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($search = request()->string('s')->toString()) {
            $query->where(function ($blogQuery) use ($search) {
                $blogQuery
                    ->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%')
                    ->orWhere('excerpt', 'like', '%' . $search . '%');
            });
        }

        if ($categorySlug = request()->string('category')->toString()) {
            $category = BlogCategory::query()->where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('cat_id', $category->id);
            }
        }

        if ($tagSlug = request()->string('tag')->toString()) {
            $tag = BlogTag::query()->where('slug', $tagSlug)->first();
            if ($tag) {
                $query->whereHas('tags', function ($tagQuery) use ($tag) {
                    $tagQuery->where('blog_tags.id', $tag->id);
                });
            }
        }

        $blogs = $query->paginate(9)->withQueryString();
        $categories = BlogCategory::query()->where('status', 'publish')->orderBy('name')->get();
        $tags = BlogTag::query()->orderBy('name')->get();

        return view('website.blogs.index', compact('blogs', 'categories', 'tags'));
    }

    public function show(Blog $blog): View
    {
        abort_unless($blog->status === 'publish', 404);

        $blog->load(['image', 'author', 'category', 'tags']);

        $relatedBlogs = Blog::query()
            ->with(['image', 'category'])
            ->published()
            ->whereKeyNot($blog->id)
            ->when($blog->cat_id, fn ($query) => $query->where('cat_id', $blog->cat_id))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        $recentBlogs = Blog::query()
            ->with(['image', 'category'])
            ->published()
            ->whereKeyNot($blog->id)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $categories = BlogCategory::query()->where('status', 'publish')->orderBy('name')->get();
        $tags = BlogTag::query()->orderBy('name')->get();

        return view('website.blogs.show', compact('blog', 'relatedBlogs', 'recentBlogs', 'categories', 'tags'));
    }
}
