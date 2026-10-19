<?php
/** @var array $page @var string $main */
$lang = $page['lang'];
$dir = $lang === 'ar' ? 'rtl' : 'ltr';
$jsonld = $page['jsonld'];
?><!doctype html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?= seo_head([
    'title' => $page['title'], 'meta' => $page['meta'] ?? '', 'path' => $page['path'], 'lang' => $lang,
    'noindex' => $page['noindex'] ?? false, 'alternates' => $page['alternates'], 'jsonld' => $jsonld,
    'og_type' => $page['og_type'] ?? 'website',
]) ?>

<?php if ($lang === 'ar'): ?>
<link rel="preload" href="<?= e(rtrim(cfg('base_path'), '/') . '/assets/fonts/ibm-plex-sans-arabic-arabic-6fbb1d.woff2') ?>" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?= e(rtrim(cfg('base_path'), '/') . '/assets/fonts/ibm-plex-sans-arabic-arabic-d381fd.woff2') ?>" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?= e(rtrim(cfg('base_path'), '/') . '/assets/fonts/inter-latin-1ab1ad.woff2') ?>" as="font" type="font/woff2" crossorigin>
<?php else: ?>
<link rel="preload" href="<?= e(rtrim(cfg('base_path'), '/') . '/assets/fonts/inter-latin-1ab1ad.woff2') ?>" as="font" type="font/woff2" crossorigin>
<?php endif; ?>
<link rel="stylesheet" href="<?= e(asset('css/site.css')) ?>">
<link rel="icon" href="<?= e(asset('img/favicon-48.png')) ?>" sizes="48x48" type="image/png">
<link rel="apple-touch-icon" href="<?= e(asset('img/favicon-180.png')) ?>">
<script>
window.dataLayer = window.dataLayer || [];
try { var c = localStorage.getItem('dwp_consent_v2'); if (c) document.documentElement.setAttribute('data-consent', c); } catch (e) {}
window.DWP = <?= json_encode([
    'lang' => $lang,
    'gtm' => cfg('gtm_id'),
    'path' => strtok($_SERVER['REQUEST_URI'] ?? '/', '?'),
], JSON_UNESCAPED_SLASHES) ?>;
</script>
<?php if (cfg('turnstile_site_key')): ?>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<?php endif; ?>
</head>
<body id="top" class="<?= e($page['body_class']) ?><?= show_markers() ? ' is-staging' : '' ?><?= ' has-hero' . (($page['body'] === null && in_array($page['sections'][0]['type'] ?? '', ['hero', 'product_hero', 'ind_hero'], true)) ? '' : ' top-light') /* menu always sits inside the first section; light first sections get the dark menu until scrolled */ ?>">
<a class="skip-link" href="#main"><?= e(t('skip')) ?></a>
<?php if (show_markers()): ?>
<div class="staging-bar" role="note">STAGING: <mark class="confirm">[CONFIRM]</mark> = DWP to supply · <span class="ar-review">AR-REVIEW</span> = Arabic to review</div>
<?php endif; ?>
<?= view('partials/header', ['page' => $page]) ?>
<main id="main">
<?php if (count($page['trail']) > 1 && (!in_array($page['sections'][0]['type'] ?? '', ['hero', 'sap_hero', 'product_hero', 'ind_hero'], true) || $page['body'] !== null)): ?>
<?= view('partials/breadcrumbs', ['trail' => $page['trail']]) ?>
<?php endif; ?>
<?= $main ?>
</main>
<?= view('partials/footer', ['page' => $page]) ?>
<?= view('partials/whatsapp-float', ['page' => $page]) ?>
<?= view('partials/cookie-banner') ?>
<script src="<?= e(asset('js/site.js')) ?>" defer></script>
</body>
</html>
