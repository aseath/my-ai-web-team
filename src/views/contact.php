<?php

declare(strict_types=1);

use App\Validation\SpamGuard;

/**
 * Reference form pattern: CSRF, honeypot, server-side validation, PRG, accessible errors.
 *
 * @var array<string, string> $errors
 * @var array<string, string> $old
 * @var bool $sent
 */

$field = static fn (string $name): string => $old[$name] ?? '';
$invalid = static fn (string $name): string => isset($errors[$name])
    ? ' aria-invalid="true" aria-describedby="' . $name . '-error"'
    : '';
?>
        <section class="section container">
            <h1>Contact us</h1>
            <p>[COPY NEEDED: contact intro]</p>

<?php if ($sent): ?>
            <div class="notice notice--success" role="status">
                <p>Thank you, your message has been sent. We'll be in touch soon.</p>
            </div>
<?php endif; ?>

<?php if ($errors !== []): ?>
            <div class="notice notice--error" role="alert">
                <p><?= e($errors['form'] ?? 'Please correct the highlighted fields and try again.') ?></p>
            </div>
<?php endif; ?>

            <form id="contact-form" class="form" method="post" action="/contact" novalidate>
                <?= csrf_field() ?>

                <div class="form__hp" aria-hidden="true">
                    <label for="<?= e(SpamGuard::HONEYPOT_FIELD) ?>">Leave this field empty</label>
                    <input type="text" id="<?= e(SpamGuard::HONEYPOT_FIELD) ?>" name="<?= e(SpamGuard::HONEYPOT_FIELD) ?>" tabindex="-1" autocomplete="off">
                </div>

                <div class="form__field">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" autocomplete="name" required maxlength="150" value="<?= e($field('name')) ?>"<?= $invalid('name') ?>>
<?php if (isset($errors['name'])): ?>
                    <p class="form__error" id="name-error"><?= e($errors['name']) ?></p>
<?php endif; ?>
                </div>

                <div class="form__field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" autocomplete="email" required maxlength="254" value="<?= e($field('email')) ?>"<?= $invalid('email') ?>>
<?php if (isset($errors['email'])): ?>
                    <p class="form__error" id="email-error"><?= e($errors['email']) ?></p>
<?php endif; ?>
                </div>

                <div class="form__field">
                    <label for="phone">Phone <span class="form__optional">(optional)</span></label>
                    <input type="tel" id="phone" name="phone" autocomplete="tel" maxlength="40" value="<?= e($field('phone')) ?>"<?= $invalid('phone') ?>>
<?php if (isset($errors['phone'])): ?>
                    <p class="form__error" id="phone-error"><?= e($errors['phone']) ?></p>
<?php endif; ?>
                </div>

                <div class="form__field">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="6" required maxlength="5000"<?= $invalid('message') ?>><?= e($field('message')) ?></textarea>
<?php if (isset($errors['message'])): ?>
                    <p class="form__error" id="message-error"><?= e($errors['message']) ?></p>
<?php endif; ?>
                </div>

                <p class="form__privacy">We'll only use your details to reply to your enquiry. See our <a href="/privacy">privacy notice</a>.</p>

                <button type="submit" class="button">Send message</button>
            </form>
        </section>
