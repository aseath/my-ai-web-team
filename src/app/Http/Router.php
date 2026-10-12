<?php

declare(strict_types=1);

namespace App\Http;

final class Router
{
    /** @var array<string, array<string, callable(): Response>> path => [method => handler] */
    private array $routes = [];

    /**
     * @param callable(): Response $handler
     */
    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    /**
     * @param callable(): Response $handler
     */
    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    /**
     * @param callable(): Response $handler
     */
    public function add(string $method, string $path, callable $handler): void
    {
        $this->routes[self::normalizePath($path)][strtoupper($method)] = $handler;
    }

    public function dispatch(string $method, string $uri): Response
    {
        $method = strtoupper($method);
        $path = self::normalizePath((string) parse_url($uri, PHP_URL_PATH));

        $handlers = $this->routes[$path] ?? null;

        if ($handlers === null) {
            return Response::view('error', [
                'meta' => ['robots' => 'noindex'],
                'title' => 'Page not found',
                'message' => 'The page you requested could not be found.',
            ], 404);
        }

        // HEAD is answered by the GET handler; the body is dropped when sending.
        $lookup = ($method === 'HEAD' && !isset($handlers['HEAD'])) ? 'GET' : $method;

        if (!isset($handlers[$lookup])) {
            $allowed = array_keys($handlers);

            if (in_array('GET', $allowed, true)) {
                $allowed[] = 'HEAD';
            }

            return Response::view('error', [
                'meta' => ['robots' => 'noindex'],
                'title' => 'Method not allowed',
                'message' => 'This request method is not supported for this page.',
            ], 405)->withHeader('Allow', implode(', ', array_unique($allowed)));
        }

        return ($handlers[$lookup])();
    }

    private static function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');

        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
