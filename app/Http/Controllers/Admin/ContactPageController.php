<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Singleton settings screen — /contact-us was a static Blade view with no admin or
// database backing at all; this covers both its real contact details and its SEO,
// same edit-in-place approach as AboutPageController.
class ContactPageController extends Controller
{
    public function edit()
    {
        $contactPage = ContactPage::first() ?? new ContactPage();

        return view('admin.contact-page.edit', compact('contactPage'));
    }

    public function update(Request $request)
    {
        $contactPage = ContactPage::firstOrNew();

        $data = $request->validate([
            'address'          => 'nullable|string|max:255',
            'phone'            => 'nullable|string|max:50',
            'email'            => 'nullable|email|max:255',
            'support_hours'    => 'nullable|string|max:255',
            'map_embed_url'    => 'nullable|url|max:2048',
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
            if ($contactPage->og_image) {
                Storage::disk('public')->delete($contactPage->og_image);
            }
            $data['og_image'] = $request->file('og_image')->store('contact-page/og', 'public');
        } else {
            unset($data['og_image']);
        }

        $contactPage->fill($data)->save();

        return redirect()->route('crm.contact-page.edit')
            ->with('success', 'Contact Us page updated successfully.');
    }
}
