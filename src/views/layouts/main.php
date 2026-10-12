<?php

declare(strict_types=1);

/**
 * Shared page layout. Every page view is rendered into $content.
 *
 * @var string $content rendered page HTML (already escaped by the page view)
 */
?>
<!doctype html>
<html lang="en-GB">
<head>
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body>
    <a class="skip-link" href="#main">Skip to main content</a>
<?php require __DIR__ . '/../partials/header.php'; ?>
    <main id="main" tabindex="-1">
<?= $content ?>
    </main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
