<?php

declare(strict_types=1);

/**
 * @var string $title
 * @var string $message
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
        <p><?= e($message) ?></p>
        <p><a href="/">Back to home</a></p>
    </main>
</body>
</html>
