<?php

declare(strict_types=1);

use App\Support\View;

if (!function_exists('e')) {
    /**
     * Escape a value for safe output in HTML (text and attribute contexts).
     */
    function e(mixed $value): string
    {
        return View::escape($value);
    }
}
