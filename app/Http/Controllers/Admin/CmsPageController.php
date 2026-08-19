<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Support\HtmlSanitizer;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsPageController extends Controller
{
    use GeneratesUniqueSlug;

    // 'about-us' and 'contact-us' are served by their own dedicated controllers/routes;
    // a CMS page at either slug would be silently unreachable (the explicit route always
    // wins), so creating/renaming into them is blocked here rather than left as dead content.
    private const RESERVED_SLUGS = ['about-us', 'contact-us'];

    public function index()
    {
        $cmsPages = CmsPage::orderBy('title')->paginate(15);

        return view('admin.cms-pages.index', compact('cmsPages'));
    }

    public function create()
    {
        return view('admin.cms-pages.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['body'] = HtmlSanitizer::clean($data['body'] ?? null);
        $data['slug'] = $this->generateUniqueSlug(CmsPage::class, $request->filled('slug') ? $request->slug : $request->title);

        if (in_array($data['slug'], self::RESERVED_SLUGS, true)) {
            return back()->withInput()->withErrors([
                'slug' => 'This slug is reserved for the dedicated About Us / Contact Us pages. Please choose a different title or slug.',
            ]);
        }

        $data['robots_index'] = $request->boolean('robots_index');
        $data['robots_follow'] = $request->boolean('robots_follow');

        if ($request->hasFile('og_image')) {
            $data['og_image'] = $request->file('og_image')->store('cms-pages/og', 'public');
        }

        CmsPage::create($data);

        return redirect()->route('crm.cms-pages.index')
            ->with('success', 'Page created successfully.');
    }

    public function edit(CmsPage $cmsPage)
    {
        return view('admin.cms-pages.edit', compact('cmsPage'));
    }

    public function update(Request $request, CmsPage $cmsPage)
    {
        $data = $this->validated($request, $cmsPage);
        $data['body'] = HtmlSanitizer::clean($data['body'] ?? null);
        $data['robots_index'] = $request->boolean('robots_index');
        $data['robots_follow'] = $request->boolean('robots_follow');

        if ($request->filled('slug') && $cmsPage->slug !== $request->slug) {
            $data['slug'] = $this->generateUniqueSlug(CmsPage::class, $request->slug, $cmsPage->id);
        } elseif (!$request->filled('slug') && $cmsPage->title !== $request->title) {
            $data['slug'] = $this->generateUniqueSlug(CmsPage::class, $request->title, $cmsPage->id);
        }

        if (isset($data['slug']) && in_array($data['slug'], self::RESERVED_SLUGS, true)) {
            return back()->withInput()->withErrors([
                'slug' => 'This slug is reserved for the dedicated About Us / Contact Us pages. Please choose a different title or slug.',
            ]);
        }

        if ($request->hasFile('og_image')) {
            if ($cmsPage->og_image) {
                Storage::disk('public')->delete($cmsPage->og_image);
            }
            $data['og_image'] = $request->file('og_image')->store('cms-pages/og', 'public');
        }

        $cmsPage->update($data);

        return redirect()->route('crm.cms-pages.index')
            ->with('success', 'Page updated successfully.');
    }

    public function duplicate(CmsPage $cmsPage)
    {
        $title = $cmsPage->title.' (Copy)';

        // Excludes slug (regenerated below) and the SEO/meta fields — carrying those over
        // verbatim would leave two pages competing with the same meta_title/canonical_url
        // once this copy is edited and activated.
        $copy = $cmsPage->replicate([
            'slug', 'meta_title', 'meta_description', 'focus_keyword', 'canonical_url',
            'og_title', 'og_description',
        ]);
        $copy->title = $title;
        $copy->slug = $this->generateUniqueSlug(CmsPage::class, $title);
        $copy->status = 'inactive';
        $copy->save();

        return redirect()->route('crm.cms-pages.edit', $copy->id)
            ->with('success', 'Page duplicated. Edit the copy below, then set it active when ready.');
    }

    public function destroy(CmsPage $cmsPage)
    {
        $cmsPage->delete();

        return redirect()->route('crm.cms-pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    public function toggleStatus(CmsPage $cmsPage)
    {
        $cmsPage->update([
            'status' => $cmsPage->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('crm.cms-pages.index')
            ->with('success', 'Status updated successfully.');
    }

    private function validated(Request $request, ?CmsPage $ignoring = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|alpha_dash|max:255|unique:cms_pages,slug,'.($ignoring?->id ?? 'NULL'),
            'body' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'focus_keyword' => 'nullable|string|max:255',
            'tags' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|max:2048',
            'robots_index' => 'nullable|boolean',
            'robots_follow' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);
    }
}
