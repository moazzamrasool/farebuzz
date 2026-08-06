<?php

namespace App\Services\WhatsApp;

use App\Models\HolidayPackage;
use Illuminate\Support\Str;

// Matches a customer's free-text requirement to one of the tenant's active holiday
// packages by simple keyword overlap — no extra AI call needed for this step.
class WhatsAppPackageMatcher
{
    private const MIN_SCORE = 1;

    public function match(string $uniqueId, string $requirement): ?HolidayPackage
    {
        $words = $this->words($requirement);

        if ($words === []) {
            return null;
        }

        // TenantScope is a no-op in this (unauthenticated webhook) context, so filter
        // by unique_id explicitly rather than relying on the global scope.
        $packages = HolidayPackage::withoutGlobalScopes()
            ->where('unique_id', $uniqueId)
            ->where('status', 'active')
            ->get(['id', 'title']);

        $best = null;
        $bestScore = 0;

        foreach ($packages as $package) {
            $score = count(array_intersect($words, $this->words($package->title)));

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $package;
            }
        }

        return $bestScore >= self::MIN_SCORE ? $best : null;
    }

    private function words(string $text): array
    {
        $words = preg_split('/[^a-z0-9]+/', Str::lower($text), -1, PREG_SPLIT_NO_EMPTY);

        // Drop very short/common words so "a trip to goa" doesn't score on "a"/"to".
        return array_values(array_filter($words, fn (string $w) => strlen($w) >= 3));
    }
}
