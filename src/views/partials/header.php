<?php

declare(strict_types=1);

$currentPath = current_path();
?>
    <header class="site-header">
        <div class="container site-header__inner">
            <a class="site-header__brand" href="/"><?= e(site('name')) ?></a>
            <nav class="site-nav" aria-label="Main">
                <ul>
<?php foreach (site('nav', []) as $item): ?>
                    <li><a href="<?= e($item['href']) ?>"<?= $item['href'] === $currentPath ? ' aria-current="page"' : '' ?>><?= e($item['label']) ?></a></li>
<?php endforeach; ?>
                </ul>
            </nav>
        </div>
    </header>
