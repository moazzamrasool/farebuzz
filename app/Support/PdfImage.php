<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

// Resolves a stored image path (Storage::disk('public') path, or a
// 'frontend/...' static asset path) to an absolute local filesystem path that
// dompdf can embed directly. dompdf's default config has enable_remote=false
// and chroot=base_path(), so http(s) URLs (as returned by MediaUrl::resolve())
// can't be used inside a PDF — only local paths under the project root work.
// Callers treat a null return as "no image available" and fall back to the
// branded CSS placeholder rather than risk a broken/missing <img>.
class PdfImage
{
    public static function resolve(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return null;
        }

        $absolute = str_starts_with($path, 'frontend/')
            ? public_path($path)
            : Storage::disk('public')->path($path);

        return is_file($absolute) ? $absolute : null;
    }
}
