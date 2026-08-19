<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListingPageSeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Singleton-per-page settings screens for the general public listing pages
// (india-packages, international-packages, hotels, activities) — same edit-in-place
// approach as HomepageSeoController, keyed by page instead of one fixed table.
class ListingPageSeoController extends Controller
{
    public function index()
    {
        return view('admin.listing-page-seo.index', ['pages' => ListingPageSeo::PAGES]);
    }

    public function edit(string $page)
    {
        $this->abortIfUnknownPage($page);

        $listingPageSeo = ListingPageSeo::forCurrentTenantPage($page);

        return view('admin.listing-page-seo.edit', [
            'listingPageSeo' => $listingPageSeo,
            'page'           => $page,
            'pageLabel'      => ListingPageSeo::PAGES[$page],
        ]);
    }

    public function update(Request $request, string $page)
    {
        $this->abortIfUnknownPage($page);

        $listingPageSeo = ListingPageSeo::forCurrentTenantPage($page);

        $data = $request->validate([
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:255',
            'focus_keyword'    => 'nullable|string|max:255',
            'tags'             => 'nullable|string|max:255',
            'canonical_url'    => 'nullable|url|max:255',
            'og_title'         => 'nullable|string|max:255',
            'og_description'   => 'nullable|string|max:500',
            'og_image'         => 'nullable|image|max:2048',
            'robots_index'     => 'nullable|boolean',
            'robots_follow'    => 'nullable|boolean',
        ]);

        $data['robots_index'] = $request->boolean('robots_index');
        $data['robots_follow'] = $request->boolean('robots_follow');

        if ($request->hasFile('og_image')) {
            if ($listingPageSeo->og_image) {
                Storage::disk('public')->delete($listingPageSeo->og_image);
            }
            $data['og_image'] = $request->file('og_image')->store('listing-pages/og', 'public');
        } else {
            unset($data['og_image']);
        }

        $listingPageSeo->fill($data)->save();

        return redirect()->route('crm.listing-page-seo.edit', $page)
            ->with('success', ListingPageSeo::PAGES[$page].' SEO updated successfully.');
    }

    private function abortIfUnknownPage(string $page): void
    {
        if (!array_key_exists($page, ListingPageSeo::PAGES)) {
            throw new NotFoundHttpException();
        }
    }
}
