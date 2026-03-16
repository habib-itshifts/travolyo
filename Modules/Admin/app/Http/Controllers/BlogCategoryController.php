<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\BlogCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlogCategoryController extends Controller
{
    protected function rules(?BlogCategory $category = null): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'slug' => ['nullable', 'string', 'max:191', Rule::unique('blog_categories', 'slug')->ignore($category?->id)],
            'content' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'publish'])],
        ];
    }

    protected function payload(array $validated, ?BlogCategory $category = null): array
    {
        $validated['slug'] = Str::slug($validated['slug'] ?: $validated['name']);
        $validated['parent_id'] = null;
        $validated['update_user'] = auth()->id();

        if (! $category) {
            $validated['create_user'] = auth()->id();
        }

        return $validated;
    }

    public function index(Request $request): View
    {
        $categories = BlogCategory::query()
            ->when($request->string('search')->toString(), fn ($query, $search) => $query->where('name', 'like', '%' . $search . '%'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin::blog-categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin::blog-categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        BlogCategory::create($this->payload($validated));

        return redirect()->route('admin.blog-categories.index')->with('success', 'Blog category created successfully.');
    }

    public function edit(BlogCategory $blog_category): View
    {
        return view('admin::blog-categories.edit', [
            'category' => $blog_category,
        ]);
    }

    public function update(Request $request, BlogCategory $blog_category): RedirectResponse
    {
        $validated = $request->validate($this->rules($blog_category));
        $blog_category->update($this->payload($validated, $blog_category));

        return redirect()->route('admin.blog-categories.index')->with('success', 'Blog category updated successfully.');
    }

    public function destroy(BlogCategory $blog_category): RedirectResponse
    {
        $blog_category->delete();

        return back()->with('success', 'Blog category deleted successfully.');
    }
}
