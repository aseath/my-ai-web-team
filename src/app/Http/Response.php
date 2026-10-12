<?php

declare(strict_types=1);

namespace App\Http;

use App\Support\View;

final class Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        private readonly string $body = '',
        private readonly int $status = 200,
        private readonly array $headers = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function view(
        string $template,
        array $data = [],
        int $status = 200,
        ?string $layout = View::DEFAULT_LAYOUT,
    ): self {
        return new self(
            View::render($template, $data, $layout),
            $status,
            ['Content-Type' => 'text/html; charset=UTF-8'],
        );
    }

    /**
     * 303 See Other: the correct status after a successful POST (Post/Redirect/Get).
     */
    public static function redirect(string $location, int $status = 303): self
    {
        return new self('', $status, ['Location' => $location]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function json(array $data, int $status = 200): self
    {
        return new self(
            json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            $status,
            ['Content-Type' => 'application/json; charset=UTF-8'],
        );
    }

    public function withHeader(string $name, string $value): self
    {
        return new self($this->body, $this->status, [...$this->headers, $name => $value]);
    }

    public function status(): int
    {
        return $this->status;
    }

    public function send(bool $includeBody = true): void
    {
        if (!headers_sent()) {
            http_response_code($this->status);

            foreach ($this->headers as $name => $value) {
                header($name . ': ' . $value);
            }
        }

        if ($includeBody) {
            echo $this->body;
        }
    }
}
