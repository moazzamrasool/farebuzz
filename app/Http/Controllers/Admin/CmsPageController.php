<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Http\Request;

class CmsPageController extends Controller
{
    use GeneratesUniqueSlug;

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
        $data['slug'] = $this->generateUniqueSlug(CmsPage::class, $request->filled('slug') ? $request->slug : $request->title);

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

        if ($request->filled('slug') && $cmsPage->slug !== $request->slug) {
            $data['slug'] = $this->generateUniqueSlug(CmsPage::class, $request->slug, $cmsPage->id);
        } elseif (!$request->filled('slug') && $cmsPage->title !== $request->title) {
            $data['slug'] = $this->generateUniqueSlug(CmsPage::class, $request->title, $cmsPage->id);
        }

        $cmsPage->update($data);

        return redirect()->route('crm.cms-pages.index')
            ->with('success', 'Page updated successfully.');
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
            'status' => 'required|in:active,inactive',
        ]);
    }
}
