<?php

namespace App\Exports;

use App\Exports\Destinations\InstructionsSheet;
use App\Exports\Destinations\TemplateSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DestinationsTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new TemplateSheet(),
            new InstructionsSheet(),
        ];
    }
}
