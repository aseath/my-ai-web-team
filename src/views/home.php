<?php

declare(strict_types=1);

/**
 * Placeholder home page; replaced by dev-agent from docs/design-spec.md and docs/copy/home.md.
 *
 * @var string $phpVersion
 * @var string $environment
 */
?>
        <section class="section container">
            <h1><?= e(site('name')) ?></h1>
            <p>[COPY NEEDED: home page]. The template is running.</p>
            <ul>
                <li>PHP version: <code><?= e($phpVersion) ?></code></li>
                <li>Environment: <code><?= e($environment) ?></code></li>
                <li>Health check: <a href="/health">/health</a></li>
            </ul>
        </section>
