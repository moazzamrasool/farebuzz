<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesUniqueSlug
{
    // Builds a unique slug for $modelClass from $name, appending -2, -3, ... on collision.
    // Pass $ignoreId when updating an existing record so it doesn't collide with itself.
    protected function generateUniqueSlug(string $modelClass, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 2;

        while (
            $modelClass::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
