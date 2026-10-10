<?php

declare(strict_types=1);

/**
 * @var string $title
 * @var string $phpVersion
 * @var string $environment
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <main>
        <h1><?= e($title) ?></h1>
        <p>Your Vanilla PHP application is running.</p>
        <ul>
            <li>PHP version: <code><?= e($phpVersion) ?></code></li>
            <li>Environment: <code><?= e($environment) ?></code></li>
        </ul>
        <p>Health check: <a href="/health">/health</a></p>
    </main>
</body>
</html>
