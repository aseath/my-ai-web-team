<?php

declare(strict_types=1);

$legal = site('legal', []);
?>
    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?= e(date('Y')) ?> <?= e($legal['legal_name'] !== '' ? $legal['legal_name'] : site('name')) ?></p>
<?php if ($legal['company_number'] !== ''): ?>
            <p>
                Registered in <?= e($legal['registered_in']) ?>, company number <?= e($legal['company_number']) ?>.
<?php if ($legal['registered_office'] !== ''): ?>
                Registered office: <?= e($legal['registered_office']) ?>.
<?php endif; ?>
            </p>
<?php endif; ?>
            <ul class="site-footer__links">
                <li><a href="/privacy">Privacy and cookies</a></li>
                <li><a href="/contact">Contact</a></li>
            </ul>
        </div>
    </footer>
