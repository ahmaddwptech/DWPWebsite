<?php

function cfg(string $key, $default = null)
{
    $v = $GLOBALS['config'];
    foreach (explode('.', $key) as $k) {
        if (!is_array($v) || !array_key_exists($k, $v)) return $default;
        $v = $v[$k];
    }
    return $v;
}

function is_staging(): bool
{
    return cfg('env') !== 'production';
}

/** On-screen review notes ([CONFIRM] tags, AR-REVIEW underlines, staging bar). Off unless config sets 'show_markers' => true on staging. */
function show_markers(): bool
{
    return is_staging() && (bool) cfg('show_markers');
}

function e($s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Current page language ('en' | 'ar'). */
function lang(): string
{
    return $GLOBALS['lang'] ?? 'en';
}

function is_ar(?string $lang = null): bool
{
    return ($lang ?? lang()) === 'ar';
}

/**
 * Build a site URL. $path is the language-neutral path ('/', '/sap-business-one/').
 * Adds base path, '/ar' prefix for Arabic and a trailing slash.
 */
function url(string $path = '/', ?string $lang = null, bool $absolute = false): string
{
    $lang = $lang ?? lang();
    if (preg_match('~^(https?:|mailto:|tel:|#)~', $path)) return $path;
    $query = '';
    if (($q = strpos($path, '?')) !== false) { $query = substr($path, $q); $path = substr($path, 0, $q); }
    $frag = '';
    if (($h = strpos($path, '#')) !== false) { $frag = substr($path, $h); $path = substr($path, 0, $h); }
    $path = '/' . trim($path, '/');
    $out = rtrim(cfg('base_path'), '/') . ($lang === 'ar' ? '/ar' : '') . ($path === '/' ? '/' : $path . '/');
    if ($absolute) $out = rtrim(cfg('base_url'), '/') . $out;
    return $out . $query . $frag;
}

function asset(string $file): string
{
    $full = ROOT_DIR . '/public/assets/' . ltrim($file, '/');
    // On Vercel every file has the same mtime, so version by commit instead.
    $sha = getenv('VERCEL_GIT_COMMIT_SHA');
    $v = is_file($full) ? substr($sha ?: md5((string) filemtime($full)), 0, 8) : '';
    return rtrim(cfg('base_path'), '/') . '/assets/' . ltrim($file, '/') . ($v ? '?v=' . $v : '');
}

function asset_exists(string $file): bool
{
    return is_file(ROOT_DIR . '/public/assets/' . ltrim($file, '/'));
}

/**
 * Render a content string to safe HTML.
 * Supports: **bold**, [text](/path/) links (language-aware), [CONFIRM: …] and [AR-REVIEW]…[/AR-REVIEW] markers.
 */
function rich(?string $s): string
{
    if ($s === null || $s === '') return '';
    $html = e($s);
    $html = preg_replace('~\*\*(.+?)\*\*~u', '<strong>$1</strong>', $html);
    $html = preg_replace_callback('~\[([^\]]+)\]\(([^)\s]+)\)~u', function ($m) {
        $href = html_entity_decode($m[2], ENT_QUOTES);
        return '<a href="' . e(url($href)) . '">' . $m[1] . '</a>';
    }, $html);
    $html = preg_replace_callback('~\[CONFIRM(?::\s*([^\]]*))?\]~u', function ($m) {
        $label = trim($m[1] ?? '');
        if (!show_markers()) return $label !== '' && preg_match('~^[\d\s–\-+]+$~u', $label) ? $label : '';
        return '<mark class="confirm" title="To be confirmed by DWP">[CONFIRM' . ($label !== '' ? ': ' . $label : '') . ']</mark>';
    }, $html);
    $html = preg_replace_callback('~\[AR-REVIEW\](.*?)\[/AR-REVIEW\]~us', function ($m) {
        return show_markers() ? '<span class="ar-review" title="Arabic to be reviewed by DWP">' . $m[1] . '</span>' : $m[1];
    }, $html);
    return $html;
}

/** Plain text for meta tags / JSON-LD: strips markers and markdown. */
function plain(?string $s): string
{
    $s = (string) $s;
    $s = preg_replace('~\[/?AR-REVIEW\]~u', '', $s);
    $s = preg_replace('~\s*\[CONFIRM(?::[^\]]*)?\]~u', '', $s);
    $s = preg_replace('~\*\*(.+?)\*\*~u', '$1', $s);
    $s = preg_replace('~\[([^\]]+)\]\(([^)\s]+)\)~u', '$1', $s);
    return trim(preg_replace('~\s+~u', ' ', $s));
}

/** A config fact, or a visible [CONFIRM] placeholder when missing. */
function fact(string $key, string $label): string
{
    $v = cfg($key);
    return ($v === '' || $v === null) ? '[CONFIRM: ' . $label . ']' : (string) $v;
}

function redirect(string $to, int $code = 302): void
{
    header('Location: ' . $to, true, $code);
    exit;
}

function client_ip(): string
{
    return $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
}

function log_line(string $file, string $msg): void
{
    $dir = cfg('log_dir');
    if (!is_dir($dir)) @mkdir($dir, 0775, true);
    @file_put_contents($dir . '/' . $file, '[' . date('c') . '] ' . $msg . PHP_EOL, FILE_APPEND);
}

function wa_link(string $message): ?string
{
    $n = preg_replace('~\D~', '', (string) cfg('whatsapp'));
    if ($n === '') return null;
    return 'https://wa.me/' . $n . '?text=' . rawurlencode($message);
}

function format_date(string $date, ?string $lang = null): string
{
    $ts = strtotime($date);
    if (!$ts) return $date;
    if (($lang ?? lang()) === 'ar') {
        $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        return date('j', $ts) . ' ' . $months[(int) date('n', $ts) - 1] . ' ' . date('Y', $ts);
    }
    return date('j F Y', $ts);
}

function employment_label(string $type): string
{
    $map = [
        'FULL_TIME' => ['Full-time', 'دوام كامل'], 'PART_TIME' => ['Part-time', 'دوام جزئي'],
        'CONTRACTOR' => ['Contract', 'عقد'], 'INTERN' => ['Internship', 'تدريب'],
    ];
    return $map[$type][is_ar() ? 1 : 0] ?? $type;
}

/** Hijri (Umm al-Qura) date for the screens, e.g. "1 Jumada I 1448" or "١ جمادى الأولى ١٤٤٨ هـ". */
function hijri(string $date = '2026-10-12', ?string $lang = null): string
{
    if (!class_exists('IntlDateFormatter')) return '';
    $ar = is_ar($lang);
    $f = new IntlDateFormatter(($ar ? 'ar_SA' : 'en_US') . '@calendar=islamic-umalqura', IntlDateFormatter::NONE, IntlDateFormatter::NONE, 'Asia/Riyadh', IntlDateFormatter::TRADITIONAL, $ar ? 'd MMMM y' : 'd MMMM y');
    return (string) $f->format(new DateTime($date));
}
