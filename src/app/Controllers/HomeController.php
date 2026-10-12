<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Response;

final class HomeController
{
    public function index(): Response
    {
        return Response::view('home', [
            'meta' => [
                'title' => site('name'),
                'description' => site('description'),
                // Replace with the right type for the client (e.g. LocalBusiness subtype) - see seo-agent.
                'schema' => [
                    '@type' => 'Organization',
                    'name' => site('name'),
                    'url' => site('url'),
                ],
            ],
            'phpVersion' => PHP_VERSION,
            'environment' => getenv('APP_ENV') ?: 'production',
        ]);
    }
}
