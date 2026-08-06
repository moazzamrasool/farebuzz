<?php

namespace App\Exports;

use App\Exports\HolidayPackages\DayActivitiesTemplateSheet;
use App\Exports\HolidayPackages\DayHotelsTemplateSheet;
use App\Exports\HolidayPackages\FaqsTemplateSheet;
use App\Exports\HolidayPackages\InclusionsExclusionsTemplateSheet;
use App\Exports\HolidayPackages\InstructionsSheet;
use App\Exports\HolidayPackages\ItineraryTemplateSheet;
use App\Exports\HolidayPackages\PackageCoreTemplateSheet;
use App\Exports\HolidayPackages\PhotosTemplateSheet;
use App\Exports\HolidayPackages\RoomTypesTemplateSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class HolidayPackagesTemplateExport implements WithMultipleSheets
{
    // Sheet titles here must match the keys HolidayPackagesImport::sheets()
    // uses exactly — that's how each uploaded tab gets routed to the right
    // sub-importer.
    public function sheets(): array
    {
        return [
            new PackageCoreTemplateSheet(),
            new ItineraryTemplateSheet(),
            new RoomTypesTemplateSheet(),
            new DayHotelsTemplateSheet(),
            new DayActivitiesTemplateSheet(),
            new InclusionsExclusionsTemplateSheet(),
            new FaqsTemplateSheet(),
            new PhotosTemplateSheet(),
            new InstructionsSheet(),
        ];
    }
}
