<?php

namespace App\Enums;

enum BedType: string
{
    case Twin = 'twin_bed';
    case Double = 'double_bed';
    case Full = 'full_bed';
    case Queen = 'queen_bed';
    case King = 'king_bed';
    case CaliforniaKing = 'california_king';
    case Rollaway = 'rollaway_bed';
    case Sofa = 'sofa_bed';

    public function label(): string
    {
        return match ($this) {
            self::Twin => 'Twin Bed',
            self::Double => 'Double Bed',
            self::Full => 'Full Bed',
            self::Queen => 'Queen Bed',
            self::King => 'King Bed',
            self::CaliforniaKing => 'California King',
            self::Rollaway => 'Rollaway Bed',
            self::Sofa => 'Sofa Bed',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
