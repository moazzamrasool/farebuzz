<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\HomepageSection;
use App\Models\SearchTabSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * One controller drives all 14 homepage sections. Each section's card shape
 * (which fields it uses, whether it has tabs, etc.) is declared in
 * config/homepage_sections.php rather than hard-coded per section, so adding
 * a 15th section is a config entry + seeder, not a new controller/view pair.
 */
class HomepageSectionController extends Controller
{
    // Homepage Sections ordering screen — reorder / enable / disable whole sections.
    public function index()
    {
        $sections = HomepageSection::orderBy('sort_order')->get();

        return view('admin.homepage.index', compact('sections'));
    }

    public function reorder(Request $request)
    {
        foreach ($request->input('order', []) as $index => $id) {
            HomepageSection::whereKey($id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function toggleStatus(HomepageSection $section)
    {
        $section->update([
            'status' => $section->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('crm.homepage-sections.index')
            ->with('success', 'Section status updated successfully.');
    }

    public function edit(HomepageSection $section)
    {
        $config = $this->configFor($section);
        $section->load('items');

        $searchTabs = $section->key === 'hero' ? SearchTabSetting::ordered()->get() : null;
        // Offer cards can optionally reference a real Coupon so its code shows on the card —
        // only needed for this one section, not every homepage section.
        $coupons = $section->key === 'offers' ? Coupon::active()->orderBy('title')->get() : null;

        return view('admin.homepage.edit', compact('section', 'config', 'searchTabs', 'coupons'));
    }

    public function update(Request $request, HomepageSection $section)
    {
        $config = $this->configFor($section);

        $data = $request->validate([
            'heading' => 'nullable|string|max:255',
            'subheading' => 'nullable|string|max:255',
            'item_limit' => 'nullable|integer|min:1|max:50',
        ]);

        $extra = [];
        foreach ($config['extra_fields'] as $field) {
            $extra[$field] = $request->input("extra.{$field}");
        }

        if (($config['group']['type'] ?? null) === 'tabs') {
            $tabs = [];
            foreach ($request->input('tabs', []) as $tab) {
                if (empty($tab['label'])) {
                    continue;
                }
                $tabs[] = [
                    'key' => $tab['key'] ?: Str::slug($tab['label'], '_'),
                    'label' => $tab['label'],
                ];
            }
            $extra['tabs'] = $tabs;
        }

        $section->update([
            'heading' => $data['heading'] ?? null,
            'subheading' => $data['subheading'] ?? null,
            'extra' => $extra,
            'item_limit' => $data['item_limit'] ?? $section->item_limit,
        ]);

        // Live (data_source-driven) sections pull their cards from real master
        // data at render time — there are no manually-curated items to sync.
        if (empty($config['data_source'])) {
            $this->syncItems($request, $section, $config);
        }

        return redirect()->route('crm.homepage-sections.edit', $section->key)
            ->with('success', 'Section updated successfully.');
    }

    private function configFor(HomepageSection $section): array
    {
        $config = config("homepage_sections.{$section->key}");
        abort_if(!$config, 404);

        return $config;
    }

    // Mirrors HolidayPackageController::syncPhotos/syncFaqs — keep existing
    // rows in place (matched by hidden `items[i][id]`), create new ones,
    // delete whatever wasn't resubmitted.
    private function syncItems(Request $request, HomepageSection $section, array $config): void
    {
        $rows = $request->input('items', []);
        $files = $request->file('items', []);
        $simpleFields = ['title', 'subtitle', 'description', 'label', 'link', 'button_label', 'price', 'old_price', 'rating', 'review_count'];
        $keepIds = [];

        foreach ($rows as $index => $row) {
            $existing = !empty($row['id']) ? $section->items()->find($row['id']) : null;
            $file = $files[$index]['image'] ?? null;

            // Skip a brand new, entirely empty row (nothing was filled in).
            if (!$existing && !$file && empty($row['title']) && empty($row['image_existing'])) {
                continue;
            }

            $attributes = [
                'group_key' => $row['group_key'] ?? null,
                'sort_order' => $index,
                'status' => !empty($row['status']) ? 'active' : 'inactive',
            ];

            if ($section->key === 'offers') {
                $attributes['coupon_id'] = $row['coupon_id'] ?: null;
            }

            foreach ($simpleFields as $field) {
                if (in_array($field, $config['fields'], true)) {
                    $attributes[$field] = ($row[$field] ?? '') !== '' ? $row[$field] : null;
                }
            }

            if (!empty($config['meta_fields']) || !empty($config['meta_image_fields'])) {
                // Start from the existing meta so an untouched image field
                // (no new file re-uploaded) keeps its previously stored path.
                $meta = $existing->meta ?? [];

                foreach ($config['meta_fields'] ?? [] as $metaField) {
                    $meta[$metaField] = $row['meta'][$metaField] ?? null;
                }

                foreach ($config['meta_image_fields'] ?? [] as $metaImageField) {
                    $metaFile = $files[$index]['meta'][$metaImageField] ?? null;
                    if ($metaFile) {
                        if (!empty($meta[$metaImageField])) {
                            Storage::disk('public')->delete($meta[$metaImageField]);
                        }
                        $meta[$metaImageField] = $metaFile->store("homepage/{$section->key}/meta", 'public');
                    }
                }

                $attributes['meta'] = $meta;
            }

            if ($file) {
                if ($existing && $existing->image) {
                    Storage::disk('public')->delete($existing->image);
                }
                $attributes['image'] = $file->store("homepage/{$section->key}", 'public');
            }

            if ($existing) {
                $existing->update($attributes);
                $keepIds[] = $existing->id;
            } else {
                $item = $section->items()->create($attributes);
                $keepIds[] = $item->id;
            }
        }

        $removed = $section->items()->whereNotIn('id', $keepIds)->get();
        foreach ($removed as $item) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $item->delete();
        }
    }
}
