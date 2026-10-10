<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Response;

final class HomeController
{
    public function index(): Response
    {
        return Response::view('home', [
            'title' => 'Welcome',
            'phpVersion' => PHP_VERSION,
            'environment' => getenv('APP_ENV') ?: 'production',
        ]);
    }
}
