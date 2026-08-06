<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SearchTabSetting;
use Illuminate\Http\Request;

// PART A: lets the CRM flip a hero search tab on/off without a code change.
class SearchTabSettingController extends Controller
{
    public function update(Request $request)
    {
        foreach ($request->input('tabs', []) as $index => $row) {
            if (empty($row['tab_key'])) {
                continue;
            }

            SearchTabSetting::where('tab_key', $row['tab_key'])->update([
                'enabled' => !empty($row['enabled']),
                'badge_text' => $row['badge_text'] ?: null,
                'sort_order' => $index,
            ]);
        }

        return redirect()->route('crm.homepage-sections.edit', 'hero')
            ->with('success', 'Search tabs updated successfully.');
    }
}
