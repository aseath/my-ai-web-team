<?php

declare(strict_types=1);

namespace App\Mail;

/**
 * Development mailer: writes the message to the PHP error log (docker compose logs app).
 * Never use in production; see docs/launch-checklist.md.
 */
final class LogMailer implements Mailer
{
    public function send(string $to, string $subject, string $textBody, ?string $replyTo = null): void
    {
        error_log(sprintf(
            "[LogMailer] To: %s | Reply-To: %s | Subject: %s\n%s",
            $to,
            $replyTo ?? '-',
            $subject,
            $textBody,
        ));
    }
}
