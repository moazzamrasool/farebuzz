<?php

namespace Tests\Unit;

use App\Support\PdfImage;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

// dompdf can only embed local filesystem paths (enable_remote is off in
// vendor/barryvdh/laravel-dompdf/config/dompdf.php), so PdfImage::resolve()
// is the one place that decides "usable in a PDF" vs. "fall back to the
// branded placeholder" — worth covering directly since a wrong answer here
// either breaks a PDF render or silently drops a real photo.
class PdfImageTest extends TestCase
{
    public function test_returns_null_for_empty_path(): void
    {
        $this->assertNull(PdfImage::resolve(null));
        $this->assertNull(PdfImage::resolve(''));
    }

    public function test_returns_null_for_remote_urls_since_dompdf_remote_fetch_is_disabled(): void
    {
        $this->assertNull(PdfImage::resolve('https://images.unsplash.com/photo-123'));
        $this->assertNull(PdfImage::resolve('http://example.com/photo.jpg'));
    }

    public function test_returns_null_when_the_stored_file_does_not_actually_exist(): void
    {
        Storage::fake('public');

        $this->assertNull(PdfImage::resolve('packages/does-not-exist.jpg'));
    }

    public function test_resolves_an_existing_public_disk_path_to_an_absolute_local_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('packages/cover.jpg', 'fake-image-bytes');

        $resolved = PdfImage::resolve('packages/cover.jpg');

        $this->assertNotNull($resolved);
        $this->assertTrue(is_file($resolved));
    }

    public function test_resolves_an_existing_frontend_static_asset(): void
    {
        // public/frontend/img/logo.png ships with the repo (the FareBuzzer logo
        // used on the live site) — a real file, not a fixture, so this also
        // guards against that asset being moved/renamed.
        $resolved = PdfImage::resolve('frontend/img/logo.png');

        $this->assertNotNull($resolved);
        $this->assertTrue(is_file($resolved));
    }
}
