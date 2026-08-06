<?php

namespace App\Imports;

use App\Imports\HolidayPackages\DayActivitiesSheetImport;
use App\Imports\HolidayPackages\DayHotelsSheetImport;
use App\Imports\HolidayPackages\FaqsSheetImport;
use App\Imports\HolidayPackages\InclusionsExclusionsSheetImport;
use App\Imports\HolidayPackages\PackageCoreSheetImport;
use App\Imports\HolidayPackages\PackageItinerarySheetImport;
use App\Imports\HolidayPackages\PhotosSheetImport;
use App\Imports\HolidayPackages\RoomTypesSheetImport;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

// Implements SkipsUnknownSheets because every sheet after "Package Core" is
// optional (a user with no FAQs, no custom photos, etc. may simply delete
// that tab from the workbook before uploading) — without it, Maatwebsite
// throws a SheetNotFoundException and aborts the whole import the moment one
// named sheet in sheets() below isn't physically present in the file.
class HolidayPackagesImport implements SkipsUnknownSheets, WithMultipleSheets
{
    private PackageCoreSheetImport $coreImport;

    private PackageItinerarySheetImport $itineraryImport;

    private RoomTypesSheetImport $roomTypesImport;

    private DayHotelsSheetImport $dayHotelsImport;

    private DayActivitiesSheetImport $dayActivitiesImport;

    private InclusionsExclusionsSheetImport $inclusionsExclusionsImport;

    private FaqsSheetImport $faqsImport;

    private PhotosSheetImport $photosImport;

    private array $missingSheetErrors = [];

    public function __construct()
    {
        $this->coreImport = new PackageCoreSheetImport;
        $this->itineraryImport = new PackageItinerarySheetImport;
        $this->roomTypesImport = new RoomTypesSheetImport;
        $this->dayHotelsImport = new DayHotelsSheetImport;
        $this->dayActivitiesImport = new DayActivitiesSheetImport;
        $this->inclusionsExclusionsImport = new InclusionsExclusionsSheetImport;
        $this->faqsImport = new FaqsSheetImport;
        $this->photosImport = new PhotosSheetImport;
    }

    // Keys must match the sheet titles produced by HolidayPackagesTemplateExport
    // exactly — that's how Maatwebsite matches each sub-importer to a physical
    // sheet in the uploaded file, and processes them in this order. Package Core
    // must run first (every other sheet looks its rows' package up by title, so
    // the package has to already exist). Room Types/Day Hotels/Day Activities/
    // Inclusions & Exclusions/FAQs/Photos are all optional — a workbook missing
    // any of them (e.g. a user deleted the Photos tab because they have none)
    // imports fine (see onUnknownSheet() below); only a missing Package Core or
    // Itinerary sheet is treated as an error, since everything else depends on
    // those two.
    public function sheets(): array
    {
        return [
            'Package Core' => $this->coreImport,
            'Itinerary' => $this->itineraryImport,
            'Room Types' => $this->roomTypesImport,
            'Day Hotels' => $this->dayHotelsImport,
            'Day Activities' => $this->dayActivitiesImport,
            'Inclusions & Exclusions' => $this->inclusionsExclusionsImport,
            'FAQs' => $this->faqsImport,
            'Photos' => $this->photosImport,
        ];
    }

    private function subImports(): array
    {
        return [
            $this->coreImport,
            $this->itineraryImport,
            $this->roomTypesImport,
            $this->dayHotelsImport,
            $this->dayActivitiesImport,
            $this->inclusionsExclusionsImport,
            $this->faqsImport,
            $this->photosImport,
        ];
    }

    public function importedCount(): int
    {
        return array_sum(array_map(fn ($import) => $import->importedCount(), $this->subImports()));
    }

    public function rowErrors(): array
    {
        return array_merge($this->missingSheetErrors, ...array_map(fn ($import) => $import->rowErrors(), $this->subImports()));
    }

    public function skippedCount(): int
    {
        return count($this->rowErrors());
    }

    // Package Core and Itinerary are the two sheets every other one depends on
    // (by package_title lookup), so a missing one is a file-level mistake, not
    // an "empty section" — surface it as a row error instead of the no-op every
    // other sheet gets.
    public function onUnknownSheet($sheetName): void
    {
        if (in_array($sheetName, ['Package Core', 'Itinerary'], true)) {
            $this->missingSheetErrors[] = [
                'row' => $sheetName,
                'errors' => ["The \"{$sheetName}\" sheet is required but missing from the uploaded file."],
            ];
        }
    }
}
