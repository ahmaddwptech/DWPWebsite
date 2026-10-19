<?php
/**
 * ops_control: eyebrow, h2, intro, bg, hint, room{title, sub, clock, kpis[[key, label, value, fmt, note]],
 *   cards_title, cards[{id, title, status (run|idle|stop|chg), label, rows[[label, value, fmt]], bar (row index for the bar), max (bar max), actions[{label, kind, log, fx{kpi: delta}, rows{index: delta}, status, state (new status label), once}]}],
 *   inbox_title, inbox[{title, sub, actions[{label, kind (ok|bad), log, logkind, fx, done, card, status, state, rows}]}],
 *   chart_title, chart_kpi, chart[], log[[time, text, kind]],
 *   tick{ms, kpis[[key, min, max, chance]], cards[[id, row, min, max, kpi]]}, feed[{log, kind, fx, card, status, label, rows}]}
 * Shared live, clickable control room for the Cortex add-on pages (site.js): figures tick, events arrive, cards open
 * a drawer with actions and the approvals inbox reacts. Dark look built on the .pcr styles. Sample data, English UI.
 */
$r = $s['room'];
$ar = ($r['lang'] ?? 'en') === 'ar';          // Arabic room: right to left, Arabic words, amounts in riyals
$T = fn(string $en, string $a) => $ar ? $a : $en;
$fmt = function ($v, $f) use ($ar) {
    if ($ar) return match ($f) { 'sar' => number_format($v) . ' ريال', 'sarm' => number_format($v / 1e6, 2) . ' مليون ريال', 'sark' => number_format($v / 1e3) . ' ألف ريال',
        'pct' => number_format($v, 1) . '%', 'pct0' => round($v) . '%', 'text' => (string) $v, default => number_format($v) };
    return match ($f) { 'sar' => 'SAR ' . number_format($v), 'sarm' => 'SAR ' . number_format($v / 1e6, 2) . 'M', 'sark' => 'SAR ' . number_format($v / 1e3) . 'K',
        'pct' => number_format($v, 1) . '%', 'pct0' => round($v) . '%', 'text' => (string) $v, default => number_format($v) };
};
$cfg = ['lang' => $ar ? 'ar' : 'en', 'kpis' => [], 'cards' => [], 'inbox' => [], 'tick' => $r['tick'] ?? [], 'feed' => $r['feed'] ?? [], 'chart' => $r['chart_kpi'] ?? null,
    'words' => ['pending' => $T('pending', 'معلقة'), 'all' => $T('All done', 'لا يوجد معلق'), 'done' => $T('Done', 'تم')]];
foreach ($r['kpis'] as [$k, $l, $v, $f]) $cfg['kpis'][$k] = ['v' => $v, 'f' => $f];
foreach ($r['cards'] as $c) $cfg['cards'][$c['id']] = ['rows' => array_map(fn($x) => ['v' => $x[1], 'f' => $x[2] ?? 'int'], $c['rows']), 'bar' => $c['bar'] ?? null, 'max' => $c['max'] ?? 100, 'status' => $c['status'], 'label' => $c['label'], 'actions' => $c['actions'] ?? []];
foreach ($r['inbox'] as $i) $cfg['inbox'][] = $i['actions'];
?>
<section class="<?= section_class($s, 'pcr-section ops-section') ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container">
    <?= section_head($s) ?>
    <?php if (!empty($s['hint'])): ?><p class="pcr-hint"><?= icon('check') ?><?= rich($s['hint']) ?></p><?php endif; ?>
    <div class="pcr-wrap"><div class="pcr-frame">
      <div class="pcr ops" data-ops dir="<?= $ar ? 'rtl' : 'ltr' ?>" lang="<?= $ar ? 'ar' : 'en' ?>">
        <script type="application/json" data-ops-cfg><?= json_encode($cfg, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?></script>
        <div class="pcr-top">
          <div class="pcr-title"><strong><?= e($r['title']) ?></strong><span class="pcr-live"><i></i><?= $T('Live', 'مباشر') ?></span></div>
          <span class="pcr-clock" data-ops-clock><?= e($r['clock'] ?? '10:42') ?></span>
          <span class="pcr-date"><?= e($T('Mon 12 Oct 2026', 'الإثنين 12 أكتوبر 2026')) ?><?php if ($hj = hijri('2026-10-12', $ar ? 'ar' : 'en')): ?><em><?= e($hj) ?></em><?php endif; ?></span>
          <span class="pcr-shift"><?= e($r['sub']) ?></span>
        </div>
        <div class="pcr-kpis">
          <?php foreach ($r['kpis'] as [$k, $l, $v, $f, $n]): ?><div><span><?= e($l) ?></span><strong data-kpi="<?= e($k) ?>"<?= in_array($k, $r['warn'] ?? [], true) ? ' class="is-warn"' : '' ?>><?= e($fmt($v, $f)) ?></strong><em><?= e($n) ?></em></div><?php endforeach; ?>
        </div>
        <div class="pcr-main">
          <div class="pcr-floor">
            <p class="pcr-h"><?= e($r['cards_title']) ?> <small><?= $T('Click a card', 'اضغط على بطاقة') ?></small></p>
            <div class="ops-cards">
              <?php foreach ($r['cards'] as $c): ?>
              <button type="button" class="pcr-m ops-card is-<?= e($c['status']) ?>" data-ops-card="<?= e($c['id']) ?>">
                <span class="pcr-m-top"><b><?= e($c['title']) ?></b><span class="pcr-st"><i></i><span data-st><?= e($c['label']) ?></span></span></span>
                <?php foreach ($c['rows'] as $ri => $row): if ($ri > 2) break; ?><span class="pmc-row"><span><?= e($row[0]) ?></span><strong data-row="<?= $ri ?>"><?= e($fmt($row[1], $row[2] ?? 'int')) ?></strong></span><?php endforeach; ?>
                <?php if (isset($c['bar'])): ?><span class="pcr-bar"><i data-bar style="--p:<?= min(100, round($c['rows'][$c['bar']][1] / ($c['max'] ?? 100) * 100)) ?>%"></i></span><?php endif; ?>
              </button>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="pcr-side">
            <div class="pcr-card">
              <p class="pcr-h"><?= e($r['inbox_title']) ?> <span class="pcr-tag" data-ops-pending><?= count($r['inbox']) ?> <?= $T('pending', 'معلقة') ?></span></p>
              <ul class="pmc-inbox">
                <?php foreach ($r['inbox'] as $ii => $it): ?>
                <li data-ops-ap="<?= $ii ?>"><div><b><?= e($it['title']) ?></b><span><?= e($it['sub']) ?></span></div><div class="pmc-ap-btns"><?php foreach ($it['actions'] as $ai => $a): ?><button type="button" class="pcr-btn<?= !empty($a['kind']) ? ' is-' . e($a['kind']) : '' ?>" data-ap="<?= $ai ?>"><?= e($a['label']) ?></button><?php endforeach; ?></div></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>
        <div class="pcr-bottom">
          <div class="pcr-card pcr-chart">
            <p class="pcr-h"><?= e($r['chart_title']) ?></p>
            <div class="pcr-bars" data-ops-bars><?php foreach ($r['chart'] as $v): ?><i style="--h:<?= (int) $v ?>%"></i><?php endforeach; ?></div>
          </div>
          <div class="pcr-card pcr-log">
            <p class="pcr-h"><?= $T('Events', 'الأحداث') ?></p>
            <ul data-ops-log><?php foreach ($r['log'] as [$t, $x, $k]): ?><li<?= $k ? ' class="is-' . e($k) . '"' : '' ?>><time><?= e($t) ?></time><?= e($x) ?></li><?php endforeach; ?></ul>
          </div>
        </div>
        <aside class="pcr-drawer" data-ops-drawer aria-hidden="true">
          <button type="button" class="pcr-close" data-ops-close aria-label="<?= $T('Close', 'إغلاق') ?>">×</button>
          <p class="pcr-d-name" data-d-name></p>
          <p class="pcr-st pcr-d-st" data-d-st><i></i><span></span></p>
          <dl class="pcr-d-facts" data-d-facts></dl>
          <div class="pcr-d-actions" data-d-actions></div>
        </aside>
        <?php /* card titles and row labels for the drawer */ ?>
        <script type="application/json" data-ops-meta><?= json_encode(array_map(fn($c) => ['title' => $c['title'], 'rows' => array_column($c['rows'], 0)], array_column($r['cards'], null, 'id')), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?></script>
      </div>
    </div></div>
  </div>
</section>
