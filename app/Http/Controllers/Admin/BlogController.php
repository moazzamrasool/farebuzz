<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Support\HtmlSanitizer;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    use GeneratesUniqueSlug;

    public function index()
    {
        $blogs = Blog::latest('created_at')->paginate(15);

        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(Request $request)
    {
        $data = $this->prepareData($request);
        $data['slug'] = $this->generateUniqueSlug(Blog::class, $request->filled('slug') ? $request->slug : $request->title);

        Blog::create($data);

        return redirect()->route('crm.blogs.index')
            ->with('success', 'Blog post created successfully.');
    }

    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $this->prepareData($request, $blog);

        if ($request->filled('slug')) {
            if ($blog->slug !== $request->slug) {
                $data['slug'] = $this->generateUniqueSlug(Blog::class, $request->slug, $blog->id);
            }
        } elseif ($blog->title !== $request->title) {
            $data['slug'] = $this->generateUniqueSlug(Blog::class, $request->title, $blog->id);
        }

        $blog->update($data);

        return redirect()->route('crm.blogs.index')
            ->with('success', 'Blog post updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        $this->deleteImages($blog);
        $blog->delete();

        return redirect()->route('crm.blogs.index')
            ->with('success', 'Blog post deleted successfully.');
    }

    public function toggleStatus(Blog $blog)
    {
        $newStatus = $blog->status === 'published' ? 'draft' : 'published';

        $blog->update([
            'status' => $newStatus,
            'published_at' => $newStatus === 'published' ? ($blog->published_at ?? now()) : $blog->published_at,
        ]);

        return redirect()->route('crm.blogs.index')
            ->with('success', 'Status updated successfully.');
    }

    private function validated(Request $request, ?Blog $ignoring = null): array
    {
        return $request->validate([
            'title'             => 'required|string|max:255',
            'slug'              => 'nullable|alpha_dash|max:255|unique:blogs,slug,'.($ignoring?->id ?? 'NULL'),
            'category'          => 'required|in:'.implode(',', array_keys(Blog::CATEGORIES)),
            'author_name'       => 'nullable|string|max:255',
            'excerpt'           => 'nullable|string|max:500',
            'content'           => 'nullable|string',
            'featured_image'    => 'nullable|image|max:2048',
            'status'            => 'required|in:draft,published',
            'published_at'      => 'nullable|date',
            'is_featured'       => 'nullable|boolean',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:255',
            'meta_keywords'     => 'nullable|string|max:255',
            'og_image'          => 'nullable|image|max:2048',
            'canonical_url'     => 'nullable|url|max:255',
        ]);
    }

    private function prepareData(Request $request, ?Blog $blog = null): array
    {
        $data = $this->validated($request, $blog);

        $data['is_featured'] = $request->boolean('is_featured');

        if (!empty($data['content'])) {
            $data['content'] = HtmlSanitizer::clean($data['content']);
        }

        if ($request->hasFile('featured_image')) {
            if ($blog?->featured_image) {
                Storage::disk('public')->delete($blog->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('blogs', 'public');
        } else {
            unset($data['featured_image']);
        }

        if ($request->hasFile('og_image')) {
            if ($blog?->og_image) {
                Storage::disk('public')->delete($blog->og_image);
            }
            $data['og_image'] = $request->file('og_image')->store('blogs/og', 'public');
        } else {
            unset($data['og_image']);
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = $blog?->published_at ?? now();
        }

        return $data;
    }

    private function deleteImages(Blog $blog): void
    {
        if ($blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
        }
        if ($blog->og_image) {
            Storage::disk('public')->delete($blog->og_image);
        }
    }
}
