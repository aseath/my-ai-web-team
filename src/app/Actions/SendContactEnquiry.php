<?php

declare(strict_types=1);

namespace App\Actions;

use App\Database\Connection;
use App\Mail\Mailer;
use Throwable;

/**
 * Stores a contact enquiry, then notifies the site owner by email.
 * The enquiry is saved first so a mail failure never loses it.
 */
final class SendContactEnquiry
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly string $recipient,
    ) {
    }

    /**
     * @param array{name: string, email: string, phone: string, message: string} $data
     */
    public function handle(array $data, ?string $ipAddress, ?string $userAgent): void
    {
        $statement = Connection::get()->prepare(
            'INSERT INTO contact_submissions (name, email, phone, message, ip_address, user_agent)
             VALUES (:name, :email, :phone, :message, :ip_address, :user_agent)',
        );

        $statement->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] !== '' ? $data['phone'] : null,
            'message' => $data['message'],
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent !== null ? mb_substr($userAgent, 0, 255) : null,
        ]);

        try {
            $this->mailer->send(
                $this->recipient,
                sprintf('New website enquiry from %s', $data['name']),
                sprintf(
                    "Name: %s\nEmail: %s\nPhone: %s\n\n%s",
                    $data['name'],
                    $data['email'],
                    $data['phone'] !== '' ? $data['phone'] : '-',
                    $data['message'],
                ),
                $data['email'],
            );
        } catch (Throwable $exception) {
            error_log('Contact enquiry saved but notification failed: ' . $exception->getMessage());
        }
    }
}
