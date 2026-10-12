<?php

declare(strict_types=1);

use App\Http\Response;
use App\Http\Router;
use App\Http\Routes;

$isLocal = getenv('APP_ENV') === 'local';

// Error handling: show errors only in local development, always log.
error_reporting(E_ALL);
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
ini_set('log_errors', '1');

// Security headers.
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Cross-Origin-Opener-Policy: same-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header(
    "Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; "
    . "img-src 'self' data:; font-src 'self'; connect-src 'self'; object-src 'none'; "
    . "base-uri 'self'; form-action 'self'; frame-ancestors 'none'"
);
header_remove('X-Powered-By');

$autoload = dirname(__DIR__) . '/vendor/autoload.php';

if (!is_file($autoload)) {
    error_log('Composer autoloader missing: run "composer install" inside the app container.');
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'The application is not available right now.';
    exit;
}

require $autoload;

// Convert PHP warnings/notices into exceptions so nothing fails silently.
set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if ((error_reporting() & $severity) === 0) {
        return false;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

try {
    session_start([
        'name' => 'app_session',
        'cookie_httponly' => true,
        'cookie_secure' => true,
        'cookie_samesite' => 'Lax',
        'cookie_path' => '/',
        'use_strict_mode' => true,
        'use_only_cookies' => true,
        'use_trans_sid' => false,
    ]);

    $router = new Router();
    Routes::register($router);

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $response = $router->dispatch($method, $_SERVER['REQUEST_URI'] ?? '/');
    $response->send(strtoupper($method) !== 'HEAD');
} catch (Throwable $exception) {
    error_log(sprintf(
        'Unhandled %s: %s in %s:%d%s%s',
        $exception::class,
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        PHP_EOL,
        $exception->getTraceAsString(),
    ));

    try {
        Response::view('error', [
            'meta' => ['robots' => 'noindex'],
            'title' => 'Something went wrong',
            'message' => 'An unexpected error occurred. Please try again later.',
        ], 500)->send();
    } catch (Throwable $renderException) {
        error_log('Failed to render error page: ' . $renderException->getMessage());

        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
        }

        echo 'An unexpected error occurred.';
    }
}
