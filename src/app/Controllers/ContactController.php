<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Actions\SendContactEnquiry;
use App\Http\Response;
use App\Support\Csrf;
use App\Support\Flash;
use App\Validation\ContactFormValidator;
use App\Validation\SpamGuard;

final class ContactController
{
    private const string FORM = 'contact';

    public function __construct(
        private readonly ContactFormValidator $validator,
        private readonly SendContactEnquiry $sendEnquiry,
    ) {
    }

    public function show(): Response
    {
        SpamGuard::markRendered(self::FORM);

        return Response::view('contact', [
            'meta' => [
                'title' => 'Contact us | ' . site('name'),
                'description' => 'Get in touch with ' . site('name') . '.',
            ],
            'errors' => Flash::pull('errors', []),
            'old' => Flash::pull('old', []),
            'sent' => (bool) Flash::pull('sent', false),
        ])->withHeader('Cache-Control', 'no-store');
    }

    public function submit(): Response
    {
        if (!Csrf::isValid($_POST[Csrf::FIELD] ?? null)) {
            Flash::put('errors', ['form' => 'Your session expired. Please try again.']);

            return Response::redirect('/contact');
        }

        // Bots get the normal success page and nothing is stored or sent.
        if (SpamGuard::isSpam(self::FORM, $_POST)) {
            Flash::put('sent', true);

            return Response::redirect('/contact');
        }

        $result = $this->validator->validate($_POST);

        if (!$result->passes()) {
            Flash::put('errors', $result->errors);
            Flash::put('old', $result->data);

            return Response::redirect('/contact#contact-form');
        }

        $this->sendEnquiry->handle(
            $result->data,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
        );

        Flash::put('sent', true);

        return Response::redirect('/contact');
    }
}
