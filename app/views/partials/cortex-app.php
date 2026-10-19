<?php
/**
 * Cortex add-on application screen drawn in HTML (sharp at any size), shown inside the product_hero laptop.
 * $w: [logo, label (accessible description), nav[[icon, label]] (first is active), title, period,
 *      kpis[[label, value, note]] (4), chart[title, bars[[label, value]]], table[title, cols[], rows[[...]], status col index],
 *      dark, date (for the Hijri date), ar (same keys in Arabic, used on Arabic pages)]
 * Sizes use container units (1cqw is about 10px on a 1000px wide screen) so the screen scales with the laptop.
 */
$statusCol = $w['table']['status'] ?? null;
$enRows = $w['table']['rows'];               // status colours come from the English status words
$ar = is_ar() && !empty($w['ar']);           // Arabic pages show the Arabic version of the screen, right to left
if ($ar) $w = array_replace_recursive($w, $w['ar']);
$max = max(array_column($w['chart']['bars'], 1)) ?: 1;
$hijri = hijri($w['date'] ?? '2026-10-12', $ar ? 'ar' : 'en');
?>
<div class="ca<?= !empty($w['dark']) ? ' pdx' : '' ?>" dir="<?= $ar ? 'rtl' : 'ltr' ?>" lang="<?= $ar ? 'ar' : 'en' ?>" role="img" aria-label="<?= e($w['label']) ?>">
  <aside class="ca-side">
    <?php if (!empty($w['logo']) && asset_exists($w['logo'])): [$lw, $lh] = getimagesize(ROOT_DIR . '/public/assets/' . $w['logo']); ?><img src="<?= e(asset($w['logo'])) ?>" alt="<?= e(($w['brand'] ?? '') ?: (str_contains($w['logo'], 'sap-business-one') ? 'SAP Business One' : 'Cortex')) ?>" width="<?= (int) $lw ?>" height="<?= (int) $lh ?>" loading="lazy"><?php elseif (!empty($w['brand'])): ?><strong class="ca-brand"><?= e($w['brand']) ?></strong><?php endif; ?>
    <ul><?php foreach ($w['nav'] as $i => [$ic, $label]): ?><li<?= $i ? '' : ' class="is-on"' ?>><?= icon($ic) ?><?= e($label) ?></li><?php endforeach; ?></ul>
    <p class="ca-co"><?= icon('briefcase') ?><?= $ar ? 'شركة أو إي سي للحاسبات' : 'OEC Computers' ?></p>
  </aside>
  <div class="ca-main">
    <div class="ca-top"><strong><?= e($w['title']) ?></strong><span class="ca-period"><?= icon('calendar') ?><?= e($w['period']) ?><?php if ($hijri): ?><em class="ca-hijri"><?= e($hijri) ?></em><?php endif; ?></span><b class="ca-user">JB</b></div>
    <div class="ca-kpis"><?php foreach ($w['kpis'] as [$l, $v, $n]): ?><div><span><?= e($l) ?></span><strong><?= e($v) ?></strong><em><?= e($n) ?></em></div><?php endforeach; ?></div>
    <div class="ca-row">
      <div class="ca-card">
        <p class="ca-card-t"><?= e($w['chart']['title']) ?></p>
        <div class="ca-bars"><?php foreach ($w['chart']['bars'] as $i => [$l, $v]): ?><div style="--h:<?= round(100 * $v / $max) ?>%;--i:<?= $i ?>"><i></i><span><?= e($l) ?></span></div><?php endforeach; ?></div>
      </div>
      <div class="ca-card">
        <p class="ca-card-t"><?= e($w['table']['title']) ?></p>
        <table class="ca-table">
          <thead><tr><?php foreach ($w['table']['cols'] as $c): ?><th><?= e($c) ?></th><?php endforeach; ?></tr></thead>
          <tbody><?php foreach ($w['table']['rows'] as $ri => $r): ?><tr><?php foreach ($r as $ci => $cell): ?><td><?php if ($ci === $statusCol): ?><span class="ca-st ca-st-<?= e(strtolower(strtok($enRows[$ri][$ci] ?? $cell, ' '))) ?>"><?= e($cell) ?></span><?php else: ?><?= e($cell) ?><?php endif; ?></td><?php endforeach; ?></tr><?php endforeach; ?></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
