<?php

namespace App\Exceptions;

// Thrown by App\Services\AiPackageService for any failure a controller should turn
// into a friendly JSON error instead of a 500 — timeouts, non-2xx provider responses,
// quota-exceeded, or AI output that still isn't valid JSON after one retry.
class AiGenerationException extends \RuntimeException
{
}
