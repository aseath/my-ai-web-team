<?php

declare(strict_types=1);

namespace App\Validation;

final class ContactFormValidator
{
    /**
     * @param array<string, mixed> $input raw $_POST
     */
    public function validate(array $input): ValidationResult
    {
        $data = [
            'name' => self::clean($input['name'] ?? ''),
            'email' => self::clean($input['email'] ?? ''),
            'phone' => self::clean($input['phone'] ?? ''),
            'message' => trim(is_string($input['message'] ?? null) ? $input['message'] : ''),
        ];

        $errors = [];

        if ($data['name'] === '') {
            $errors['name'] = 'Please enter your name.';
        } elseif (mb_strlen($data['name']) > 150) {
            $errors['name'] = 'Your name must be 150 characters or fewer.';
        }

        if ($data['email'] === '') {
            $errors['email'] = 'Please enter your email address.';
        } elseif (mb_strlen($data['email']) > 254 || filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'Please enter a valid email address, like name@example.com.';
        }

        if ($data['phone'] !== '' && preg_match('/^[0-9 +()\-]{7,40}$/', $data['phone']) !== 1) {
            $errors['phone'] = 'Please enter a valid phone number, or leave it blank.';
        }

        if ($data['message'] === '') {
            $errors['message'] = 'Please enter your message.';
        } elseif (mb_strlen($data['message']) > 5000) {
            $errors['message'] = 'Your message must be 5,000 characters or fewer.';
        }

        return new ValidationResult($data, $errors);
    }

    /**
     * Single-line field: trims and strips control characters (prevents header injection via Reply-To).
     */
    private static function clean(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }

        return trim((string) preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value));
    }
}
