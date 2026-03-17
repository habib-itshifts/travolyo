<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\BlogTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlogTagController extends Controller
{
    protected function rules(?BlogTag $tag = null): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'slug' => ['nullable', 'string', 'max:191', Rule::unique('blog_tags', 'slug')->ignore($tag?->id)],
            'content' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function payload(array $validated, ?BlogTag $tag = null): array
    {
        $validated['slug'] = Str::slug($validated['slug'] ?: $validated['name']);
        $validated['update_user'] = auth()->id();

        if (! $tag) {
            $validated['create_user'] = auth()->id();
        }

        return $validated;
    }

    public function index(Request $request): View
    {
        $tags = BlogTag::query()
            ->when($request->string('search')->toString(), fn ($query, $search) => $query->where('name', 'like', '%' . $search . '%'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin::blog-tags.index', compact('tags'));
    }

    public function create(): View
    {
        return view('admin::blog-tags.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        BlogTag::create($this->payload($validated));

        return redirect()->route('admin.blog-tags.index')->with('success', 'Blog tag created successfully.');
    }

    public function edit(BlogTag $blog_tag): View
    {
        return view('admin::blog-tags.edit', ['tag' => $blog_tag]);
    }

    public function update(Request $request, BlogTag $blog_tag): RedirectResponse
    {
        $validated = $request->validate($this->rules($blog_tag));
        $blog_tag->update($this->payload($validated, $blog_tag));

        return redirect()->route('admin.blog-tags.index')->with('success', 'Blog tag updated successfully.');
    }

    public function destroy(BlogTag $blog_tag): RedirectResponse
    {
        $blog_tag->delete();

        return back()->with('success', 'Blog tag deleted successfully.');
    }
}
