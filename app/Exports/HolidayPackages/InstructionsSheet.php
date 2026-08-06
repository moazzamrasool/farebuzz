<?php

namespace App\Exports\HolidayPackages;

use App\Models\Activity;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\PackageFeature;
use App\Models\TravelCategory;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class InstructionsSheet implements FromArray, WithHeadings, WithTitle
{
    public function array(): array
    {
        $rows = [
            ['This sheet is a reference guide only — do not enter data here.', '', ''],
            ['', '', ''],
            ['Scope', '', 'Bulk upload creates the package core, its day-wise itinerary, room/pricing tiers, per-day hotels, per-day activities, inclusions/exclusions and FAQs, and downloads photos from image URLs — one package per Excel file upload, built from the sheets below. Reviews, departure cities and "You Might Also Like" picks are NOT covered by bulk upload — add those afterwards from the package\'s normal edit screen.'],
            ['Linking sheets together', '', 'Every sheet after Package Core has a package_title column. It must exactly match a "title" on the Package Core sheet in this same file — that is how a row on any other sheet gets attached to the right package.'],
            ['Pricing tier', '', 'If the Room Types sheet has no rows for a package, it automatically gets one "Standard" tier using its price and discounted_price, since the app requires at least one pricing tier to display pricing. If the Room Types sheet does have rows for that package, they replace the auto "Standard" tier entirely.'],
            ['Optional sheets', '', 'Room Types, Day Hotels, Day Activities, Inclusions & Exclusions, FAQs and Photos are all optional — delete a tab entirely if you have nothing to add there, the rest of the file will still import.'],
            ['', '', ''],

            ['Package Core column', 'Required?', 'Format / Notes'],
            ['title', 'Required', 'Plain text.'],
            ['slug', 'Optional', 'Leave blank to auto-generate from title.'],
            ['destination', 'Required', 'Must exactly match one of your existing destination names below.'],
            ['categories', 'Optional', 'Comma-separated list — every name must match one of your existing travel category names below.'],
            ['nights', 'Required', 'Whole number.'],
            ['days', 'Optional', 'Whole number. Leave blank to default to nights + 1. If provided, it must equal nights + 1.'],
            ['hotel_category', 'Optional', 'Plain text, e.g. "4 Star".'],
            ['meals', 'Optional', 'Plain text, e.g. "Breakfast".'],
            ['language', 'Optional', 'Plain text.'],
            ['places_to_visit', 'Optional', 'Plain text.'],
            ['overview', 'Optional', 'Plain text.'],
            ['price', 'Required', 'Number — the original/base price.'],
            ['discounted_price', 'Optional', 'Number, must be less than price.'],
            ['booking_type', 'Optional', 'Exactly "enquiry_only" or "book_enquiry". Defaults to "enquiry_only".'],
            ['best_seller', 'Optional', '"yes" or "no". Defaults to "no".'],
            ['featured', 'Optional', '"yes" or "no". Defaults to "no".'],
            ['status', 'Required', 'Exactly "active" or "inactive".'],
            ['sort_order', 'Optional', 'Whole number. Defaults to 0.'],
            ['', '', ''],

            ['Itinerary column', 'Required?', 'Format / Notes'],
            ['package_title', 'Required', 'Must exactly match a "title" from the Package Core sheet in this same file.'],
            ['day_number', 'Required', 'Whole number, 1 and up.'],
            ['day_title', 'Required', 'Plain text, e.g. "Arrival in Goa".'],
            ['route_summary', 'Optional', 'Short plain text, e.g. "Airport to Baga Beach".'],
            ['detail', 'Optional', 'Plain text — the day\'s full description.'],
            ['bullet_points', 'Optional', 'Semicolon-separated list, e.g. "Airport pickup included;Welcome drink on arrival".'],
            ['meal_tags', 'Optional', 'Comma-separated, only these values are valid: breakfast, lunch, dinner.'],
            ['', '', ''],

            ['Room Types column', 'Required?', 'Format / Notes'],
            ['package_title', 'Required', 'Must exactly match a "title" from the Package Core sheet.'],
            ['room_name', 'Required', 'Plain text, e.g. "Deluxe Sea View".'],
            ['price', 'Required', 'Number — the original price for this tier.'],
            ['discounted_price', 'Optional', 'Number, must be less than price.'],
            ['sort_order', 'Optional', 'Whole number. Defaults to the order rows appear in for that package.'],
            ['', '', ''],

            ['Day Hotels column', 'Required?', 'Format / Notes'],
            ['package_title', 'Required', 'Must exactly match a "title" from the Package Core sheet.'],
            ['day_number', 'Optional', 'Whole number, 1 and up. Leave blank if the hotel applies to the whole trip rather than one specific day.'],
            ['hotel_name', 'Required', 'Must exactly match one of your existing hotel names below.'],
            ['room_type_name', 'Optional', 'Must exactly match one of that hotel\'s own room type names below, if given.'],
            ['price', 'Optional', 'Number — override price for this stay, if it differs from the room type\'s own price.'],
            ['is_optional', 'Optional', '"yes" or "no". Defaults to "no".'],
            ['nights', 'Optional', 'Whole number. Defaults to 1.'],
            ['note', 'Optional', 'Short plain text.'],
            ['sort_order', 'Optional', 'Whole number. Defaults to the order rows appear in for that package.'],
            ['', '', ''],

            ['Day Activities column', 'Required?', 'Format / Notes'],
            ['package_title', 'Required', 'Must exactly match a "title" from the Package Core sheet.'],
            ['day_number', 'Optional', 'Whole number, 1 and up. Leave blank if the activity is not tied to one specific day.'],
            ['activity_name', 'Required', 'Must exactly match one of your existing activity names below.'],
            ['price', 'Optional', 'Number — price if this is a paid add-on.'],
            ['is_optional', 'Optional', '"yes" or "no". Defaults to "no".'],
            ['note', 'Optional', 'Short plain text.'],
            ['sort_order', 'Optional', 'Whole number. Defaults to the order rows appear in for that package.'],
            ['', '', ''],

            ['Inclusions & Exclusions column', 'Required?', 'Format / Notes'],
            ['package_title', 'Required', 'Must exactly match a "title" from the Package Core sheet.'],
            ['type', 'Required', 'Exactly "inclusion" or "exclusion".'],
            ['item_title', 'Required', 'If it exactly matches one of your existing inclusion/exclusion names below, the package links to that curated item. Otherwise a custom one-off line is created with this exact text.'],
            ['sort_order', 'Optional', 'Whole number. Defaults to the order rows appear in for that package.'],
            ['', '', ''],

            ['FAQs column', 'Required?', 'Format / Notes'],
            ['package_title', 'Required', 'Must exactly match a "title" from the Package Core sheet.'],
            ['question', 'Required', 'Plain text.'],
            ['answer', 'Optional', 'Plain text.'],
            ['sort_order', 'Optional', 'Whole number. Defaults to the order rows appear in for that package.'],
            ['', '', ''],

            ['Photos column', 'Required?', 'Format / Notes'],
            ['package_title', 'Required', 'Must exactly match a "title" from the Package Core sheet.'],
            ['image_url', 'Required', 'A direct, publicly reachable image link (jpg/png/webp/gif). The image is downloaded and stored — the row fails if it can\'t be fetched.'],
            ['is_cover', 'Optional', '"yes" or "no". Defaults to "no".'],
            ['sort_order', 'Optional', 'Whole number. Defaults to the order rows appear in for that package.'],
            ['', '', ''],

            ['Your existing destination names:', '', ''],
        ];

        foreach (Destination::orderBy('name')->pluck('name') as $name) {
            $rows[] = [$name, '', ''];
        }

        $rows[] = ['', '', ''];
        $rows[] = ['Your existing travel category names:', '', ''];

        foreach (TravelCategory::orderBy('name')->pluck('name') as $name) {
            $rows[] = [$name, '', ''];
        }

        $rows[] = ['', '', ''];
        $rows[] = ['Your existing hotel names (with destination and room types):', '', ''];

        foreach (Hotel::with('destination', 'roomTypes')->orderBy('name')->get() as $hotel) {
            $roomTypeNames = $hotel->roomTypes->pluck('name')->implode(', ') ?: '—';
            $rows[] = [$hotel->name, $hotel->destination->name ?? '', "Room types: {$roomTypeNames}"];
        }

        $rows[] = ['', '', ''];
        $rows[] = ['Your existing activity names (with destination):', '', ''];

        foreach (Activity::with('destination')->orderBy('name')->get() as $activity) {
            $rows[] = [$activity->name, $activity->destination->name ?? '', ''];
        }

        $rows[] = ['', '', ''];
        $rows[] = ['Your existing inclusion names:', '', ''];

        foreach (PackageFeature::inclusions()->orderBy('title')->pluck('title') as $title) {
            $rows[] = [$title, '', ''];
        }

        $rows[] = ['', '', ''];
        $rows[] = ['Your existing exclusion names:', '', ''];

        foreach (PackageFeature::exclusions()->orderBy('title')->pluck('title') as $title) {
            $rows[] = [$title, '', ''];
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['Column', 'Required?', 'Format / Notes'];
    }

    public function title(): string
    {
        return 'Instructions';
    }
}
