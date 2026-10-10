<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Connection;
use App\Http\Response;
use Throwable;

final class HealthController
{
    public function show(): Response
    {
        try {
            $statement = Connection::get()->prepare('SELECT 1');
            $statement->execute();
            $databaseOk = (int) $statement->fetchColumn() === 1;
        } catch (Throwable $exception) {
            error_log('Health check database failure: ' . $exception->getMessage());
            $databaseOk = false;
        }

        return Response::json([
            'status' => $databaseOk ? 'ok' : 'degraded',
            'database' => $databaseOk ? 'up' : 'down',
        ], $databaseOk ? 200 : 503)->withHeader('Cache-Control', 'no-store');
    }
}
