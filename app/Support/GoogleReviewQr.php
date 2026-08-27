<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GoogleReviewQr
{
    /**
     * Public URL to an SVG QR code for the given review URL, generated once per
     * (tenant, url) pair and cached on disk — the filename is content-addressed
     * by the URL, so a company changing their review link naturally gets a fresh
     * QR the next time this is called, with no explicit cache-busting needed.
     * SVG (not PNG) because bacon/qr-code's raster backend needs ext-imagick,
     * which isn't guaranteed on every tenant's server; SVG needs no extension
     * and stays crisp at any size.
     */
    public static function forUrl(string $uniqueId, string $url): string
    {
        $path = 'google-review-qr/'.$uniqueId.'-'.sha1($url).'.svg';

        if (!Storage::disk('public')->exists($path)) {
            $svg = QrCode::format('svg')->size(240)->margin(1)->generate($url);
            Storage::disk('public')->put($path, $svg);
        }

        return asset('storage/'.$path);
    }
}
