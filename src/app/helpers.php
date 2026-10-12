<?php

declare(strict_types=1);

use App\Support\Csrf;
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

if (!function_exists('site')) {
    /**
     * Read site settings from config/site.php, e.g. site('name') or site('legal.company_number').
     */
    function site(?string $key = null, mixed $default = null): mixed
    {
        static $config = null;
        $config ??= require dirname(__DIR__) . '/config/site.php';

        if ($key === null) {
            return $config;
        }

        $value = $config;

        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Hidden CSRF input for POST forms. Already escaped; output it raw.
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="' . Csrf::FIELD . '" value="' . e(Csrf::token()) . '">';
    }
}

if (!function_exists('current_path')) {
    function current_path(): string
    {
        $path = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        return $path === '' ? '/' : $path;
    }
}
