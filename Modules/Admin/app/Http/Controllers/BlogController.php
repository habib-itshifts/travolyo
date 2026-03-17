<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Admin\Models\MediaFile;

class BlogController extends Controller
{
    protected function rules(?Blog $blog = null): array
    {
        return [
            'title' => ['required', 'string', 'max:191'],
            'slug' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('blogs', 'slug')->ignore($blog?->id),
            ],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'publish'])],
            'image_id' => ['nullable', 'integer', 'exists:media_files,id'],
            'author_id' => ['nullable', 'integer', 'exists:users,id'],
            'cat_id' => ['nullable', 'integer', 'exists:blog_categories,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:blog_tags,id'],
            'tag_names' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function payload(Request $request, array $validated, ?Blog $blog = null): array
    {
        $validated['slug'] = Str::slug($validated['slug'] ?: $validated['title']);
        $validated['image_id'] = $this->validImageId($request->input('image_id'));
        $validated['author_id'] = $request->filled('author_id') ? (int) $request->input('author_id') : auth()->id();
        $validated['cat_id'] = $request->filled('cat_id') ? (int) $request->input('cat_id') : null;
        $validated['update_user'] = auth()->id();

        if (! $blog) {
            $validated['create_user'] = auth()->id();
        }

        if ($validated['status'] === 'publish') {
            $validated['published_at'] = $blog?->published_at ?: Carbon::now();
        } else {
            $validated['published_at'] = null;
        }

        return $validated;
    }

    protected function validImageId(mixed $value): ?int
    {
        $id = (int) $value;

        if ($id < 1) {
            return null;
        }

        return MediaFile::query()->whereKey($id)->exists() ? $id : null;
    }

    public function index(Request $request): View
    {
        $blogs = Blog::query()
            ->with(['image', 'author', 'category', 'tags'])
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($blogQuery) use ($search) {
                    $blogQuery
                        ->where('title', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%')
                        ->orWhere('excerpt', 'like', '%' . $search . '%');
                });
            })
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->when($request->filled('category'), fn ($query) => $query->where('cat_id', (int) $request->input('category')))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $categories = BlogCategory::query()->where('status', 'publish')->orderBy('name')->get();

        return view('admin::blogs.index', compact('blogs', 'categories'));
    }

    public function create(): View
    {
        return view('admin::blogs.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $blog = Blog::create($this->payload($request, $validated));
        $this->syncTags($blog, $request);

        return redirect()
            ->route('admin.blogs.index')
            ->with('success', 'Blog "' . $blog->title . '" created successfully.');
    }

    public function edit(Blog $blog): View
    {
        $blog->load(['image', 'tags']);

        return view('admin::blogs.edit', array_merge(
            ['blog' => $blog],
            $this->formData($blog)
        ));
    }

    public function update(Request $request, Blog $blog): RedirectResponse
    {
        $validated = $request->validate($this->rules($blog));
        $blog->update($this->payload($request, $validated, $blog));
        $this->syncTags($blog, $request);

        return redirect()
            ->route('admin.blogs.index')
            ->with('success', 'Blog "' . $blog->title . '" updated successfully.');
    }

    public function destroy(Blog $blog): RedirectResponse
    {
        $blog->delete();

        return back()->with('success', 'Blog "' . $blog->title . '" deleted.');
    }

    protected function syncTags(Blog $blog, Request $request): void
    {
        $selectedIds = collect($request->input('tag_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->values()
            ->all();

        $newTagIds = BlogTag::saveNames((string) $request->input('tag_names', ''), auth()->id());
        $tagIds = array_values(array_unique(array_merge($selectedIds, $newTagIds)));

        $blog->tags()->sync($tagIds);
    }

    protected function formData(?Blog $blog = null): array
    {
        return [
            'users' => User::query()->orderBy('name')->get(),
            'categories' => BlogCategory::query()->orderBy('name')->get(),
            'tags' => BlogTag::query()->orderBy('name')->get(),
            'selectedTagIds' => $blog?->tags->pluck('id')->all() ?? [],
        ];
    }
}
