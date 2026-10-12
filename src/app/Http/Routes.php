<?php

declare(strict_types=1);

namespace App\Http;

use App\Actions\SendContactEnquiry;
use App\Controllers\ContactController;
use App\Controllers\HealthController;
use App\Controllers\HomeController;
use App\Controllers\PageController;
use App\Mail\LogMailer;
use App\Validation\ContactFormValidator;

final class Routes
{
    public static function register(Router $router): void
    {
        $contact = new ContactController(
            new ContactFormValidator(),
            // Swap LogMailer for a real transport per project (see App\Mail\Mailer).
            new SendContactEnquiry(new LogMailer(), (string) site('contact_email')),
        );

        $router->get('/', [new HomeController(), 'index']);
        $router->get('/contact', [$contact, 'show']);
        $router->post('/contact', [$contact, 'submit']);
        $router->get('/privacy', [new PageController(), 'privacy']);
        $router->get('/health', [new HealthController(), 'show']);
    }
}
