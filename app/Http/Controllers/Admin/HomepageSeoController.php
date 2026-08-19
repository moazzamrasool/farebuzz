<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Singleton settings screen — homepage content itself lives in HomepageSection /
// HomepageSectionItem, this only covers the <head> tags the homepage renders,
// same edit-in-place approach as AboutPageController / ContactPageController.
class HomepageSeoController extends Controller
{
    public function edit()
    {
        $homepageSeo = HomepageSeo::first() ?? new HomepageSeo();

        return view('admin.homepage-seo.edit', compact('homepageSeo'));
    }

    public function update(Request $request)
    {
        $homepageSeo = HomepageSeo::firstOrNew();

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
            if ($homepageSeo->og_image) {
                Storage::disk('public')->delete($homepageSeo->og_image);
            }
            $data['og_image'] = $request->file('og_image')->store('homepage/og', 'public');
        } else {
            unset($data['og_image']);
        }

        $homepageSeo->fill($data)->save();

        return redirect()->route('crm.homepage-seo.edit')
            ->with('success', 'Homepage SEO updated successfully.');
    }
}
