<?php

declare(strict_types=1);

namespace App\Http;

use App\Controllers\HealthController;
use App\Controllers\HomeController;

final class Routes
{
    public static function register(Router $router): void
    {
        $router->get('/', [new HomeController(), 'index']);
        $router->get('/health', [new HealthController(), 'show']);
    }
}
