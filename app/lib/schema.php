<?php
/** JSON-LD builders. */

/** Default share image (absolute URL) — used by Open Graph and as Article image. */
function og_image_url(): string
{
    return rtrim(cfg('base_url'), '/') . asset('img/og-default.jpg');
}

function org_id(): string { return rtrim(cfg('base_url'), '/') . url('/', 'en') . '#organization'; }

function schema_organization(string $lang): array
{
    $same = array_values(array_filter([cfg('linkedin'), cfg('instagram'), cfg('facebook'), cfg('x')]));
    $o = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        '@id' => org_id(),
        'name' => 'DWP Arabia',
        'legalName' => cfg('legal_name'),
        'url' => url('/', $lang, true),
        'logo' => rtrim(cfg('base_url'), '/') . asset(asset_exists("img/logo-dark.png") ? "img/logo-dark.png" : "img/logo-light.png"),
        'foundingDate' => '1999',
        'telephone' => cfg('phone_e164'),
        'address' => schema_address($lang),
        'sameAs' => $same,
    ];
    if (cfg('email')) $o['email'] = cfg('email');
    return $o;
}

function schema_address(string $lang): array
{
    return [
        '@type' => 'PostalAddress',
        'streetAddress' => $lang === 'ar' ? 'مكاتب هلا سنتر، 3310 طريق أنس بن مالك' : 'Hala Center Offices, 3310 Anas bin Malik Road',
        'addressLocality' => $lang === 'ar' ? 'الرياض' : 'Riyadh',
        'addressCountry' => 'SA',
    ];
}

function schema_website(string $lang): array
{
    return [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'DWP Arabia',
        'url' => url('/', $lang, true),
        'inLanguage' => $lang === 'ar' ? 'ar-SA' : 'en-SA',
        'publisher' => ['@id' => org_id()],
    ];
}

function schema_local_business(string $lang): array
{
    $lb = [
        '@context' => 'https://schema.org',
        '@type' => 'ProfessionalService',
        'name' => 'DWP Arabia',
        'url' => url('/', $lang, true),
        'image' => rtrim(cfg('base_url'), '/') . asset(asset_exists("img/logo-dark.png") ? "img/logo-dark.png" : "img/logo-light.png"),
        'telephone' => cfg('phone_e164'),
        'address' => schema_address($lang),
        'parentOrganization' => ['@id' => org_id()],
        'openingHoursSpecification' => [[
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
            'opens' => '09:00',
            'closes' => '17:00',
        ]],
        'areaServed' => ['@type' => 'Country', 'name' => 'Saudi Arabia'],
    ];
    $geo = cfg('geo');
    if (!empty($geo['lat']) && !empty($geo['lng'])) {
        $lb['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => $geo['lat'], 'longitude' => $geo['lng']];
    }
    if (cfg('email')) $lb['email'] = cfg('email');
    return $lb;
}

function schema_breadcrumbs(array $trail, string $lang): array
{
    $items = [];
    foreach ($trail as $i => [$name, $path]) {
        $items[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => plain($name), 'item' => url($path, $lang, true)];
    }
    return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items];
}

function schema_faq(array $items): array
{
    return [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(fn($qa) => [
            '@type' => 'Question',
            'name' => plain($qa[0]),
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => plain($qa[1])],
        ], $items),
    ];
}

function schema_howto(string $name, array $steps): array
{
    return [
        '@context' => 'https://schema.org',
        '@type' => 'HowTo',
        'name' => plain($name),
        'step' => array_map(fn($s, $i) => [
            '@type' => 'HowToStep', 'position' => $i + 1, 'name' => plain($s[0]), 'text' => plain($s[1] ?? $s[0]),
        ], $steps, array_keys($steps)),
    ];
}

function schema_simple(string $type, array $c, string $path, string $lang): array
{
    $base = [
        '@context' => 'https://schema.org',
        '@type' => $type,
        'name' => plain($c['h1'] ?? $c['title'] ?? ''),
        'description' => plain($c['meta'] ?? ''),
        'url' => url($path, $lang, true),
        'inLanguage' => $lang === 'ar' ? 'ar-SA' : 'en-SA',
    ];
    switch ($type) {
        case 'Service':
            $base['provider'] = ['@id' => org_id()];
            $base['areaServed'] = ['@type' => 'Country', 'name' => 'Saudi Arabia'];
            $base['serviceType'] = plain($c['name'] ?? $c['h1'] ?? '');
            break;
        case 'Article':
            $base['headline'] = plain($c['h1'] ?? '');
            $base['image'] = [og_image_url()];
            $base['author'] = ['@id' => org_id()];
            $base['publisher'] = ['@id' => org_id()];
            if (!empty($c['date'])) $base['datePublished'] = $c['date'];
            break;
        case 'SoftwareApplication':
            $base['name'] = plain($c['name'] ?? $c['h1'] ?? '');
            $base['applicationCategory'] = 'BusinessApplication';
            $base['operatingSystem'] = 'Web';
            $base['publisher'] = ['@id' => org_id()];
            break;
        case 'AboutPage':
        case 'CollectionPage':
        case 'WebPage':
            $base['isPartOf'] = ['@type' => 'WebSite', 'url' => url('/', $lang, true)];
            break;
    }
    return $base;
}

/** All JSON-LD objects for a registered page. */
function page_jsonld(array $page, array $c, string $lang, array $trail): array
{
    $out = [];
    foreach ($page['schema'] as $type) {
        $out[] = match ($type) {
            'Organization' => schema_organization($lang),
            'WebSite' => schema_website($lang),
            'LocalBusiness' => schema_local_business($lang),
            default => schema_simple($type, $c, $page['path'], $lang),
        };
    }
    $faq = [];
    foreach ($c['sections'] ?? [] as $s) {
        if (($s['type'] ?? '') === 'faq') $faq = array_merge($faq, $s['items']);
        if (($s['type'] ?? '') === 'steps' && !empty($s['howto'])) $out[] = schema_howto($s['h2'] ?? $c['h1'], $s['items']);
    }
    if ($faq) $out[] = schema_faq($faq);
    if (count($trail) > 1) $out[] = schema_breadcrumbs($trail, $lang);
    return $out;
}
