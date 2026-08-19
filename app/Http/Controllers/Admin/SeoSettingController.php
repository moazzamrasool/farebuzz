<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RobotsTxtRequest;
use App\Http\Requests\Admin\SitemapConfigRequest;
use App\Models\SeoSetting;
use App\Services\Seo\RobotsTxtBuilder;
use App\Services\Seo\SitemapGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeoSettingController extends Controller
{
    public function edit(SitemapGenerator $sitemap)
    {
        $setting = SeoSetting::forCurrentTenant();

        return view('admin.seo.edit', [
            'setting' => $setting,
            'sitemapConfig' => $setting->sitemapConfig(),
            'defaultRobotsTxt' => RobotsTxtBuilder::default(),
            'sitemapUrl' => url('/sitemap.xml'),
            'robotsUrl' => url('/robots.txt'),
        ]);
    }

    public function updateRobots(RobotsTxtRequest $request)
    {
        $setting = SeoSetting::forCurrentTenant();

        $setting->update([
            'robots_txt' => $request->input('robots_txt') ?: null,
        ]);

        return redirect()
            ->route('crm.seo-settings.edit')
            ->with('success', 'robots.txt updated.');
    }

    public function updateSitemapConfig(SitemapConfigRequest $request, SitemapGenerator $sitemap)
    {
        $setting = SeoSetting::forCurrentTenant();

        $config = [];
        foreach (array_keys(SeoSetting::DEFAULT_SITEMAP_CONFIG) as $type) {
            $config[$type] = [
                'included' => $request->boolean("included.{$type}"),
                'priority' => (string) $request->input("priority.{$type}"),
                'changefreq' => $request->input("changefreq.{$type}"),
            ];
        }

        $setting->update(['sitemap_config' => $config]);
        $sitemap->regenerate();

        return redirect()
            ->route('crm.seo-settings.edit')
            ->with('success', 'Sitemap settings updated and regenerated.');
    }

    public function updateDefaults(Request $request)
    {
        $setting = SeoSetting::forCurrentTenant();

        $data = $request->validate([
            'organization_name' => 'nullable|string|max:255',
            'organization_logo' => 'nullable|image|max:2048',
            'default_og_image'  => 'nullable|image|max:2048',
            'social_links.facebook'  => 'nullable|url|max:255',
            'social_links.instagram' => 'nullable|url|max:255',
            'social_links.twitter'   => 'nullable|url|max:255',
            'social_links.youtube'   => 'nullable|url|max:255',
            'social_links.linkedin'  => 'nullable|url|max:255',
        ]);

        if ($request->hasFile('organization_logo')) {
            if ($setting->organization_logo) {
                Storage::disk('public')->delete($setting->organization_logo);
            }
            $data['organization_logo'] = $request->file('organization_logo')->store('seo', 'public');
        } else {
            unset($data['organization_logo']);
        }

        if ($request->hasFile('default_og_image')) {
            if ($setting->default_og_image) {
                Storage::disk('public')->delete($setting->default_og_image);
            }
            $data['default_og_image'] = $request->file('default_og_image')->store('seo', 'public');
        } else {
            unset($data['default_og_image']);
        }

        $data['social_links'] = array_filter($data['social_links'] ?? []);

        $setting->update($data);

        return redirect()
            ->route('crm.seo-settings.edit')
            ->with('success', 'Site-wide SEO defaults updated.');
    }

    public function regenerateSitemap(SitemapGenerator $sitemap)
    {
        $result = $sitemap->regenerate();

        return redirect()
            ->route('crm.seo-settings.edit')
            ->with('success', "Sitemap regenerated — {$result['count']} URLs.");
    }
}
