<?php
/**
 * Live product layout for any page that has a file in content/live/<page key>.php (Odoo, DWP platforms, solutions,
 * SAP Business One services, industries). Applied by page_content() before the FAQ is moved last:
 *   product_hero (laptop with the page's application screen, built from the page's own hero sub and buttons)
 *   results (outcomes) > the page's existing sections > clickable process > live control room > works with
 *   > the page's lead form > FAQ > contact band.
 * The page keeps its title, meta, H1, body content and FAQ. Live file keys:
 *   brand {en, ar} (text shown as the product name) or logo (path), accent [colour, colour],
 *   screen {en, ar} (partials/cortex-app data), outcomes {en, ar} (items), process {en, ar}, control {en, ar},
 *   room (English room for sections/ops_control.php), room_ar (English text => Arabic), integrations {en, ar} (items),
 *   industry_hero {photo, photo_alt{en,ar}, icon, eyebrow{en,ar}, metrics{en,ar}, note{en,ar}} (industries: photo first section with live figures),
 *   no_sap (true: the works with hub shows only this product, e.g. Odoo), screen_logo (logo inside the laptop screen;
 *   without brand and logo the first section shows the SAP Business One logo)
 * Helpers available inside live files: $R (Arabic review marker), $o (outcome), $g (integration), $st (process step).
 */
if (!function_exists('live_layout')) {
function live_layout(array $c, string $key, string $lang): array
{
    $file = __DIR__ . '/live/' . $key . '.php';
    if (empty($c['sections']) || !is_file($file)) return $c;
    static $cache = [];
    $L = $cache[$key] ??= (static function () use ($file) {
        $R = fn(string $t) => '[AR-REVIEW]' . $t . '[/AR-REVIEW]';
        $o = fn($v, $pre, $suf, $label, $before = null, $after = null) => ['value' => $v, 'prefix' => $pre, 'suffix' => $suf, 'label' => $label, 'before' => $before, 'after' => $after];
        $g = fn($name, $text, $icon = 'link', $logo = null) => ['name' => $name, 'text' => $text, 'icon' => $icon, 'logo' => $logo];
        $st = fn($icon, $title, $text, array $f) => ['icon' => $icon, 'title' => $title, 'text' => $text, 'functions' => $f];
        return include $file;
    })();
    $ar = $lang === 'ar';
    $pick = fn($k) => $L[$k][$lang] ?? $L[$k]['en'] ?? null;
    $heads = $ar
        ? ['oc' => ['[AR-REVIEW]النتائج[/AR-REVIEW]', '[AR-REVIEW]ما الذي يتغير معنا[/AR-REVIEW]'], 'ig' => ['[AR-REVIEW]يعمل مع[/AR-REVIEW]', '[AR-REVIEW]متصل بالأنظمة التي تستخدمها اليوم[/AR-REVIEW]'], 'oc_note' => '[AR-REVIEW]نتائج معتادة من مشاريع DWP.[/AR-REVIEW] [CONFIRM: outcome figures]']
        : ['oc' => ['Results', 'What changes with DWP'], 'ig' => ['Works with', 'Connected to the systems you already use'], 'oc_note' => 'Typical results from DWP projects. [CONFIRM: outcome figures]'];

    $hero = null; $rest = []; $lead = []; $faqEtc = [];
    foreach ($c['sections'] as $s) {
        $t = $s['type'] ?? '';
        if (!$hero && in_array($t, ['hero', 'product_hero'], true)) $hero = $s;
        elseif ($t === 'lead_form') $lead[] = $s;
        elseif (in_array($t, ['faq', 'cta_band'], true)) $faqEtc[] = $s;
        elseif ($t === 'steps' && !empty($L['process'])) continue;   // the clickable process replaces the plain steps list
        else $rest[] = $s;
    }
    $screen = $pick('screen');
    $out = [];
    if (!empty($L['industry_hero'])) {   // industries: their own first section (photo and live figures), not the laptop
        $ih = $L['industry_hero'];
        $out[] = ['type' => 'ind_hero', 'photo' => $ih['photo'], 'photo_alt' => $ih['photo_alt'][$lang] ?? null, 'icon' => $ih['icon'] ?? 'factory', 'accent' => $L['accent'] ?? null,
            'eyebrow' => $ih['eyebrow'][$lang] ?? '', 'metrics' => $ih['metrics'][$lang] ?? [], 'note' => $ih['note'][$lang] ?? '',
            'sub' => $hero['sub'] ?? '', 'buttons' => $hero['buttons'] ?? []];
    } else
    $out[] = ['type' => 'product_hero', 'logo' => $L['logo'] ?? null, 'logo_alt' => $L['brand']['en'] ?? '', 'brand' => $pick('brand'), 'accent' => $L['accent'] ?? null,
        'sub' => $hero['sub'] ?? '', 'buttons' => $hero['buttons'] ?? [],
        'window' => ['partial' => 'cortex-app', 'data' => ($L['screen']['en'] ?? []) + ['dark' => true, 'logo' => $L['screen_logo'] ?? $L['logo'] ?? null, 'brand' => $L['brand']['en'] ?? '',
            'label' => ($L['brand']['en'] ?? '') . ' screen: ' . ($L['screen']['en']['title'] ?? ''), 'ar' => $L['screen']['ar'] ?? null]]];
    if ($items = $pick('outcomes')) $out[] = ['type' => 'outcomes', 'id' => 'results', 'eyebrow' => $heads['oc'][0], 'h2' => $heads['oc'][1], 'note' => $heads['oc_note'], 'items' => $items];
    array_push($out, ...$rest);
    if ($p = $pick('process')) $out[] = ['type' => 'prod_process', 'bg' => 'mesh', 'id' => 'process'] + $p;
    if (!empty($L['room']) && ($ctl = $pick('control'))) {
        $room = $L['room'];
        if ($ar) {
            $dict = $L['room_ar'] ?? [];
            $tr = function ($v, $k = null) use (&$tr, $dict) {
                if (is_array($v)) { foreach ($v as $kk => $x) $v[$kk] = $tr($x, is_string($kk) ? $kk : $k); return $v; }
                if (!is_string($v) || in_array($k, ['id', 'status', 'kind', 'logkind', 'card', 'type'], true)) return $v;
                return $dict[$v] ?? $v;
            };
            $room = $tr($room) + ['lang' => 'ar'];
        }
        $out[] = ['type' => 'ops_control', 'bg' => 'dark', 'id' => 'control-room', 'room' => $room] + $ctl;
    }
    if ($items = $pick('integrations')) $out[] = ['type' => 'integrations', 'id' => 'integrations', 'logo' => $L['logo'] ?? null, 'brand' => $pick('brand'), 'no_sap' => !empty($L['no_sap']), 'eyebrow' => $heads['ig'][0], 'h2' => $heads['ig'][1], 'note' => '[CONFIRM: integration list and scope]', 'items' => $items];
    array_push($out, ...$lead, ...$faqEtc);
    $c['sections'] = $out;
    return $c;
}
}
