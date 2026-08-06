<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use App\Models\AboutPageItem;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Singleton settings screen — one row per company, no index/create/delete,
// mirrors SearchTabSettingController's edit-in-place approach. Every
// repeatable-list section (team, timeline, gallery, ...) lives in the flat
// AboutPageItem table and is synced the same way HomepageSectionController
// syncs its cards.
class AboutPageController extends Controller
{
    // Single-value image fields on the about_page row itself (as opposed to
    // AboutPageItem rows, which carry their own image per list entry).
    private const IMAGE_FIELDS = ['hero_image', 'story_image', 'mission_image', 'life_image', 'impact_image'];

    public function edit()
    {
        $about = AboutPage::first() ?? new AboutPage();

        $items = [];
        foreach (array_keys(AboutPageItem::SECTIONS) as $key) {
            $items[$key] = AboutPageItem::section($key)->ordered()->get();
        }

        return view('admin.about-page.edit', compact('about', 'items'));
    }

    public function update(Request $request)
    {
        $about = AboutPage::firstOrNew();
        $data = $this->validated($request);

        if (!empty($data['story_body'])) {
            $data['story_body'] = HtmlSanitizer::clean($data['story_body']);
        }
        if (!empty($data['mission_body'])) {
            $data['mission_body'] = HtmlSanitizer::clean($data['mission_body']);
        }

        foreach (self::IMAGE_FIELDS as $field) {
            if ($request->hasFile($field)) {
                if ($about->{$field}) {
                    Storage::disk('public')->delete($about->{$field});
                }
                $data[$field] = $request->file($field)->store('about', 'public');
            } else {
                unset($data[$field]);
            }
        }

        $about->fill($data)->save();

        foreach (array_keys(AboutPageItem::SECTIONS) as $key) {
            $this->syncItems($request, $key);
        }

        return redirect()->route('crm.about-page.edit')
            ->with('success', 'About Us page updated successfully.');
    }

    // Mirrors HomepageSectionController::syncItems — keep existing rows in
    // place (matched by hidden items[section][i][id]), create new ones,
    // delete whatever wasn't resubmitted for this section.
    private function syncItems(Request $request, string $sectionKey): void
    {
        $rows = $request->input("items.{$sectionKey}", []);
        $files = $request->file("items.{$sectionKey}", []);
        $keepIds = [];

        foreach ($rows as $index => $row) {
            $existing = !empty($row['id']) ? AboutPageItem::section($sectionKey)->find($row['id']) : null;
            $file = $files[$index]['image'] ?? null;

            if (!$existing && !$file && empty($row['title']) && empty($row['image_existing'])) {
                continue;
            }

            $attributes = [
                'section_key' => $sectionKey,
                'title' => $row['title'] ?? null,
                'subtitle' => $row['subtitle'] ?? null,
                'description' => $row['description'] ?? null,
                'link' => $row['link'] ?? null,
                'sort_order' => $index,
                'status' => !empty($row['status']) ? 'active' : 'inactive',
            ];

            if ($file) {
                if ($existing && $existing->image) {
                    Storage::disk('public')->delete($existing->image);
                }
                $attributes['image'] = $file->store("about/{$sectionKey}", 'public');
            }

            if ($existing) {
                $existing->update($attributes);
                $keepIds[] = $existing->id;
            } else {
                $item = AboutPageItem::create($attributes);
                $keepIds[] = $item->id;
            }
        }

        $removed = AboutPageItem::section($sectionKey)->whereNotIn('id', $keepIds)->get();
        foreach ($removed as $item) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $item->delete();
        }
    }

    private function validated(Request $request): array
    {
        $rules = [
            'hero_heading' => 'nullable|string|max:255',
            'hero_tagline' => 'nullable|string|max:255',
            'hero_image' => 'nullable|image|max:2048',

            'scale_heading' => 'nullable|string|max:255',
            'scale_subheading' => 'nullable|string|max:255',

            'story_heading' => 'nullable|string|max:255',
            'story_body' => 'nullable|string',
            'story_image' => 'nullable|image|max:2048',

            'mission_heading' => 'nullable|string|max:255',
            'mission_body' => 'nullable|string',
            'mission_image' => 'nullable|image|max:2048',
            'founded_year' => 'nullable|integer|min:1900|max:'.now()->year,

            'team_heading' => 'nullable|string|max:255',
            'team_subheading' => 'nullable|string|max:255',

            'growth_heading' => 'nullable|string|max:255',
            'growth_subheading' => 'nullable|string|max:255',

            'timeline_heading' => 'nullable|string|max:255',
            'timeline_subheading' => 'nullable|string|max:255',

            'life_heading' => 'nullable|string|max:255',
            'life_subheading' => 'nullable|string|max:255',
            'life_body' => 'nullable|string',
            'life_image' => 'nullable|image|max:2048',

            'gallery_heading' => 'nullable|string|max:255',
            'testimonials_heading' => 'nullable|string|max:255',
            'press_heading' => 'nullable|string|max:255',

            'impact_heading' => 'nullable|string|max:255',
            'impact_body' => 'nullable|string',
            'impact_image' => 'nullable|image|max:2048',

            'awards_heading' => 'nullable|string|max:255',

            'cta_heading' => 'nullable|string|max:255',
            'cta_text' => 'nullable|string|max:255',
            'cta_button_text' => 'nullable|string|max:255',
            'cta_button_link' => 'nullable|string|max:255',

            'cta2_heading' => 'nullable|string|max:255',
            'cta2_text' => 'nullable|string|max:255',
            'cta2_button_text' => 'nullable|string|max:255',
            'cta2_button_link' => 'nullable|string|max:255',

            'career_heading' => 'nullable|string|max:255',
            'career_text' => 'nullable|string|max:255',
            'career_button_text' => 'nullable|string|max:255',
            'career_button_link' => 'nullable|string|max:255',

            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
        ];

        for ($i = 1; $i <= 4; $i++) {
            $rules["stat{$i}_label"] = 'nullable|string|max:255';
            $rules["stat{$i}_value"] = 'nullable|string|max:255';
            $rules["stat{$i}_source"] = 'nullable|in:'.implode(',', array_keys(AboutPage::STAT_SOURCES));
        }

        for ($i = 1; $i <= 3; $i++) {
            $rules["feature{$i}_icon"] = 'nullable|string|max:255';
            $rules["feature{$i}_title"] = 'nullable|string|max:255';
            $rules["feature{$i}_description"] = 'nullable|string|max:500';
        }

        return $request->validate($rules);
    }
}
