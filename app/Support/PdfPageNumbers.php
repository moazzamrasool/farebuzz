<?php

namespace App\Support;

use Barryvdh\DomPDF\PDF;

// Draws "Page X of Y" onto every page's footer bar. dompdf has no CSS-level
// page-number substitution — the {PAGE_NUM}/{PAGE_COUNT} token replacement in
// dompdf's own Renderer\Text is dead code (commented out in dompdf core), so
// literal "{PAGE_NUM}"/"{PAGE_COUNT}" text in a Blade view renders as-is rather
// than being replaced. The only mechanism that actually works is
// Canvas::page_text() — a low-level draw call, not something expressible in
// the Blade template's HTML — which must run after render() but before the
// PDF is streamed/downloaded/output.
class PdfPageNumbers
{
    public static function apply(PDF $pdf): PDF
    {
        $pdf->render();

        $canvas = $pdf->getDomPDF()->getCanvas();
        $font = $pdf->getDomPDF()->getFontMetrics()->getFont('DejaVu Sans', 'normal');

        // Right-hand side of the navy footer bar (see partials/_footer.blade.php),
        // roughly vertically centered in its ~34px/25pt height.
        $canvas->page_text(
            $canvas->get_width() - 150,
            $canvas->get_height() - 18,
            'Page {PAGE_NUM} of {PAGE_COUNT}',
            $font,
            8.5,
            [0.7255, 0.7686, 0.8392] // #b9c4d6, matches .brand-footer .page-count
        );

        return $pdf;
    }
}
