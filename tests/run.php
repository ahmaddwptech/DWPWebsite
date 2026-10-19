<?php
/**
 * Launch checks (spec Part D). Runs over HTTP against a running site.
 *   php -S 127.0.0.1:8080 -t public scripts/dev-router.php   (in another terminal)
 *   php tests/run.php [http://127.0.0.1:8080]
 * Exit code 1 if any check fails.
 */
require __DIR__ . '/../app/bootstrap.php';

$host = rtrim($argv[1] ?? 'http://127.0.0.1:8080', '/');
$base = $host . rtrim(cfg('base_path'), '/');
$fail = 0;
$pass = 0;
$warnings = [];

function check(bool $ok, string $msg): void
{
    global $fail, $pass;
    if ($ok) { $pass++; return; }
    $fail++;
    echo "  ✗ $msg\n";
}

function fetch(string $url, bool $follow = false): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => $follow, CURLOPT_HEADER => false, CURLOPT_TIMEOUT => 20]);
    $body = (string) curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $loc = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    return [$code, $body, $loc];
}

function dom(string $html): DOMXPath
{
    $d = new DOMDocument();
    libxml_use_internal_errors(true);
    $d->loadHTML('<?xml encoding="utf-8"?>' . $html);
    libxml_clear_errors();
    return new DOMXPath($d);
}

$siteBase = rtrim(cfg('base_url'), '/') . rtrim(cfg('base_path'), '/');
$enUiOnAr = ['Book a Demo', 'Talk to an SAP Business One expert', 'Full name', 'Work email', 'Privacy Policy', 'Read more', 'Learn more', 'Frequently asked questions'];

echo "== Pages (EN + AR): title, meta, single H1, hreflang, canonical, schema, RTL\n";
$GLOBALS['config']['hrms_enabled'] = cfg('hrms_enabled'); // HRMS is tested only when enabled
foreach (pages() as $key => $p) {
    if (!$p['enabled']) { echo "  – skipped $key (disabled)\n"; continue; }
    foreach (['en', 'ar'] as $lang) {
        $c = page_content($key, $lang);
        $path = ($lang === 'ar' ? '/ar' : '') . $p['path'];
        $label = "$key [$lang] $path";
        check((bool) $c, "$label: content missing");
        if (!$c) continue;
        [$code, $html] = fetch($base . $path);
        check($code === 200, "$label: HTTP $code");
        if ($code !== 200) continue;
        $x = dom($html);

        $htmlEl = $x->query('//html')->item(0);
        check($htmlEl && $htmlEl->getAttribute('lang') === $lang, "$label: <html lang> should be $lang");
        check($htmlEl && $htmlEl->getAttribute('dir') === ($lang === 'ar' ? 'rtl' : 'ltr'), "$label: wrong dir");

        $title = trim($x->query('//title')->item(0)?->textContent ?? '');
        check($title !== '' && $title === plain($c['title']), "$label: <title> mismatch ('$title')");
        check(!preg_match('~\[(CONFIRM|AR-REVIEW)~', $title), "$label: marker in <title>");
        $meta = $x->query('//meta[@name="description"]/@content')->item(0)?->nodeValue ?? '';
        if (!$p['noindex']) {
            check($meta !== '' && $meta === plain($c['meta'] ?? ''), "$label: meta description missing/mismatch");
        }
        $h1s = $x->query('//h1');
        check($h1s->length === 1, "$label: expected 1 <h1>, found {$h1s->length}");
        if ($h1s->length) check(trim(preg_replace('~\s+~u', ' ', $h1s->item(0)->textContent)) === plain($c['h1']), "$label: H1 text differs from content");

        $canon = $x->query('//link[@rel="canonical"]/@href')->item(0)?->nodeValue;
        check($canon === url($p['path'], $lang, true), "$label: canonical is $canon");
        if (!$p['noindex']) {
            $hl = [];
            foreach ($x->query('//link[@rel="alternate"][@hreflang]') as $l) $hl[$l->getAttribute('hreflang')] = $l->getAttribute('href');
            check(($hl['en-SA'] ?? '') === url($p['path'], 'en', true), "$label: hreflang en-SA");
            check(($hl['ar-SA'] ?? '') === url($p['path'], 'ar', true), "$label: hreflang ar-SA");
            check(($hl['x-default'] ?? '') === url($p['path'], 'en', true), "$label: hreflang x-default");
            check($x->query('//meta[@name="robots"][contains(@content,"noindex")]')->length === 0, "$label: indexable page has noindex");
        } else {
            check($x->query('//meta[@name="robots"][contains(@content,"noindex")]')->length === 1, "$label: should be noindex");
        }
        check($x->query('//meta[@property="og:locale"][@content="' . og_locale($lang) . '"]')->length === 1, "$label: og:locale");
        check($x->query('//meta[@property="og:image"]')->length === 1, "$label: og:image");

        // JSON-LD: valid JSON; breadcrumbs on inner pages; FAQ schema matches visible FAQ.
        $types = [];
        $faqSchema = [];
        foreach ($x->query('//script[@type="application/ld+json"]') as $s) {
            $j = json_decode($s->textContent, true);
            check(is_array($j), "$label: invalid JSON-LD");
            if (!is_array($j)) continue;
            $types[] = $j['@type'];
            if ($j['@type'] === 'FAQPage') foreach ($j['mainEntity'] as $q) $faqSchema[] = [$q['name'], $q['acceptedAnswer']['text']];
        }
        foreach ($p['schema'] as $t) {
            $want = $t === 'LocalBusiness' ? 'ProfessionalService' : $t;
            check(in_array($want, $types, true), "$label: missing $t schema");
        }
        if ($key !== 'home' && !$p['noindex']) {
            check(in_array('BreadcrumbList', $types, true), "$label: missing BreadcrumbList");
            check($x->query('//nav[contains(@class,"breadcrumbs")]')->length === 1, "$label: missing visible breadcrumbs");
        }
        $visibleFaq = [];
        foreach ($x->query('//section[contains(@class,"faq-section")]//details') as $det) {
            $q = trim(preg_replace('~\s+~u', ' ', $x->query('.//summary', $det)->item(0)->textContent));
            $a = trim(preg_replace('~\s+~u', ' ', $x->query('.//div[contains(@class,"faq-answer")]', $det)->item(0)->textContent));
            $visibleFaq[] = [$q, $a];
        }
        if ($visibleFaq || $faqSchema) {
            $schemaPlain = array_map(fn($qa) => [$qa[0], $qa[1]], $faqSchema);
            if (cfg('env') === 'production') check($visibleFaq === $schemaPlain, "$label: FAQ schema text differs from visible FAQ");
            else check(count($visibleFaq) === count($schemaPlain), "$label: FAQ schema count differs from visible FAQ");
        }

        // Images need alt + dimensions.
        foreach ($x->query('//main//img') as $img) {
            check($img->hasAttribute('alt') && trim($img->getAttribute('alt')) !== '', "$label: image without alt (" . $img->getAttribute('src') . ')');
            check($img->hasAttribute('width') && $img->hasAttribute('height'), "$label: image without width/height");
        }

        // Language switch points to the same page in the other language.
        $other = $lang === 'ar' ? 'en' : 'ar';
        $sw = $x->query('//a[contains(@class,"lang-switch")]/@href')->item(0)?->nodeValue;
        check($sw === url($p['path'], $other), "$label: language switch points to $sw");

        $text = $x->query('//body')->item(0)->textContent;
        check(!preg_match('~No Post Found|lorem ipsum~i', $text), "$label: demo/filler text found");
        check(!preg_match('~reseller|licen[cs]e sales|authori[sz]ed distributor~i', $text), "$label: forbidden reseller wording");
        if ($lang === 'ar') {
            foreach ($enUiOnAr as $s) check(!str_contains($text, $s), "$label: English UI text on Arabic page: '$s'");
        }
    }
}

echo "== Titles, metas and H1s match the spec verbatim (refdoc/DWP-Arabia-Website-Build-Spec.md)\n";
$specFile = ROOT_DIR . '/refdoc/DWP-Arabia-Website-Build-Spec.md';
if (is_file($specFile)) {
    $spec = str_replace(['\\|', '’'], ['|', "'"], file_get_contents($specFile));
    // Compare words only: the site removes dashes from copy (CLAUDE.md), so punctuation may differ from the spec.
    $words = fn(string $t) => trim(preg_replace('/\s+/u', ' ', preg_replace('/[—–,،.:;!?]/u', ' ', $t)));
    $specWords = $words($spec);
    // Pages/fields the spec doesn't define text for (written during the build; listed in CONFIRM.md).
    $notInSpec = ['thank-you' => ['meta', 'title', 'h1'], 'privacy-policy' => ['meta'], 'products' => ['meta', 'title', 'h1'], 'solutions' => ['meta', 'title', 'h1'], 'cortex.ai' => ['meta', 'title', 'h1'], 'cortex.intercompany' => ['meta', 'title', 'h1'], 'cortex.production' => ['meta', 'title', 'h1'], 'cortex.project-management' => ['meta', 'title', 'h1'], 'cortex.hr-payroll' => ['meta', 'title', 'h1'], 'cortex.pos' => ['meta', 'title', 'h1'], 'cortex.property-management' => ['meta', 'title', 'h1'], 'cortex.assets-management' => ['meta', 'title', 'h1']];
    foreach (pages() as $key => $p) {
        foreach (['en', 'ar'] as $lang) {
            $c = page_content($key, $lang);
            foreach (['title', 'meta', 'h1'] as $field) {
                if (in_array($field, $notInSpec[$key] ?? [], true)) continue;
                $v = plain($c[$field] ?? '');
                check($v !== '' && str_contains($specWords, $words($v)), "$key [$lang] $field not found in spec (same words): '$v'");
            }
        }
    }
} else {
    echo "  – spec file not found, skipped\n";
}

echo "== Structured data: Google rich-result required fields (spec D: Home, SAP pillar FAQ, Contact LocalBusiness, articles)\n";
function check_jsonld(array $j, string $label): void
{
    $type = $j['@type'] ?? '?';
    $abs = fn($u) => is_string($u) && preg_match('~^https?://~', $u);
    switch ($type) {
        case 'Organization':
            check(!empty($j['name']) && $abs($j['url'] ?? null) && $abs($j['logo'] ?? null), "$label: Organization needs name, url, logo");
            break;
        case 'ProfessionalService':
            $a = $j['address'] ?? [];
            check(!empty($j['name']) && !empty($j['telephone']) && !empty($a['streetAddress']) && !empty($a['addressLocality']) && !empty($a['addressCountry']), "$label: LocalBusiness needs name, telephone, full address");
            check(!empty($j['openingHoursSpecification']), "$label: LocalBusiness openingHours");
            break;
        case 'FAQPage':
            check(!empty($j['mainEntity']), "$label: FAQPage has no questions");
            foreach ($j['mainEntity'] ?? [] as $q) check(($q['@type'] ?? '') === 'Question' && trim($q['name'] ?? '') !== '' && trim($q['acceptedAnswer']['text'] ?? '') !== '', "$label: FAQ question/answer empty");
            break;
        case 'BreadcrumbList':
            foreach ($j['itemListElement'] ?? [] as $i => $li) check(($li['position'] ?? 0) === $i + 1 && trim($li['name'] ?? '') !== '' && $abs($li['item'] ?? null), "$label: breadcrumb item $i invalid");
            break;
        case 'Article':
            check(trim($j['headline'] ?? '') !== '' && mb_strlen($j['headline']) <= 110, "$label: Article headline missing or > 110 chars");
            check(!empty($j['image']), "$label: Article image missing");
            check(!empty($j['author']) && !empty($j['publisher']), "$label: Article author/publisher missing");
            break;
        case 'JobPosting':
            check(!empty($j['title']) && !empty($j['description']) && !empty($j['datePosted']) && !empty($j['hiringOrganization']['name']) && !empty($j['jobLocation']['address']['addressCountry']), "$label: JobPosting required fields");
            break;
        case 'HowTo':
            check(count($j['step'] ?? []) >= 2, "$label: HowTo needs steps");
            break;
        case 'Service':
            check(!empty($j['name']) && !empty($j['provider']), "$label: Service name/provider");
            break;
    }
}
$ldUrls = [];
foreach (pages() as $p) if ($p['enabled'] && !$p['noindex']) foreach (['', '/ar'] as $pre) $ldUrls[] = $pre . $p['path'];
foreach (db_all("SELECT slug, lang FROM insights WHERE status IN ('published'" . (is_staging() ? ",'draft'" : '') . ')') as $a) $ldUrls[] = ($a['lang'] === 'ar' ? '/ar' : '') . '/insights/' . $a['slug'] . '/';
foreach ($ldUrls as $u) {
    [$code, $html] = fetch($base . $u);
    if ($code !== 200) continue;
    preg_match_all('~<script type="application/ld\+json">(.*?)</script>~s', $html, $m);
    foreach ($m[1] as $raw) {
        $j = json_decode($raw, true);
        if (is_array($j)) check_jsonld($j, $u);
    }
}
foreach (db_all("SELECT slug, lang FROM insights WHERE status = 'published' AND published_at IS NULL") as $a) {
    check(false, "published article {$a['lang']}/{$a['slug']} has no published date (Article datePublished is required)");
}

echo "== Insights articles (DB)\n";
foreach (db_all("SELECT slug, lang FROM insights WHERE status IN ('published'" . (is_staging() ? ",'draft'" : '') . ')') as $a) {
    $path = ($a['lang'] === 'ar' ? '/ar' : '') . '/insights/' . $a['slug'] . '/';
    [$code, $html] = fetch($base . $path);
    check($code === 200, "article $path: HTTP $code");
    $x = dom($html);
    check($x->query('//h1')->length === 1, "article $path: expected one <h1>");
    $types = [];
    foreach ($x->query('//script[@type="application/ld+json"]') as $s) $types[] = json_decode($s->textContent, true)['@type'] ?? '?';
    check(in_array('Article', $types, true) && in_array('BreadcrumbList', $types, true), "article $path: Article/BreadcrumbList schema");
}

echo "== Redirects (A5)\n";
foreach (redirect_map() as $from => $to) {
    [$code, , $loc] = fetch($base . $from);
    check($code === 301 && str_ends_with((string) $loc, rtrim(cfg('base_path'), '/') . $to), "redirect $from → $to (got $code $loc)");
}
[$code, , $loc] = fetch($base . '/career/some-old-job/');
check($code === 301 && str_ends_with((string) $loc, '/careers/'), "redirect /career/* → /careers/ (got $code)");
foreach (gone_paths() as $g) {
    [$code] = fetch($base . $g);
    check($code === 410, "$g should be 410 (got $code)");
}
[$code, , $loc] = fetch($base . '/About');
check($code === 301, "uppercase URL should 301 to lowercase (got $code)");
[$code, , $loc] = fetch($base . '/about');
check($code === 301 && str_ends_with((string) $loc, '/about/'), "missing trailing slash should 301 (got $code)");
[$code, $html] = fetch($base . '/does-not-exist/');
check($code === 404 && str_contains($html, 'noindex'), "404 page should be 404 + noindex (got $code)");

echo "== Sitemaps + robots\n";
[$code, $xml] = fetch($base . '/sitemap-index.xml');
check($code === 200 && str_contains($xml, '/ar/sitemap.xml'), 'sitemap index');
foreach (['en' => '/sitemap.xml', 'ar' => '/ar/sitemap.xml'] as $lang => $sm) {
    [$code, $xml] = fetch($base . $sm);
    check($code === 200, "$sm HTTP $code");
    preg_match_all('~<loc>([^<]+)</loc>~', $xml, $m);
    check(count($m[1]) > 10, "$sm lists only " . count($m[1]) . ' URLs');
    foreach ($m[1] as $loc) {
        $local = str_replace($siteBase, $base, html_entity_decode($loc));
        [$c, $h] = fetch($local);
        check($c === 200 && !str_contains($h, 'content="noindex'), "$sm: $loc is not a live indexable page ($c)");
        foreach (array_keys(redirect_map()) as $old) check(!str_ends_with($loc, rtrim(cfg('base_path'), '/') . $old), "$sm lists old URL $loc");
    }
}
[$code, $txt] = fetch($base . '/robots.txt');
check($code === 200 && (cfg('env') !== 'production' || str_contains($txt, 'sitemap-index.xml')), 'robots.txt');

echo "== Source scans\n";
// Every file the stylesheet points to must exist (a deleted image otherwise shows up as a console 404)
$css = file_get_contents(ROOT_DIR . '/public/assets/css/site.css');
preg_match_all('~url\(\s*["\']?(?!data:|https?:)([^"\')?#]+)~', $css, $m);
foreach (array_unique($m[1]) as $ref) check(is_file(ROOT_DIR . '/public/assets/css/' . $ref), "site.css references a missing file: $ref");

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ROOT_DIR, FilesystemIterator::SKIP_DOTS));
foreach ($files as $f) {
    $p = $f->getPathname();
    if (preg_match('~/(\.git|storage|node_modules|refdoc|vendor)/|\.(png|jpe?g|webp|avif|gif|ico|pdf|mp4|woff2?)$~', $p)) continue;
    if (str_ends_with($p, 'tests/run.php')) continue;
    $src = file_get_contents($p);
    $rel = substr($p, strlen(ROOT_DIR) + 1);
    check(!preg_match('~[\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]~u', $src), "$rel contains Arabic presentation-form characters (U+FB50–U+FEFF)");
    if (str_starts_with($rel, 'content/') || str_starts_with($rel, 'database/') || str_starts_with($rel, 'app/lang/')) {
        check(!preg_match('~reseller|licen[cs]e sales|authori[sz]ed distributor~i', $src), "$rel contains forbidden reseller wording");
        check(!preg_match('~lorem ipsum|No Post Found~i', $src), "$rel contains filler text");
    }
}

echo "== Forms\n";
[$code, $html] = fetch($base . '/contact/');
$x = dom($html);
$form = $x->query('//form[@data-lead-form]')->item(0);
check((bool) $form, 'contact page has a lead form');
if ($form) {
    foreach (array_merge(TRACKING_FIELDS, ['page_language', '_token', 'website']) as $hf) {
        check($x->query('.//input[@name="' . $hf . '"]', $form)->length === 1, "lead form missing hidden field $hf");
    }
    check($x->query('.//input[@name="consent"][@required]', $form)->length === 1, 'lead form consent checkbox');
}
// Honeypot submission must be rejected (no redirect to thank-you).
$ch = curl_init($base . '/api/lead/');
curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_POSTFIELDS => http_build_query(['website' => 'spam', 'page_language' => 'en', 'page_path' => cfg('base_path') . '/contact/', '_token' => form_token()])]);
curl_exec($ch);
check(curl_getinfo($ch, CURLINFO_HTTP_CODE) === 422, 'honeypot submission should be rejected');

echo "\n" . ($fail ? "FAILED: $fail check(s) failed, $pass passed.\n" : "OK: all $pass checks passed.\n");
exit($fail ? 1 : 0);
