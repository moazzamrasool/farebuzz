<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RobotsTxtRequest;
use App\Http\Requests\Admin\SitemapConfigRequest;
use App\Models\SeoSetting;
use App\Services\Seo\RobotsTxtBuilder;
use App\Services\Seo\SitemapGenerator;

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

    public function regenerateSitemap(SitemapGenerator $sitemap)
    {
        $result = $sitemap->regenerate();

        return redirect()
            ->route('crm.seo-settings.edit')
            ->with('success', "Sitemap regenerated — {$result['count']} URLs.");
    }
}
