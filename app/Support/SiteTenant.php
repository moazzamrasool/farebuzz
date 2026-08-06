<?php

namespace App\Support;

class SiteTenant
{
    /**
     * The unique_id of the single company the public frontend serves.
     */
    public static function id(): ?string
    {
        return config('app.company_unique_id');
    }
}
