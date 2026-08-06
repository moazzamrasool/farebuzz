<?php

namespace App\Enums;

enum LeadStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Interested = 'interested';
    case QuotationSent = 'quotation_sent';
    case Negotiation = 'negotiation';
    case FollowUp = 'follow_up';
    case Converted = 'converted';
    case Lost = 'lost';
    case Junk = 'junk';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Contacted => 'Contacted',
            self::Interested => 'Interested',
            self::QuotationSent => 'Quotation Sent',
            self::Negotiation => 'Negotiation',
            self::FollowUp => 'Follow-up',
            self::Converted => 'Converted (Won)',
            self::Lost => 'Lost / Not Interested',
            self::Junk => 'Junk',
        };
    }

    // Only Bootstrap's standard badge-* classes exist in this AdminLTE build (no
    // badge-purple/badge-orange) — 9 statuses over 7 usable colors means New/FollowUp
    // and QuotationSent/Junk deliberately share a color; both pairs are semantically
    // close enough ("needs agent action" / "closed out, unremarkable") not to confuse.
    public function badgeClass(): string
    {
        return match ($this) {
            self::New => 'badge-warning',
            self::Contacted => 'badge-info',
            self::Interested => 'badge-primary',
            self::QuotationSent => 'badge-secondary',
            self::Negotiation => 'badge-dark',
            self::FollowUp => 'badge-warning',
            self::Converted => 'badge-success',
            self::Lost => 'badge-danger',
            self::Junk => 'badge-secondary',
        };
    }

    public function requiresLostReason(): bool
    {
        return in_array($this, [self::Lost, self::Junk], true);
    }

    /** @return list<string> */
    public static function requiringLostReason(): array
    {
        return [self::Lost->value, self::Junk->value];
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
