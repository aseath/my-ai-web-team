<?php

declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;
use Throwable;

final class View
{
    private const string VIEWS_PATH = __DIR__ . '/../../views';

    public static function escape(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            $value = $value ? '1' : '0';
        }

        if (!is_scalar($value) && !$value instanceof \Stringable) {
            throw new InvalidArgumentException('Only scalar or Stringable values can be escaped.');
        }

        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Render a template from the views directory and return the HTML.
     *
     * @param array<string, mixed> $data
     */
    public static function render(string $template, array $data = []): string
    {
        if (preg_match('/^[a-z0-9_\-]+(\/[a-z0-9_\-]+)*$/i', $template) !== 1) {
            throw new InvalidArgumentException('Invalid view name.');
        }

        $file = self::VIEWS_PATH . '/' . $template . '.php';

        if (!is_file($file)) {
            throw new InvalidArgumentException(sprintf('View "%s" not found.', $template));
        }

        $level = ob_get_level();
        ob_start();

        try {
            (static function (string $__file, array $__data): void {
                extract($__data, EXTR_SKIP);
                require $__file;
            })($file, $data);

            return (string) ob_get_clean();
        } catch (Throwable $exception) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $exception;
        }
    }
}
