<?php

namespace App\Support\Seo;

use App\Models\SeoSetting;
use App\Support\MediaUrl;
use Illuminate\Support\Str;

// Turns a content model's (possibly-blank) SEO columns into a complete set of
// <head> values with sensible fallbacks, so no public page ever ships an empty
// title/description/OG tag. Every content-type show/index view calls this once
// and pipes the result into partials._seo_head.
class SeoResolver
{
    /**
     * @param object|null $seo Model instance exposing meta_title/meta_description/meta_keywords/
     *                         focus_keyword/canonical_url/og_title/og_description/og_image/
     *                         robots_index/robots_follow (any/all may be null, or the object itself
     *                         may be null for pages with no admin-editable SEO row at all)
     * @param string $fallbackTitle Used when meta_title is blank
     * @param string|null $fallbackDescriptionSource Raw HTML/plain text excerpted when meta_description is blank
     * @param string|null $fallbackImage A storage-relative path (e.g. a cover image) used when og_image is blank
     * @param string|null $canonical Absolute URL for this page; defaults to the current request URL (no query string)
     */
    public static function resolve(
        ?object $seo,
        string $fallbackTitle,
        ?string $fallbackDescriptionSource = null,
        ?string $fallbackImage = null,
        ?string $canonical = null,
    ): array {
        $title = self::blankToNull($seo?->meta_title) ?? $fallbackTitle;

        $description = self::blankToNull($seo?->meta_description)
            ?? self::excerpt($fallbackDescriptionSource)
            ?? self::siteDefaultDescription();

        $ogTitle = self::blankToNull($seo?->og_title) ?? $title;
        $ogDescription = self::blankToNull($seo?->og_description) ?? $description;

        $ogImage = self::resolveImagePath($seo?->og_image)
            ?? self::resolveImagePath($fallbackImage)
            ?? self::siteDefaultImage();

        $robotsIndex = $seo?->robots_index ?? true;
        $robotsFollow = $seo?->robots_follow ?? true;

        return [
            'title'         => $title,
            'description'   => $description,
            'keywords'      => self::blankToNull($seo?->meta_keywords),
            'canonical'     => self::blankToNull($seo?->canonical_url) ?? $canonical ?? CanonicalUrl::current(),
            'ogTitle'       => $ogTitle,
            'ogDescription' => $ogDescription,
            'ogImage'       => $ogImage,
            'robots'        => ($robotsIndex ? 'index' : 'noindex').', '.($robotsFollow ? 'follow' : 'nofollow'),
        ];
    }

    private static function blankToNull(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private static function excerpt(?string $source): ?string
    {
        $text = trim(strip_tags((string) $source));

        return $text === '' ? null : Str::limit($text, 160, '...');
    }

    private static function resolveImagePath(?string $path): ?string
    {
        return $path ? MediaUrl::resolve($path) : null;
    }

    private static function siteDefaultImage(): ?string
    {
        $default = SeoSetting::forSite()->first()?->default_og_image;

        return $default ? MediaUrl::resolve($default) : null;
    }

    private static function siteDefaultDescription(): string
    {
        return 'Discover holiday packages, hotels and activities with FareBuzzer Travel — your trusted travel companion.';
    }
}
