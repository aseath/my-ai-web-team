<?php

declare(strict_types=1);

namespace App\Support;

/**
 * One-request session storage for Post/Redirect/Get (errors, old input, status messages).
 */
final class Flash
{
    private const string SESSION_KEY = '_flash';

    public static function put(string $key, mixed $value): void
    {
        $_SESSION[self::SESSION_KEY][$key] = $value;
    }

    public static function pull(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION[self::SESSION_KEY][$key] ?? $default;
        unset($_SESSION[self::SESSION_KEY][$key]);

        return $value;
    }
}
