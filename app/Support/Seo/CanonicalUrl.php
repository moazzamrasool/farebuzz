<?php

namespace App\Support\Seo;

use Illuminate\Support\Facades\Request;

// Canonical strategy for the public frontend: filtered/query-string URLs always
// canonical to their clean base URL to avoid duplicate-content penalties, while
// page 2+ of a paginated listing canonicals to itself (current Google guidance —
// rel=prev/next is deprecated in favour of self-canonical + index,follow).
//
// Built on the url()/route() generator (not the raw Request facade) so it honours
// AppServiceProvider's URL::forceScheme('https') — Request::url()/fullUrl() read
// the literal incoming request scheme instead, which would leak http:// into the
// canonical if the app is ever reached through a proxy that doesn't forward the
// original scheme.
class CanonicalUrl
{
    // Current request's own URL — used as the default canonical for a singular
    // detail page (package/hotel/destination/blog show, CMS page, ...).
    public static function current(): string
    {
        return url()->current();
    }

    // For listing pages: the clean base URL, with only a `page` param preserved
    // when it's present (any other filter/sort query string is dropped).
    public static function forListing(): string
    {
        $base = url()->current();

        return Request::filled('page') ? $base.'?page='.Request::integer('page') : $base;
    }
}
