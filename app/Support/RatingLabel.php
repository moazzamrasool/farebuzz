<?php

namespace App\Support;

class RatingLabel
{
    // Booking.com-style score → label banding, matching the original static homepage copy.
    public static function forScore(float|string|null $score): string
    {
        $score = (float) $score;

        return match (true) {
            $score >= 9.0 => 'Superb',
            $score >= 8.5 => 'Fabulous',
            $score >= 8.0 => 'Very good',
            $score >= 7.0 => 'Good',
            default => 'Fair',
        };
    }
}
