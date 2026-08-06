<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMXPath;

// Dependency-free HTML sanitizer for admin-authored rich-text (CKEditor) output.
// Strips script-execution vectors while leaving normal formatting markup (including
// inline style="" attributes CKEditor relies on for alignment/color) untouched.
class HtmlSanitizer
{
    private const STRIPPED_TAGS = ['script', 'iframe', 'object', 'embed', 'style'];

    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        // NOIMPLIED/NODEFDTD keep DOMDocument from wrapping the fragment in its own
        // <html><body>, so saveHTML() below returns just the original fragment back.
        $dom->loadHTML(
            '<?xml encoding="utf-8" ?>'.$html,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        // Drop the leading UTF-8 encoding hint injected above — loadHTML() keeps it
        // as a real processing-instruction node, which saveHTML() would otherwise
        // echo straight back into the stored content.
        foreach (iterator_to_array($dom->childNodes) as $node) {
            if ($node->nodeType === XML_PI_NODE) {
                $dom->removeChild($node);
            }
        }

        foreach (self::STRIPPED_TAGS as $tag) {
            $nodes = $dom->getElementsByTagName($tag);
            for ($i = $nodes->length - 1; $i >= 0; $i--) {
                $node = $nodes->item($i);
                $node->parentNode?->removeChild($node);
            }
        }

        $xpath = new DOMXPath($dom);
        foreach ($xpath->query('//*') as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }
            foreach (iterator_to_array($element->attributes ?? []) as $attribute) {
                $name = strtolower($attribute->name);
                $value = trim($attribute->value);
                $isEventHandler = str_starts_with($name, 'on');
                $isJsUrl = in_array($name, ['href', 'src'], true) && stripos($value, 'javascript:') === 0;
                if ($isEventHandler || $isJsUrl) {
                    $element->removeAttribute($attribute->name);
                }
            }
        }

        return trim($dom->saveHTML());
    }
}
