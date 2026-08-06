<?php

namespace App\Exports;

use App\Exports\Hotels\InstructionsSheet;
use App\Exports\Hotels\TemplateSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class HotelsTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new TemplateSheet(),
            new InstructionsSheet(),
        ];
    }
}
