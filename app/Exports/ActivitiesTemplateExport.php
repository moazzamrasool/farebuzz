<?php

namespace App\Exports;

use App\Exports\Activities\InstructionsSheet;
use App\Exports\Activities\TemplateSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ActivitiesTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new TemplateSheet(),
            new InstructionsSheet(),
        ];
    }
}
