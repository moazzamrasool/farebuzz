<?php

namespace App\Support;

class CountryFlag
{
    // Country name → ISO 3166-1 alpha-2, just enough to cover the destinations
    // this app seeds/manages. Unmapped countries simply render no flag.
    private const CODES = [
        'india' => 'IN',
        'maldives' => 'MV',
        'switzerland' => 'CH',
        'indonesia' => 'ID',
        'uae' => 'AE',
        'united arab emirates' => 'AE',
        'singapore' => 'SG',
        'thailand' => 'TH',
        'sri lanka' => 'LK',
        'nepal' => 'NP',
        'bhutan' => 'BT',
        'malaysia' => 'MY',
        'vietnam' => 'VN',
        'united kingdom' => 'GB',
        'usa' => 'US',
        'united states' => 'US',
        'australia' => 'AU',
        'france' => 'FR',
        'italy' => 'IT',
        'spain' => 'ES',
        'japan' => 'JP',
        'south korea' => 'KR',
        'egypt' => 'EG',
        'turkey' => 'TR',
    ];

    // Regional-indicator flag emoji built from the ISO code — same technique
    // the original static homepage/international-packages mockups used.
    // NOTE: Windows Chrome has no glyph for these regional-indicator pairs and
    // falls back to rendering the two letters as plain text (e.g. "IN"), which
    // reads as a duplicate when placed next to a country-code label — prefer
    // code() + a flagcdn.com <img> for anything rendered in a real page.
    public static function emoji(?string $country): string
    {
        $code = self::CODES[strtolower(trim((string) $country))] ?? null;
        if (!$code) {
            return '';
        }

        return mb_convert_encoding('&#'.(127397 + ord($code[0])).';&#'.(127397 + ord($code[1])).';', 'UTF-8', 'HTML-ENTITIES');
    }

    // Lowercase ISO 3166-1 alpha-2 code, e.g. for flagcdn.com/w20/{code}.png.
    public static function code(?string $country): ?string
    {
        $code = self::CODES[strtolower(trim((string) $country))] ?? null;

        return $code ? strtolower($code) : null;
    }
}
