<?php

namespace App\Imports\Concerns;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Shared row-level helpers for every module importer: yes/no coercion, best-effort
// image-by-URL download (never fatal — a bad/missing image just leaves the field
// blank), and tenant-scoped name→model resolution for relationship columns. Name
// lookups go through the Eloquent model class itself (not raw queries) so the
// model's own TenantScope global scope filters to the current admin's unique_id
// automatically, exactly like the rest of the app.
trait ResolvesImportValues
{
    protected function toBoolean(mixed $value): bool
    {
        return in_array(strtolower(trim((string) $value)), ['yes', 'y', 'true', '1'], true);
    }

    protected function downloadImage(?string $url, string $folder): ?string
    {
        $url = trim((string) $url);
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        try {
            $response = Http::timeout(10)->get($url);
            if (!$response->successful()) {
                return null;
            }

            $contentType = (string) $response->header('Content-Type');
            if (!str_starts_with($contentType, 'image/')) {
                return null;
            }

            $extension = match (true) {
                str_contains($contentType, 'png') => 'png',
                str_contains($contentType, 'webp') => 'webp',
                str_contains($contentType, 'gif') => 'gif',
                default => 'jpg',
            };

            $path = $folder.'/'.Str::uuid().'.'.$extension;
            Storage::disk('public')->put($path, $response->body());

            return $path;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function findByName(string $modelClass, ?string $name): ?object
    {
        $name = trim((string) $name);
        if ($name === '') {
            return null;
        }

        return $modelClass::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();
    }

    /**
     * @return array{models: array<int, object>, missing: array<int, string>}
     */
    protected function findManyByName(string $modelClass, ?string $namesCsv): array
    {
        $names = collect(explode(',', (string) $namesCsv))
            ->map(fn ($n) => trim($n))
            ->filter(fn ($n) => $n !== '')
            ->values();

        $models = [];
        $missing = [];

        foreach ($names as $name) {
            $model = $this->findByName($modelClass, $name);
            if ($model) {
                $models[] = $model;
            } else {
                $missing[] = $name;
            }
        }

        return ['models' => $models, 'missing' => $missing];
    }
}
