<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Shared helper for seeders: generates labelled placeholder images (GD, no external
// dependency) or downloads a real reference photo, for master data that has no
// uploaded image yet. Idempotent — only fills in images that are still empty.
trait GeneratesDemoImages
{
    protected function placeholderImage(int $width, int $height, string $label, string $folder, string $bgHex = '#0d6efd'): string
    {
        [$r, $g, $b] = sscanf($bgHex, '#%02x%02x%02x');
        $im = imagecreatetruecolor($width, $height);
        imagefill($im, 0, 0, imagecolorallocate($im, $r, $g, $b));

        $white = imagecolorallocate($im, 255, 255, 255);
        $font = 'C:/Windows/Fonts/arialbd.ttf';
        $fontSize = max(12, (int) ($width / 18));

        if (function_exists('imagettftext') && is_file($font)) {
            $lines = $this->wrapForFont($label, $font, $fontSize, $width - 40);
            $lineHeight = (int) ($fontSize * 1.4);
            $totalHeight = count($lines) * $lineHeight;
            $y = (int) (($height - $totalHeight) / 2) + $fontSize;

            foreach ($lines as $line) {
                $box = imagettfbbox($fontSize, 0, $font, $line);
                $textWidth = abs($box[2] - $box[0]);
                $x = (int) (($width - $textWidth) / 2);
                imagettftext($im, $fontSize, 0, $x, $y, $white, $font, $line);
                $y += $lineHeight;
            }
        } else {
            $x = (int) (($width - imagefontwidth(5) * strlen($label)) / 2);
            imagestring($im, 5, max(0, $x), (int) ($height / 2) - 8, $label, $white);
        }

        $path = $folder.'/'.Str::random(16).'.jpg';
        ob_start();
        imagejpeg($im, null, 85);
        Storage::disk('public')->put($path, ob_get_clean());
        imagedestroy($im);

        return $path;
    }

    // Real-catalogue images (destinations, landmarks, activities) are sourced from
    // Wikimedia Commons — freely licensed, so no copyright risk. Uses the
    // Special:FilePath redirect keyed by the file's title (e.g. "Dal Lake Srinagar.jpg")
    // rather than a hand-typed /thumb/x/xx/hash/ URL — we can't reliably know Commons'
    // internal hash prefix for a given file, but Special:FilePath resolves by title and
    // 302s to the real upload.wikimedia.org URL, which fetchUrl()'s CURLOPT_FOLLOWLOCATION
    // already follows. Downloads once at seed time and stores locally; if the title is
    // wrong or the file doesn't exist, this just fails closed (null) like any other
    // downloadImage() call, and the caller falls back to placeholderImage().
    protected function downloadFromCommons(string $fileTitle, string $folder, int $width = 1200): ?string
    {
        $url = 'https://commons.wikimedia.org/wiki/Special:FilePath/'.rawurlencode($fileTitle).'?width='.$width;

        return $this->downloadImage($url, $folder);
    }

    protected function downloadImage(string $url, string $folder): ?string
    {
        try {
            $contents = $this->fetchUrl($url);
            if ($contents === null) {
                return null;
            }
            $path = $folder.'/'.Str::random(16).'.jpg';
            Storage::disk('public')->put($path, $contents);

            return $path;
        } catch (\Throwable) {
            return null;
        }
    }

    // Wikimedia (and some other hosts) reject requests with no/blank User-Agent,
    // so plain file_get_contents() fails silently. cURL with an identifying UA
    // works for both Wikimedia and Unsplash.
    private function fetchUrl(string $url): ?string
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_USERAGENT => 'FareBuzzAdmin-Seeder/1.0 (+https://farebuzz.example)',
            ]);
            $data = curl_exec($ch);
            $ok = $data !== false && curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200;
            curl_close($ch);

            return $ok ? $data : null;
        }

        $context = stream_context_create(['http' => [
            'header' => "User-Agent: FareBuzzAdmin-Seeder/1.0 (+https://farebuzz.example)\r\n",
            'timeout' => 15,
        ]]);
        $data = @file_get_contents($url, false, $context);

        return $data === false ? null : $data;
    }

    private function wrapForFont(string $text, string $font, int $size, int $maxWidth): array
    {
        $words = explode(' ', $text);
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $test = trim($current.' '.$word);
            $box = imagettfbbox($size, 0, $font, $test);
            if (abs($box[2] - $box[0]) > $maxWidth && $current !== '') {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $test;
            }
        }
        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines ?: [$text];
    }
}
