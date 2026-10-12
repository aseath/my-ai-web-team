<?php

declare(strict_types=1);

namespace App\Validation;

final readonly class ValidationResult
{
    /**
     * @param array<string, string> $data   cleaned, validated values
     * @param array<string, string> $errors field => message
     */
    public function __construct(
        public array $data,
        public array $errors,
    ) {
    }

    public function passes(): bool
    {
        return $this->errors === [];
    }
}
