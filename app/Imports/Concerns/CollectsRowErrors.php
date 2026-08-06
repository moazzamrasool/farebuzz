<?php

namespace App\Imports\Concerns;

trait CollectsRowErrors
{
    protected int $importedCount = 0;

    protected array $rowErrors = [];

    protected function markImported(): void
    {
        $this->importedCount++;
    }

    protected function addRowError(int|string $row, string|array $errors): void
    {
        $this->rowErrors[] = [
            'row' => $row,
            'errors' => is_array($errors) ? array_values($errors) : [$errors],
        ];
    }

    public function importedCount(): int
    {
        return $this->importedCount;
    }

    public function rowErrors(): array
    {
        return $this->rowErrors;
    }

    public function skippedCount(): int
    {
        return count($this->rowErrors);
    }
}
