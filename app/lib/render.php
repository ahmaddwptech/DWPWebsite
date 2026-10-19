<?php

/** Render a view file with variables; returns HTML. */
function view(string $name, array $vars = []): string
{
    extract($vars, EXTR_SKIP);
    ob_start();
    include APP_DIR . '/views/' . $name . '.php';
    return ob_get_clean();
}

/** Render a list of content sections. */
function render_sections(array $sections, array $page): string
{
    $html = '';
    foreach ($sections as $i => $s) {
        $type = $s['type'] ?? 'text';
        $file = APP_DIR . '/views/sections/' . $type . '.php';
        if (!is_file($file)) {
            $html .= is_staging() ? '<!-- unknown section type: ' . e($type) . ' -->' : '';
            continue;
        }
        $out = view('sections/' . $type, ['s' => $s, 'page' => $page, 'index' => $i]);
        if (!show_markers() && section_is_empty($out)) continue;
        $html .= $out;
    }
    return $html;
}

/** True when a rendered section has nothing but its heading — e.g. its body was only [CONFIRM] placeholders, which production hides. */
function section_is_empty(string $html): bool
{
    if (trim($html) === '' || preg_match('~<(img|form|video|iframe|table|details|button|input|a)\b~i', $html)) return trim($html) === '';
    $body = preg_replace(['~<h[1-3]\b.*?</h[1-3]>~is', '~<p class="eyebrow">.*?</p>~is', '~<svg\b.*?</svg>~is'], '', $html);
    return mb_strlen(trim(preg_replace('~\s+~u', ' ', html_entity_decode(strip_tags($body), ENT_QUOTES)))) < 3;
}

/**
 * Render a full page.
 * $page: ['key','path','lang','title','meta','h1','noindex','alternates','jsonld','trail','sections','interest','name','body_class','og_type']
 */
function render_page(array $page): void
{
    $page += ['alternates' => ['en' => true, 'ar' => true], 'trail' => [], 'jsonld' => [], 'sections' => [], 'body' => null, 'body_class' => '', 'interest' => 'sap'];
    if ($page['trail'] === [] && isset(pages()[$page['key']])) {
        $page['trail'] = breadcrumb_trail($page['key'], $page['lang']);
    }
    $GLOBALS['current_page'] = $page;
    $main = $page['body'] ?? render_sections($page['sections'], $page);
    header('Content-Type: text/html; charset=utf-8');
    if (!empty($page['noindex'])) header('X-Robots-Tag: noindex');
    echo view('layout', ['page' => $page, 'main' => $main]);
}

function render_404(?string $lang = null): void
{
    http_response_code(404);
    $lang = $lang ?? lang();
    render_page([
        'key' => '404', 'path' => '/', 'lang' => $lang, 'noindex' => true,
        'title' => t('not_found.title'), 'meta' => '', 'h1' => t('not_found.h1'),
        'alternates' => ['en' => false, 'ar' => false],
        'body' => view('partials/not-found'),
    ]);
}

function img(array $im, bool $lazy = true, string $class = ''): string
{
    if (empty($im['src'])) return '';
    $src = $im['src'];
    if (!asset_exists($src)) {
        // Missing asset: show a labelled placeholder in staging, nothing in production.
        return show_markers() ? '<div class="img-missing ' . e($class) . '" style="aspect-ratio:' . (int) ($im['w'] ?? 16) . '/' . (int) ($im['h'] ?? 9) . '"><span>' . e($im['alt'] ?? $src) . '</span></div>' : '';
    }
    return sprintf(
        '<img src="%s"%s alt="%s" width="%d" height="%d"%s%s>',
        e(asset($src)), responsive_srcset($src, $im['sizes'] ?? '(min-width: 1000px) 50vw, 100vw'),
        e(plain($im['alt'] ?? '')), (int) ($im['w'] ?? 0), (int) ($im['h'] ?? 0),
        $lazy ? ' loading="lazy" decoding="async"' : ' fetchpriority="high"',
        $class ? ' class="' . e($class) . '"' : ''
    );
}

/** Small (800px) variant of an image if one exists: img/x.webp -> img/x-sm.webp */
function small_variant(string $src): ?string
{
    $sm = preg_replace('~\.webp$~', '-sm.webp', $src);
    return ($sm !== $src && asset_exists($sm)) ? $sm : null;
}

/** srcset/sizes attributes so phones download the 800px version. */
function responsive_srcset(string $src, string $sizes): string
{
    $sm = small_variant($src);
    if (!$sm) return '';
    $wFull = @getimagesize(ROOT_DIR . '/public/assets/' . $src)[0] ?? 0;
    $wSm = @getimagesize(ROOT_DIR . '/public/assets/' . $sm)[0] ?? 0;
    if (!$wFull || !$wSm || $wSm >= $wFull) return '';
    return ' srcset="' . e(asset($sm)) . ' ' . $wSm . 'w, ' . e(asset($src)) . ' ' . $wFull . 'w" sizes="' . e($sizes) . '"';
}

/** Button/link: [label, href, style] */
function button(array $b, string $extra = ''): string
{
    [$label, $href] = $b;
    $style = $b[2] ?? 'primary';
    if ($style === 'whatsapp' || $href === 'whatsapp') {
        $link = wa_link(sprintf(t('wa_message'), page_name($GLOBALS['current_page']['key'] ?? 'home', lang())));
        $href = $link ?? url('/contact/');
        return '<a class="btn btn-whatsapp ' . e($extra) . '" href="' . e($href) . '"' . ($link ? ' target="_blank" rel="noopener"' : '') . ' data-track="whatsapp_click">' . icon('whatsapp') . '<span>' . rich($label) . '</span></a>';
    }
    return '<a class="btn btn-' . e($style) . ' ' . e($extra) . '" href="' . e(url($href)) . '">' . rich($label) . '</a>';
}

function paragraphs($body): string
{
    $out = '';
    foreach ((array) $body as $p) $out .= '<p>' . rich($p) . '</p>';
    return $out;
}

function section_head(array $s, string $tag = 'h2'): string
{
    $h = '';
    if (!empty($s['eyebrow'])) $h .= '<p class="eyebrow">' . rich($s['eyebrow']) . '</p>';
    if (!empty($s['h2'])) $h .= "<{$tag}>" . rich($s['h2']) . "</{$tag}>";
    if (!empty($s['intro'])) $h .= '<div class="lead">' . paragraphs($s['intro']) . '</div>';
    return $h ? '<header class="section-head">' . $h . '</header>' : '';
}

function icon(string $name): string
{
    static $icons = [
        'whatsapp' => '<path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2a8.2 8.2 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8s-.4-.1-.6.1-.7.8-.8 1-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6a2.7 2.7 0 0 0 1.8-1.2 2.2 2.2 0 0 0 .1-1.3c0-.1-.2-.2-.5-.3Z" fill="currentColor"/>',
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
        'mail' => '<rect x="2" y="4" width="20" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="m22 7-10 6L2 7" fill="none" stroke="currentColor" stroke-width="2"/>',
        'pin' => '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0Z" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="10" r="3" fill="none" stroke="currentColor" stroke-width="2"/>',
        'clock' => '<circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/><path d="M12 6v6l4 2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        'check' => '<path d="M20 6 9 17l-5-5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>',
        'arrow' => '<path d="M5 12h14M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
        'chevron' => '<path d="m6 9 6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
        'menu' => '<path d="M3 6h18M3 12h18M3 18h18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        'close' => '<path d="M18 6 6 18M6 6l12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        'linkedin' => '<path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5ZM3 9h4v12H3V9Zm7 0h3.8v1.7h.1a4.2 4.2 0 0 1 3.8-2c4 0 4.8 2.6 4.8 6V21h-4v-5.6c0-1.3 0-3-1.9-3s-2.1 1.5-2.1 2.9V21h-4V9Z" fill="currentColor"/>',
        'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.2" fill="currentColor"/>',
        'facebook' => '<path d="M14 8h3V4h-3a5 5 0 0 0-5 5v3H6v4h3v8h4v-8h3l1-4h-4V9a1 1 0 0 1 1-1Z" fill="currentColor"/>',
        'x' => '<path d="M17.8 3h3.1l-6.8 7.7L22 21h-6.2l-4.9-6.4L5.3 21H2.2l7.2-8.3L2 3h6.4l4.4 5.8L17.8 3Zm-1.1 16.2h1.7L7.4 4.7H5.6l11.1 14.5Z" fill="currentColor"/>',
        'chart' => '<path d="M3 3v18h18M7 15l4-4 3 3 5-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
        'box' => '<path d="M21 8 12 3 3 8v8l9 5 9-5V8ZM3 8l9 5 9-5M12 13v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>',
        'cart' => '<circle cx="9" cy="20" r="1.5" fill="currentColor"/><circle cx="18" cy="20" r="1.5" fill="currentColor"/><path d="M2 3h3l2.7 12.4a2 2 0 0 0 2 1.6h8.6a2 2 0 0 0 2-1.5L22 7H6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
        'factory' => '<path d="M2 20V9l6 4V9l6 4V4h4l1 16H2Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>',
        'coins' => '<circle cx="9" cy="9" r="6" fill="none" stroke="currentColor" stroke-width="2"/><path d="M15.5 9.4A6 6 0 1 1 9.4 15.5" fill="none" stroke="currentColor" stroke-width="2"/>',
        'users' => '<circle cx="9" cy="8" r="4" fill="none" stroke="currentColor" stroke-width="2"/><path d="M2 21a7 7 0 0 1 14 0M16 4a4 4 0 0 1 0 8M22 21a7 7 0 0 0-4-6.3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        'doc' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Z M14 2v6h6M8 13h8M8 17h5" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>',
        'briefcase' => '<rect x="2" y="7" width="20" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" fill="none" stroke="currentColor" stroke-width="2"/>',
        'cloud' => '<path d="M17.5 19a4.5 4.5 0 1 0-.9-8.9A6 6 0 0 0 5 11.5 3.75 3.75 0 0 0 6 19h11.5Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>',
        'cookie' => '<path d="M21.9 12.6A10 10 0 1 1 11.4 2.1a3.5 3.5 0 0 0 4.4 4.4 3.5 3.5 0 0 0 4.4 4.4c.6.5 1.1 1 1.7 1.7Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><circle cx="8.5" cy="9.5" r="1.3" fill="currentColor"/><circle cx="14.5" cy="15" r="1.3" fill="currentColor"/><circle cx="8.5" cy="15.5" r="1.1" fill="currentColor"/><circle cx="12" cy="12" r=".9" fill="currentColor"/>',
        'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="m9 12 2 2 4-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        'receipt' => '<path d="M4 2v20l3-2 3 2 2-2 2 2 3-2 3 2V2l-3 2-3-2-2 2-2-2-3 2-3-2Z M8 8h8M8 12h8M8 16h5" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>',
        'spark' => '<path d="M12 2v4M12 18v4M4.9 4.9l2.8 2.8M16.3 16.3l2.8 2.8M2 12h4M18 12h4M4.9 19.1l2.8-2.8M16.3 7.7l2.8-2.8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        'chat' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>',
        'eye' => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2"/>',
        'gear' => '<circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3h0a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8v0a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z" fill="none" stroke="currentColor" stroke-width="1.6"/>',
        'link' => '<path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        'map' => '<path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6ZM9 3v15M15 6v15" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>',
    ];
    $p = $icons[$name] ?? $icons['check'];
    return '<svg class="icon icon-' . e($name) . '" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">' . $p . '</svg>';
}

/** Section wrapper classes from the 'bg' key: '' | 'muted' | 'dark'. */
function section_class(array $s, string $extra = ''): string
{
    $bg = $s['bg'] ?? '';
    if ($bg === 'deep') $bg = 'dark section-deep'; // darker navy, so two dark sections in a row stay distinct
    if ($bg === 'mesh') $bg = 'mesh'; // light background with slowly drifting colour glows (CSS .section-mesh)
    return 'section' . ($bg ? ' section-' . $bg : '') . ($extra ? ' ' . $extra : '');
}

/**
 * Official SAP logos (supplied by DWP), shown plainly in SAP's colours.
 * SAP Business One has two transparent versions made from DWP's file: 'light' (navy text, for light backgrounds)
 * and 'dark' (white "Business One" text, for dark backgrounds — replace with SAP's official reversed file if DWP has it).
 */
function sap_partner_logo(int $height = 40, string $class = '', bool $lazy = true): string
{
    $src = asset_exists('img/badges/sap-partner.webp') ? 'img/badges/sap-partner.webp' : 'img/badges/sap-partner.png';
    if (!asset_exists($src)) return '';
    $w = (int) round($height * 4267 / 2502);
    return '<img class="sap-logo ' . e($class) . '" src="' . e(asset($src)) . '" alt="SAP Partner" width="' . $w . '" height="' . $height . '"' . ($lazy ? ' loading="lazy"' : '') . ' decoding="async">';
}

function sap_b1_logo(int $height = 28, string $class = '', string $on = 'light'): string
{
    $src = $on === 'dark' ? 'img/brand/sap-business-one-white.webp' : 'img/brand/sap-business-one.webp';
    if (!asset_exists($src)) return '';
    $w = (int) round($height * 826 / 140);
    return '<img class="sap-logo ' . e($class) . '" src="' . e(asset($src)) . '" alt="SAP Business One" width="' . $w . '" height="' . $height . '" loading="lazy" decoding="async">';
}

/** Official partner logos in their own colours, SAP first (partner band, home intro roller). */
function official_partner_logos(bool $lazy = true): array
{
    $l = $lazy ? ' loading="lazy"' : '';
    $img = fn(string $src, string $alt, int $w, int $h) => asset_exists($src) ? '<img src="' . e(asset($src)) . '" alt="' . e($alt) . '" width="' . $w . '" height="' . $h . '"' . $l . '>' : '';
    return array_values(array_filter([
        sap_partner_logo(56, '', $lazy),
        $img('img/badges/odoo-silver-partner.webp', t('trust.odoo'), 69, 60),
        $img('img/badges/future-factories.png', t('trust.ff'), 169, 48),
        $img('img/badges/huawei-partner.webp', 'Huawei Enterprise Partner', 62, 60),
    ]));
}

/** Technology vendor logos shown after the official partners in the home roller (on-dark variants: light lettering, brand colours kept). */
function technology_logos(): array
{
    $img = fn(string $src, string $alt, int $w, int $h) => asset_exists($src) ? '<img src="' . e(asset($src)) . '" alt="' . e($alt) . '" width="' . $w . '" height="' . $h . '" loading="lazy">' : '';
    return array_values(array_filter([
        $img('img/partners/microsoft-dark.svg', 'Microsoft', 338, 72),
        $img('img/partners/fortinet-dark.svg', 'Fortinet', 156, 19),
        $img('img/partners/cisco.svg', 'Cisco', 216, 114),
        $img('img/partners/kaspersky.svg', 'Kaspersky', 633, 131),
        $img('img/partners/attackiq-dark.svg', 'AttackIQ', 321, 34),
        $img('img/partners/zatca-dark.svg', 'ZATCA E-Invoicing', 215, 48),
    ]));
}
