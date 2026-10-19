<?php

/**
 * Build the <head> SEO block for the current page context.
 * $ctx keys: title, meta, path (language-neutral), lang, noindex, alternates (['en'=>bool,'ar'=>bool]),
 *            og_image, og_type, jsonld (array of schema objects).
 */
function seo_head(array $ctx): string
{
    $lang = $ctx['lang'];
    $title = plain($ctx['title']);
    $meta = plain($ctx['meta'] ?? '');
    $canonical = url($ctx['path'], $lang, true);
    $alts = $ctx['alternates'] ?? ['en' => true, 'ar' => true];
    $img = $ctx['og_image'] ?? (rtrim(cfg('base_url'), '/') . asset('img/og-default.jpg'));

    $h = [];
    $h[] = '<title>' . e($title) . '</title>';
    if ($meta !== '') $h[] = '<meta name="description" content="' . e($meta) . '">';
    if (!empty($ctx['noindex'])) {
        $h[] = '<meta name="robots" content="noindex, follow">';
    } else {
        $h[] = '<meta name="robots" content="index, follow, max-image-preview:large">';
    }
    $h[] = '<link rel="canonical" href="' . e($canonical) . '">';
    if (empty($ctx['noindex'])) {
        if (!empty($alts['en'])) $h[] = '<link rel="alternate" hreflang="en-SA" href="' . e(url($ctx['path'], 'en', true)) . '">';
        if (!empty($alts['ar'])) $h[] = '<link rel="alternate" hreflang="ar-SA" href="' . e(url($ctx['path'], 'ar', true)) . '">';
        if (!empty($alts['en'])) $h[] = '<link rel="alternate" hreflang="x-default" href="' . e(url($ctx['path'], 'en', true)) . '">';
    }

    // Open Graph + Twitter
    $h[] = '<meta property="og:type" content="' . e($ctx['og_type'] ?? 'website') . '">';
    $h[] = '<meta property="og:site_name" content="DWP Arabia">';
    $h[] = '<meta property="og:title" content="' . e($title) . '">';
    if ($meta !== '') $h[] = '<meta property="og:description" content="' . e($meta) . '">';
    $h[] = '<meta property="og:url" content="' . e($canonical) . '">';
    $h[] = '<meta property="og:image" content="' . e($img) . '">';
    $h[] = '<meta property="og:image:width" content="1200">';
    $h[] = '<meta property="og:image:height" content="630">';
    $h[] = '<meta property="og:locale" content="' . og_locale($lang) . '">';
    $other = $lang === 'ar' ? 'en' : 'ar';
    if (!empty($alts[$other])) $h[] = '<meta property="og:locale:alternate" content="' . og_locale($other) . '">';
    $h[] = '<meta name="twitter:card" content="summary_large_image">';
    $h[] = '<meta name="twitter:title" content="' . e($title) . '">';
    if ($meta !== '') $h[] = '<meta name="twitter:description" content="' . e($meta) . '">';
    $h[] = '<meta name="twitter:image" content="' . e($img) . '">';

    foreach ($ctx['jsonld'] ?? [] as $obj) {
        $h[] = '<script type="application/ld+json">' . json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) . '</script>';
    }
    return implode("\n", $h);
}

/** Breadcrumb trail for a registered page: [[name, neutral path], …] */
function breadcrumb_trail(string $key, string $lang): array
{
    $pages = pages();
    $trail = [];
    $k = $key;
    while ($k !== null && isset($pages[$k])) {
        $trail[] = [$k === 'home' ? t('home', $lang) : page_name($k, $lang), $pages[$k]['path']];
        $k = $pages[$k]['parent'];
    }
    return array_reverse($trail);
}
