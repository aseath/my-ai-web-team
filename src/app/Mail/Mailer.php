<?php

declare(strict_types=1);

namespace App\Mail;

/**
 * Implement this per project for the client's mail provider (SMTP, Microsoft 365, Postmark...),
 * e.g. with symfony/mailer, then swap it in where LogMailer is used in App\Http\Routes.
 */
interface Mailer
{
    public function send(string $to, string $subject, string $textBody, ?string $replyTo = null): void;
}
