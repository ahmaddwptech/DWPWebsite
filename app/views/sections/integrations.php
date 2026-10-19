<?php
/**
 * integrations: eyebrow, h2, intro, bg, logo (product logo path), note, items[{name, logo (optional, under assets), text, icon}]
 * The local systems a Cortex add-on works with: the product in the middle, the systems around it, each with a live
 * connection line. Real logos where we have them, otherwise the system name set as a wordmark.
 */
$items = $s['items'] ?? [];
if (!$items) return;
$logo = !empty($s['logo']) && asset_exists($s['logo']) ? $s['logo'] : null;
$half = (int) ceil(count($items) / 2);
$tile = function ($it) {
    $img = !empty($it['logo']) && asset_exists($it['logo']) ? $it['logo'] : null;
    ob_start(); ?>
    <li class="ig-tile">
      <span class="ig-mark"><?php if ($img): [$w, $h] = getimagesize(ROOT_DIR . '/public/assets/' . $img); ?><img src="<?= e(asset($img)) ?>" alt="<?= e(plain($it['name'])) ?>" width="<?= (int) $w ?>" height="<?= (int) $h ?>" loading="lazy"><?php else: ?><span class="ig-word"><?= icon($it['icon'] ?? 'link') ?></span><?php endif; ?></span>
      <span class="ig-name"><?= rich($it['name']) ?></span>
      <span class="ig-text"><?= rich($it['text']) ?></span>
      <span class="ig-link" aria-hidden="true"><i></i></span>
    </li>
    <?php return ob_get_clean();
};
?>
<section class="<?= section_class($s, 'ig-section') ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container">
    <?= section_head($s) ?>
    <div class="ig">
      <ul class="ig-side"><?php foreach (array_slice($items, 0, $half) as $it) echo $tile($it); ?></ul>
      <div class="ig-hub"><span class="ig-ring" aria-hidden="true"></span><span class="ig-core"><?php if ($logo): [$lw, $lh] = getimagesize(ROOT_DIR . '/public/assets/' . $logo); ?><img src="<?= e(asset($logo)) ?>" alt="<?= e($s['logo_alt'] ?? 'Cortex') ?>" width="<?= (int) $lw ?>" height="<?= (int) $lh ?>" loading="lazy"><?php elseif (!empty($s['brand'])): ?><strong class="ig-brand"><?= rich($s['brand']) ?></strong><?php endif; ?><?php if (empty($s['no_sap'])): ?><?= sap_b1_logo(20) ?><?php endif; ?></span></div>
      <ul class="ig-side is-right"><?php foreach (array_slice($items, $half) as $it) echo $tile($it); ?></ul>
    </div>
    <?php if (!empty($s['note'])): ?><p class="oc-note"><?= rich($s['note']) ?></p><?php endif; ?>
  </div>
</section>
