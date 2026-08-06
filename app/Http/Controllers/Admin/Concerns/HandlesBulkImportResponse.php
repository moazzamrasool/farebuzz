<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Exports\ImportErrorsExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

// Shared by every module's bulk-upload controller: turns an importer's tallies
// into the {imported, skipped, errors, error_report_url} JSON contract the
// FBBulkUpload modal expects, and streams the one-time error report back.
// The error file path is namespaced by the current admin's own unique_id, so
// a token alone can never be used to read another tenant's report.
trait HandlesBulkImportResponse
{
    protected function bulkImportResponse(int $imported, array $rowErrors): JsonResponse
    {
        $errorReportUrl = null;

        if (!empty($rowErrors)) {
            $token = (string) Str::uuid();
            $path = $this->importErrorReportPath($token);

            Excel::store(new ImportErrorsExport($rowErrors), $path, 'local');

            $errorReportUrl = route($this->bulkUploadErrorRouteName(), $token);
        }

        return response()->json([
            'success' => true,
            'imported' => $imported,
            'skipped' => count($rowErrors),
            'errors' => $rowErrors,
            'error_report_url' => $errorReportUrl,
        ]);
    }

    protected function streamImportErrorReport(string $token)
    {
        $path = $this->importErrorReportPath($token);

        abort_unless(Storage::disk('local')->exists($path), 404);

        return response()->download(Storage::disk('local')->path($path))->deleteFileAfterSend(true);
    }

    private function importErrorReportPath(string $token): string
    {
        $uniqueId = Auth::guard('admin')->user()->unique_id;

        return "import-errors/{$uniqueId}/{$token}.xlsx";
    }

    abstract protected function bulkUploadErrorRouteName(): string;
}
