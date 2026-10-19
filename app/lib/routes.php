<?php
/**
 * Page registry. Key = content file name in content/pages/<key>.php.
 * path    language-neutral path (same slug in EN and AR)
 * parent  key of the breadcrumb parent ('home' implied at the root)
 * schema  JSON-LD types for this page (FAQPage / HowTo are added automatically from sections)
 * interest  pre-selected "Interested in" option on the lead form
 */
function pages(): array
{
    static $pages = null;
    if ($pages !== null) return $pages;
    $pages = [
        'home'            => ['path' => '/', 'schema' => ['Organization', 'WebSite', 'LocalBusiness']],
        'about'           => ['path' => '/about/', 'schema' => ['Organization', 'AboutPage']],
        'partners'        => ['path' => '/partners/', 'schema' => ['Organization']],
        'our-clients'     => ['path' => '/our-clients/', 'schema' => ['CollectionPage']],
        'contact'         => ['path' => '/contact/', 'schema' => ['LocalBusiness']],
        'book-a-demo'     => ['path' => '/book-a-demo/', 'schema' => ['WebPage']],
        'thank-you'       => ['path' => '/thank-you/', 'noindex' => true],
        'privacy-policy'  => ['path' => '/privacy-policy/'],

        'sap-business-one'                 => ['path' => '/sap-business-one/', 'schema' => ['Service'], 'interest' => 'sap'],
        'sap-business-one.implementation'  => ['path' => '/sap-business-one/implementation/', 'parent' => 'sap-business-one', 'schema' => ['Service'], 'interest' => 'sap'],
        'sap-business-one.zatca'           => ['path' => '/sap-business-one/zatca-e-invoicing/', 'parent' => 'sap-business-one', 'schema' => ['Service'], 'interest' => 'zatca'],
        'sap-business-one.support'         => ['path' => '/sap-business-one/support/', 'parent' => 'sap-business-one', 'schema' => ['Service'], 'interest' => 'sap'],
        'sap-business-one.switch'          => ['path' => '/sap-business-one/switch/', 'parent' => 'sap-business-one', 'schema' => ['Article'], 'interest' => 'sap'],
        'sap-business-one.pricing'         => ['path' => '/sap-business-one/pricing/', 'parent' => 'sap-business-one', 'schema' => ['Article'], 'interest' => 'sap'],
        'sap-business-one.vs-odoo'         => ['path' => '/sap-business-one/vs-odoo/', 'parent' => 'sap-business-one', 'schema' => ['Article'], 'interest' => 'sap'],
        'sap-business-one.free-assessment' => ['path' => '/sap-business-one/free-assessment/', 'parent' => 'sap-business-one', 'schema' => ['WebPage'], 'interest' => 'sap'],

        'cortex.ai'                  => ['path' => '/cortex/ai/', 'parent' => 'ai', 'schema' => ['SoftwareApplication'], 'interest' => 'ai'],
        'cortex.intercompany'        => ['path' => '/cortex/intercompany/', 'parent' => 'sap-business-one', 'schema' => ['SoftwareApplication'], 'interest' => 'sap'],
        'cortex.production'           => ['path' => '/cortex/production/', 'parent' => 'sap-business-one', 'schema' => ['SoftwareApplication'], 'interest' => 'sap'],
        'cortex.project-management'   => ['path' => '/cortex/project-management/', 'parent' => 'sap-business-one', 'schema' => ['SoftwareApplication'], 'interest' => 'sap'],
        'cortex.hr-payroll'           => ['path' => '/cortex/hr-payroll/', 'parent' => 'sap-business-one', 'schema' => ['SoftwareApplication'], 'interest' => 'sap'],
        'cortex.pos'                  => ['path' => '/cortex/pos/', 'parent' => 'sap-business-one', 'schema' => ['SoftwareApplication'], 'interest' => 'sap'],
        'cortex.property-management'  => ['path' => '/cortex/property-management/', 'parent' => 'sap-business-one', 'schema' => ['SoftwareApplication'], 'interest' => 'sap'],
        'cortex.assets-management'    => ['path' => '/cortex/assets-management/', 'parent' => 'sap-business-one', 'schema' => ['SoftwareApplication'], 'interest' => 'sap'],
        'ai'                         => ['path' => '/ai/', 'schema' => ['Service'], 'interest' => 'ai'],
        'ai.ai-for-sap-business-one' => ['path' => '/ai/ai-for-sap-business-one/', 'parent' => 'ai', 'schema' => ['Service'], 'interest' => 'ai'],
        'ai.predictive-analytics'    => ['path' => '/ai/predictive-analytics/', 'parent' => 'ai', 'schema' => ['Service'], 'interest' => 'ai'],
        'ai.arabic-ai-assistants'    => ['path' => '/ai/arabic-ai-assistants/', 'parent' => 'ai', 'schema' => ['Service'], 'interest' => 'ai'],
        'ai.document-automation'     => ['path' => '/ai/document-automation/', 'parent' => 'ai', 'schema' => ['Service'], 'interest' => 'ai'],
        'ai.vision-ai'               => ['path' => '/ai/vision-ai/', 'parent' => 'ai', 'schema' => ['Service'], 'interest' => 'ai'],

        'erp'                         => ['path' => '/erp/', 'schema' => ['Service'], 'interest' => 'sap'],
        'erp.odoo'                    => ['path' => '/erp/odoo/', 'parent' => 'erp', 'schema' => ['Service'], 'interest' => 'odoo'],
        'data-and-bi'                 => ['path' => '/data-and-bi/', 'schema' => ['Service'], 'interest' => 'bi'],
        'consulting'                  => ['path' => '/consulting/', 'schema' => ['Service'], 'interest' => 'other'],
        'iot-industrial-intelligence' => ['path' => '/iot-industrial-intelligence/', 'schema' => ['Service'], 'interest' => 'iot'],

        'products'                => ['path' => '/products/', 'schema' => ['CollectionPage']],
        'solutions'               => ['path' => '/solutions/', 'schema' => ['CollectionPage']],
        'cras'                    => ['path' => '/cras/', 'schema' => ['SoftwareApplication'], 'interest' => 'other'],
        'pms'                     => ['path' => '/pms/', 'schema' => ['SoftwareApplication'], 'interest' => 'other'],
        'industrial-iot-platform' => ['path' => '/industrial-iot-platform/', 'schema' => ['SoftwareApplication'], 'interest' => 'iot'],
        'hrms'                    => ['path' => '/hrms/', 'schema' => ['SoftwareApplication'], 'interest' => 'other', 'enabled' => (bool) cfg('hrms_enabled')],

        'industries'               => ['path' => '/industries/', 'schema' => ['CollectionPage'], 'interest' => 'sap'],
        'industries.distribution'  => ['path' => '/industries/distribution/', 'parent' => 'industries', 'schema' => ['Service'], 'interest' => 'sap'],
        'industries.manufacturing' => ['path' => '/industries/manufacturing/', 'parent' => 'industries', 'schema' => ['Service'], 'interest' => 'sap'],
        'industries.retail'        => ['path' => '/industries/retail/', 'parent' => 'industries', 'schema' => ['Service'], 'interest' => 'sap'],
        'industries.contracting'   => ['path' => '/industries/contracting/', 'parent' => 'industries', 'schema' => ['Service'], 'interest' => 'sap'],
        'built-for-saudi-arabia'   => ['path' => '/built-for-saudi-arabia/', 'parent' => 'industries', 'schema' => ['Service'], 'interest' => 'zatca'],

        'insights' => ['path' => '/insights/', 'schema' => ['CollectionPage']],
        'careers'  => ['path' => '/careers/', 'schema' => ['WebPage']],
    ];
    foreach ($pages as $k => &$p) {
        $p['key'] = $k;
        $p += ['parent' => $k === 'home' ? null : 'home', 'schema' => [], 'interest' => 'sap', 'noindex' => false, 'enabled' => true];
    }
    return $pages;
}

function page_by_path(string $path): ?array
{
    foreach (pages() as $p) {
        if ($p['path'] === $path && $p['enabled']) return $p;
    }
    return null;
}

/** Load a page's content for a language. */
function page_content(string $key, string $lang): array
{
    static $cache = [];
    if (!isset($cache[$key])) {
        $file = ROOT_DIR . '/content/pages/' . $key . '.php';
        $cache[$key] = is_file($file) ? require $file : [];
    }
    require_once ROOT_DIR . '/content/live.php';   // live product layout for pages with a content/live/<key>.php file
    return faq_last(live_layout($cache[$key][$lang] ?? [], $key, $lang), $key, $lang);
}

/**
 * Every page ends with the FAQ (then the footer with its "Let's talk" contact row). The page's own FAQ moves to the end;
 * pages without one get the home page FAQ. All FAQs share the same scroll-revealed background (sections/faq.php).
 */
function faq_last(array $c, string $key, string $lang): array
{
    if (empty($c['sections'])) return $c;
    $faq = []; $rest = []; $tail = [];
    foreach ($c['sections'] as $s) {
        $t = $s['type'] ?? '';
        if ($t === 'faq') $faq[] = $s;
        elseif ($t === 'cta_band') $tail[] = $s;
        else $rest[] = $s;
    }
    if (!$faq && $key !== 'home') {
        foreach (page_content('home', $lang)['sections'] ?? [] as $s) if (($s['type'] ?? '') === 'faq') { $faq[] = $s; break; }
    }
    foreach ($faq as &$f) unset($f['cta']);
    $tail = array_values(array_filter($tail, fn($s) => empty($s['hide'])));
    $c['sections'] = array_merge($rest, $faq, $tail);
    return $c;
}

/** Short page name (breadcrumbs, WhatsApp message). */
function page_name(string $key, string $lang): string
{
    $c = page_content($key, $lang);
    return plain($c['name'] ?? $c['h1'] ?? $key);
}

/** Old URLs → new URLs (spec A5). Paths are relative to the base path, with trailing slash. */
function redirect_map(): array
{
    $map = [
        '/erp-programs/'       => '/erp/',
        '/solutions-ai/'       => '/ai/',
        '/ar/erp-programs-ar/' => '/ar/erp/',
        '/ar/solutions-ai-ar/' => '/ar/ai/',
    ];
    foreach (['about', 'partners', 'our-clients', 'contact', 'consulting', 'data-and-bi', 'iot-industrial-intelligence', 'cras', 'pms', 'industrial-iot-platform', 'built-for-saudi-arabia', 'hrms', 'careers'] as $slug) {
        $map["/ar/{$slug}-ar/"] = "/ar/{$slug}/";
    }
    foreach ([
        '5-common-help-desk-mistakes', 'future-of-help-desks-2025-and-beyond', 'future-of-help-desks-2026-and-beyond',
        'mastering-customer-queries-like-a-pro', 'transform-your-customer-journey-in-30-days',
        '5-signs-its-time-to-upgrade-your-support-system', 'how-we-doubled-customer-happiness-in-6-months',
        'how-we-doubled-customer-happiness-in-6-months-2', 'how-top-support-teams-stay-motivated-and-fast',
        'how-top-support-teams-stay-motivated-and-fast-2', 'tell-me-you-want-it-without-telling-me',
        'how-to-build-customer-loyalty-in-a-competitive-market',
    ] as $slug) {
        $map["/{$slug}/"] = '/insights/';
    }
    return $map;
}

function gone_paths(): array
{
    return ['/hello-world/', '/test-page/'];
}
