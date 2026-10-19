<?php
/** Front controller: redirects, special endpoints, static and database-driven pages. */

function dispatch(string $requestUri, string $method): void
{
    $base = rtrim(cfg('base_path'), '/');
    $rawPath = rawurldecode(parse_url($requestUri, PHP_URL_PATH) ?? '/');
    $query = parse_url($requestUri, PHP_URL_QUERY);
    $qs = $query ? '?' . $query : '';

    if ($base !== '' && $rawPath !== $base && !str_starts_with($rawPath, $base . '/')) {
        $GLOBALS['lang'] = 'en';
        render_404();
        return;
    }
    $path = $base !== '' ? substr($rawPath, strlen($base)) : $rawPath;
    if ($path === '' || $path === false) $path = '/';

    // Static files that map to generated content.
    if ($path === '/robots.txt') { robots_txt(); return; }
    if ($path === '/sitemap.xml') { sitemap_xml('en'); return; }
    if ($path === '/ar/sitemap.xml') { sitemap_xml('ar'); return; }
    if ($path === '/sitemap-index.xml') { sitemap_index(); return; }

    // Admin area.
    if ($path === '/admin' || str_starts_with($path, '/admin/')) {
        if (!cfg('admin_enabled')) { render_404('en'); return; }
        require APP_DIR . '/admin/admin.php';
        admin_dispatch(substr($path, 6) ?: '/', $method);
        return;
    }

    // Form endpoints.
    if ($method === 'POST') {
        $GLOBALS['lang'] = 'en';
        match (rtrim($path, '/') . '/') {
            '/api/lead/' => handle_lead_post(),
            '/api/assessment/' => handle_assessment_post(),
            '/api/cv/' => handle_cv_post(),
            default => (function () { http_response_code(405); echo 'Method not allowed'; })(),
        };
        return;
    }

    // Normalise: lowercase + trailing slash (301).
    $norm = strtolower($path);
    if (!str_ends_with($norm, '/') && !preg_match('~\.[a-z0-9]{2,5}$~', $norm)) $norm .= '/';
    $norm = preg_replace('~/+~', '/', $norm);

    // Legacy redirects (A5).
    $map = redirect_map();
    if (isset($map[$norm])) redirect($base . $map[$norm] . $qs, 301);
    if (preg_match('~^/career/~', $norm) || $norm === '/career/') redirect($base . '/careers/' . $qs, 301);
    if (preg_match('~^/ar/career/~', $norm)) redirect($base . '/ar/careers/' . $qs, 301);
    if (in_array($norm, gone_paths(), true)) {
        http_response_code(410);
        $GLOBALS['lang'] = 'en';
        render_page(['key' => '410', 'path' => '/', 'lang' => 'en', 'noindex' => true, 'title' => 'Page removed | DWP Arabia', 'meta' => '', 'h1' => 'Page removed', 'alternates' => ['en' => false, 'ar' => false], 'body' => view('partials/not-found')]);
        return;
    }
    if ($norm !== $path) redirect($base . $norm . $qs, 301);

    $lang = 'en';
    if ($norm === '/ar/' || str_starts_with($norm, '/ar/')) {
        $lang = 'ar';
        $norm = substr($norm, 3);
    }
    route_page($norm, $lang);
}

/** Render the page at a language-neutral path. */
function route_page(string $path, string $lang): void
{
    $GLOBALS['lang'] = $lang;

    if ($page = page_by_path($path)) {
        render_registered($page, $lang);
        return;
    }
    if (preg_match('~^/insights/([a-z0-9\-]+)/$~', $path, $m)) { render_article($m[1], $lang); return; }
    if (preg_match('~^/careers/([a-z0-9\-]+)/$~', $path, $m)) { render_role($m[1], $lang); return; }
    if (preg_match('~^/our-clients/([a-z0-9\-]+)/$~', $path, $m)) { render_case_study($m[1], $lang); return; }
    if ($path === '/sap-business-one/free-assessment/result/') { render_assessment_result($lang); return; }

    render_404($lang);
}

function render_registered(array $page, string $lang): void
{
    $c = page_content($page['key'], $lang);
    if (!$c) { render_404($lang); return; }
    $trail = breadcrumb_trail($page['key'], $lang);
    render_page([
        'key' => $page['key'], 'path' => $page['path'], 'lang' => $lang,
        'title' => $c['title'], 'meta' => $c['meta'] ?? '', 'h1' => $c['h1'],
        'noindex' => $page['noindex'], 'trail' => $trail,
        'jsonld' => page_jsonld($page, $c, $lang, $trail),
        'sections' => $c['sections'] ?? [], 'interest' => $page['interest'],
        'body_class' => 'page-' . str_replace('.', '-', $page['key']),
    ]);
}

/** Alternate-language availability for a DB row (same slug in the other language). */
function db_alternates(string $table, string $slug, string $lang, string $statusSql): array
{
    $other = $lang === 'ar' ? 'en' : 'ar';
    $row = db_one("SELECT id FROM `$table` WHERE slug = ? AND lang = ? AND $statusSql", [$slug, $other]);
    return [$lang => true, $other => (bool) $row];
}

function render_article(string $slug, string $lang): void
{
    $status = is_staging() ? "status IN ('published','draft')" : "status = 'published'";
    $a = db_one("SELECT * FROM insights WHERE slug = ? AND lang = ? AND $status", [$slug, $lang]);
    if (!$a) { render_404($lang); return; }
    $path = '/insights/' . $slug . '/';
    $trail = [[t('home'), '/'], [page_name('insights', $lang), '/insights/'], [$a['title'], $path]];
    $faq = $a['faq_json'] ? (json_decode($a['faq_json'], true) ?: []) : [];
    $abs = fn($p) => url($p, $lang, true);
    $article = [
        '@context' => 'https://schema.org', '@type' => 'Article',
        'headline' => $a['h1'] ?: $a['title'], 'description' => $a['meta_description'], 'image' => [og_image_url()],
        'inLanguage' => $lang === 'ar' ? 'ar-SA' : 'en-SA', 'mainEntityOfPage' => $abs($path),
        'datePublished' => $a['published_at'] ?: null, 'dateModified' => $a['updated_at'] ?: $a['published_at'],
        'author' => $a['author_name'] ? ['@type' => 'Person', 'name' => $a['author_name'], 'jobTitle' => $a['author_title'], 'sameAs' => array_filter([$a['author_linkedin']])] : ['@id' => org_id()],
        'publisher' => ['@id' => org_id()],
    ];
    $jsonld = [array_filter($article, fn($v) => $v !== null && $v !== ''), schema_breadcrumbs($trail, $lang)];
    if ($faq) $jsonld[] = schema_faq($faq);
    $related = db_all("SELECT slug, title FROM insights WHERE lang = ? AND slug <> ? AND $status ORDER BY published_at DESC LIMIT 3", [$lang, $slug]);
    render_page([
        'key' => 'insights', 'path' => $path, 'lang' => $lang, 'og_type' => 'article',
        'title' => $a['title'] . ' | DWP Arabia', 'meta' => $a['meta_description'], 'h1' => $a['h1'] ?: $a['title'],
        'noindex' => $a['status'] !== 'published', 'trail' => $trail, 'jsonld' => $jsonld,
        'alternates' => db_alternates('insights', $slug, $lang, $status),
        'body' => view('partials/article', ['a' => $a, 'faq' => $faq, 'related' => $related]),
        'body_class' => 'page-article',
    ]);
}

function render_role(string $slug, string $lang): void
{
    $r = db_one("SELECT * FROM careers WHERE slug = ? AND lang = ? AND status = 'open'", [$slug, $lang]);
    if (!$r) { redirect(url('/careers/', $lang), 302); }
    $path = '/careers/' . $slug . '/';
    $trail = [[t('home'), '/'], [page_name('careers', $lang), '/careers/'], [$r['title'], $path]];
    $job = array_filter([
        '@context' => 'https://schema.org', '@type' => 'JobPosting',
        'title' => $r['title'], 'description' => $r['body_html'], 'datePosted' => $r['date_posted'],
        'validThrough' => $r['valid_through'] ? $r['valid_through'] . 'T23:59:59+03:00' : null,
        'employmentType' => $r['employment_type'],
        'hiringOrganization' => ['@type' => 'Organization', 'name' => 'DWP Arabia', 'sameAs' => url('/', $lang, true)],
        'jobLocation' => ['@type' => 'Place', 'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Riyadh', 'addressCountry' => 'SA']],
    ]);
    render_page([
        'key' => 'careers', 'path' => $path, 'lang' => $lang,
        'title' => $r['title'] . ' | ' . ($lang === 'ar' ? 'الوظائف في DWP Arabia' : 'Careers at DWP Arabia'),
        'meta' => $r['meta_description'], 'h1' => $r['title'], 'trail' => $trail,
        'jsonld' => [$job, schema_breadcrumbs($trail, $lang)],
        'alternates' => db_alternates('careers', $slug, $lang, "status = 'open'"),
        'body' => view('partials/role', ['r' => $r]) . render_sections([['type' => 'cv_form', 'role' => $slug, 'heading' => t('apply'), 'sub' => '']], ['key' => 'careers', 'interest' => 'other']),
    ]);
}

function render_case_study(string $slug, string $lang): void
{
    $cs = db_one("SELECT * FROM case_studies WHERE slug = ? AND lang = ? AND status = 'published'", [$slug, $lang]);
    if (!$cs) { render_404($lang); return; }
    $path = '/our-clients/' . $slug . '/';
    $trail = [[t('home'), '/'], [page_name('our-clients', $lang), '/our-clients/'], [$cs['title'], $path]];
    $article = ['@context' => 'https://schema.org', '@type' => 'Article', 'headline' => $cs['title'], 'image' => [og_image_url()], 'datePublished' => substr((string) $cs['updated_at'], 0, 10), 'description' => $cs['meta_description'], 'publisher' => ['@id' => org_id()], 'author' => ['@id' => org_id()], 'inLanguage' => $lang === 'ar' ? 'ar-SA' : 'en-SA'];
    $page = ['key' => 'our-clients', 'path' => $path, 'lang' => $lang, 'interest' => 'sap'];
    render_page($page + [
        'title' => $cs['title'] . ' | DWP Arabia', 'meta' => $cs['meta_description'], 'h1' => $cs['title'], 'trail' => $trail,
        'jsonld' => [$article, schema_breadcrumbs($trail, $lang)],
        'alternates' => db_alternates('case_studies', $slug, $lang, "status = 'published'"),
        'body' => view('partials/case-study', ['cs' => $cs]) . render_sections([['type' => 'lead_form'], ['type' => 'cta_band']], $page),
    ]);
}

function render_assessment_result(string $lang): void
{
    $row = db_one('SELECT * FROM assessments WHERE token = ?', [preg_replace('~[^a-f0-9]~', '', $_GET['t'] ?? '')]);
    if (!$row) { redirect(url('/sap-business-one/free-assessment/', $lang)); }
    $bc = assessment_band_content($row['band'], $lang);
    $page = ['key' => 'sap-business-one.free-assessment', 'path' => '/sap-business-one/free-assessment/', 'lang' => $lang, 'interest' => 'sap'];
    render_page($page + [
        'title' => plain($bc['title']) . ' | DWP Arabia', 'meta' => '', 'h1' => $bc['title'], 'noindex' => true,
        'alternates' => ['en' => false, 'ar' => false],
        'body' => render_sections([['type' => 'assessment_result', 'row' => $row], ['type' => 'cta_band']], $page),
    ]);
}

/* ---------- robots + sitemaps ---------- */

function robots_txt(): void
{
    header('Content-Type: text/plain; charset=utf-8');
    $base = rtrim(cfg('base_url'), '/') . rtrim(cfg('base_path'), '/');
    echo "User-agent: *\n";
    if (cfg('env') !== 'production') { echo "Disallow: /\n"; return; }
    echo "Disallow: " . rtrim(cfg('base_path'), '/') . "/admin/\n";
    echo "Disallow: " . rtrim(cfg('base_path'), '/') . "/api/\n";
    echo "Disallow: " . rtrim(cfg('base_path'), '/') . "/thank-you/\n";
    echo "Disallow: " . rtrim(cfg('base_path'), '/') . "/ar/thank-you/\n\n";
    echo "Sitemap: {$base}/sitemap-index.xml\n";
}

/** All indexable URLs for one language: [[neutral path, lastmod|null, alt exists], …] */
function sitemap_entries(string $lang): array
{
    $out = [];
    foreach (pages() as $p) {
        if (!$p['enabled'] || $p['noindex']) continue;
        if (!page_content($p['key'], $lang)) continue;
        $file = ROOT_DIR . '/content/pages/' . $p['key'] . '.php';
        $out[] = [$p['path'], date('Y-m-d', filemtime($file)), true];
    }
    $other = $lang === 'ar' ? 'en' : 'ar';
    foreach ([['insights', "status = 'published'", 'COALESCE(updated_at, published_at)'], ['careers', "status = 'open'", 'date_posted'], ['case_studies', "status = 'published'", 'updated_at']] as [$table, $where, $mod]) {
        $prefix = ['insights' => '/insights/', 'careers' => '/careers/', 'case_studies' => '/our-clients/'][$table];
        foreach (db_all("SELECT t.slug, $mod AS m, (SELECT COUNT(*) FROM `$table` o WHERE o.slug = t.slug AND o.lang = ? AND o.$where) AS alt FROM `$table` t WHERE t.lang = ? AND t.$where", [$other, $lang]) as $r) {
            $out[] = [$prefix . $r['slug'] . '/', $r['m'] ? substr((string) $r['m'], 0, 10) : null, (bool) $r['alt']];
        }
    }
    return $out;
}

function sitemap_xml(string $lang): void
{
    header('Content-Type: application/xml; charset=utf-8');
    $GLOBALS['lang'] = $lang;
    $other = $lang === 'ar' ? 'en' : 'ar';
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
    foreach (sitemap_entries($lang) as [$path, $mod, $alt]) {
        echo "  <url>\n    <loc>" . e(url($path, $lang, true)) . "</loc>\n";
        if ($mod) echo "    <lastmod>$mod</lastmod>\n";
        echo '    <xhtml:link rel="alternate" hreflang="' . $lang . '-SA" href="' . e(url($path, $lang, true)) . '"/>' . "\n";
        if ($alt) echo '    <xhtml:link rel="alternate" hreflang="' . $other . '-SA" href="' . e(url($path, $other, true)) . '"/>' . "\n";
        echo "  </url>\n";
    }
    echo "</urlset>\n";
}

function sitemap_index(): void
{
    header('Content-Type: application/xml; charset=utf-8');
    $base = rtrim(cfg('base_url'), '/') . rtrim(cfg('base_path'), '/');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    echo "  <sitemap><loc>{$base}/sitemap.xml</loc><lastmod>" . date('Y-m-d') . "</lastmod></sitemap>\n";
    echo "  <sitemap><loc>{$base}/ar/sitemap.xml</loc><lastmod>" . date('Y-m-d') . "</lastmod></sitemap>\n";
    echo "</sitemapindex>\n";
}
