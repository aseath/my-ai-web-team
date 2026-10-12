<?php

declare(strict_types=1);

namespace App\Validation;

/**
 * Cheap bot filter for public forms: a hidden honeypot field plus a minimum time to fill the form.
 * Bots that trip it are shown the normal success message so they get no signal.
 */
final class SpamGuard
{
    public const string HONEYPOT_FIELD = 'website';

    private const string SESSION_PREFIX = '_form_rendered_at.';

    private const int MIN_SECONDS = 3;

    public static function markRendered(string $form): void
    {
        $_SESSION[self::SESSION_PREFIX . $form] = time();
    }

    /**
     * @param array<string, mixed> $input
     */
    public static function isSpam(string $form, array $input): bool
    {
        $renderedAt = $_SESSION[self::SESSION_PREFIX . $form] ?? null;

        if (($input[self::HONEYPOT_FIELD] ?? '') !== '') {
            return true;
        }

        return !is_int($renderedAt) || (time() - $renderedAt) < self::MIN_SECONDS;
    }
}
