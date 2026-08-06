<?php

namespace App\Support;

class MediaUrl
{
    /**
     * Resolve a HomepageSectionItem-style image value to a renderable URL.
     * Seed data intentionally reuses the exact external (Unsplash/flagcdn) and
     * local template (public/frontend/img/...) URLs the static homepage used,
     * so the site looks identical after going dynamic; new admin uploads are
     * plain Storage::disk('public') paths. This resolves either transparently.
     */
    public static function resolve(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'frontend/')) {
            return asset($path);
        }

        return asset('storage/'.$path);
    }

    /**
     * Resolve a HomepageSectionItem/Coupon-style link value (a bare path like
     * 'about-us', 'careers', or '#') into an absolute URL built from the
     * APP_URL env value, so links work the same regardless of which page
     * they're rendered on.
     */
    public static function link(?string $path): string
    {
        $path = trim((string) $path);

        if ($path === '' || $path === '#') {
            return '#';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return rtrim(config('app.url'), '/').'/'.ltrim($path, '/');
    }
}
