<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Response;

/**
 * Simple content pages with no data of their own.
 */
final class PageController
{
    public function privacy(): Response
    {
        return Response::view('pages/privacy', [
            'meta' => [
                'title' => 'Privacy and cookies | ' . site('name'),
                'description' => 'How ' . site('name') . ' collects, uses and protects your personal data.',
            ],
        ]);
    }
}
