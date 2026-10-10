<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use RuntimeException;

/**
 * Lazily creates a single PDO connection configured from environment variables.
 */
final class Connection
{
    private static ?PDO $pdo = null;

    public static function get(): PDO
    {
        return self::$pdo ??= self::create();
    }

    private static function create(): PDO
    {
        $host = self::env('DB_HOST');
        $port = self::env('DB_PORT', '3306');
        $database = self::env('DB_DATABASE');

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);

        return new PDO($dsn, self::env('DB_USERNAME'), self::env('DB_PASSWORD', ''), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]);
    }

    private static function env(string $key, ?string $default = null): string
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            if ($default === null) {
                throw new RuntimeException(sprintf('Missing required environment variable "%s".', $key));
            }

            return $default;
        }

        return $value;
    }
}
