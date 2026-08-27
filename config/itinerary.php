<?php

// Switches which Blade template ItineraryService/QuotationService render.
// 'classic' (default) is today's plain table-based PDF, unchanged. 'premium'
// is the new dynamic brochure-style template. Flip via .env and
// `php artisan config:clear` — no deploy needed, and it reverts just as fast.
return [
    'pdf_template' => env('ITINERARY_PDF_TEMPLATE', 'classic'),
];
