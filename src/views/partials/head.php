<?php

declare(strict_types=1);

/**
 * <head> contents. Controllers pass page SEO data as $meta:
 *   title        full <title>, under 60 chars (defaults to $title or the site name)
 *   description  under 160 chars
 *   canonical    absolute URL (defaults to site url + current path)
 *   og_image     path or absolute URL to a 1200x630 image
 *   og_type      'website' (default) or 'article'
 *   robots       e.g. 'noindex' for error and thank-you pages
 *   schema       JSON-LD as a PHP array (single object or ['@graph' => [...]])
 *
 * @var array<string, mixed>|null $meta
 * @var string|null $title
 */

$meta = array_merge([
    'title' => $title ?? site('name'),
    'description' => site('description'),
    'canonical' => site('url') . current_path(),
    'og_image' => site('og_image'),
    'og_type' => 'website',
    'robots' => null,
    'schema' => null,
], $meta ?? []);

$ogImage = $meta['og_image'];

if (is_string($ogImage) && str_starts_with($ogImage, '/')) {
    $ogImage = site('url') . $ogImage;
}
?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($meta['title']) ?></title>
<?php if ($meta['description'] !== '' && $meta['description'] !== null): ?>
    <meta name="description" content="<?= e($meta['description']) ?>">
<?php endif; ?>
<?php if ($meta['robots'] !== null): ?>
    <meta name="robots" content="<?= e($meta['robots']) ?>">
<?php else: ?>
    <link rel="canonical" href="<?= e($meta['canonical']) ?>">
<?php endif; ?>

    <meta property="og:site_name" content="<?= e(site('name')) ?>">
    <meta property="og:locale" content="<?= e(site('locale')) ?>">
    <meta property="og:type" content="<?= e($meta['og_type']) ?>">
    <meta property="og:title" content="<?= e($meta['title']) ?>">
    <meta property="og:url" content="<?= e($meta['canonical']) ?>">
<?php if ($meta['description'] !== '' && $meta['description'] !== null): ?>
    <meta property="og:description" content="<?= e($meta['description']) ?>">
<?php endif; ?>
<?php if (is_string($ogImage) && $ogImage !== ''): ?>
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
<?php endif; ?>

    <link rel="stylesheet" href="/css/tokens.css">
    <link rel="stylesheet" href="/css/app.css">
<?php if (is_array($meta['schema']) && $meta['schema'] !== []): ?>
    <script type="application/ld+json"><?= json_encode(
        ['@context' => 'https://schema.org'] + $meta['schema'],
        JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP,
    ) ?></script>
<?php endif; ?>
