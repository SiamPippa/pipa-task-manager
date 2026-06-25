<?php

namespace App\Support;

class HtmlSanitizer
{
    private const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><ul><ol><li><a><h1><h2><h3><h4><blockquote><code><pre>';

    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        $cleaned = strip_tags($html, self::ALLOWED_TAGS);

        return trim($cleaned) === '' ? null : $cleaned;
    }
}
