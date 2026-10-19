<?php
/**
 * cortex_connect: eyebrow, h2, intro, bg, logo (Cortex product logo path), labels[left, right], items[{icon, title, text}], no_sap
 * SAP Business One (or a plain ERP card when no_sap is set) and a Cortex product joined by a live animated connection, with short facts underneath.
 */
$logo = !empty($s['logo']) && asset_exists($s['logo']) ? $s['logo'] : null;
?>
<section class="<?= section_class($s, 'connect-section') ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container">
    <?= section_head($s) ?>
    <div class="connect" aria-hidden="true">
      <div class="connect-node">
        <span class="connect-card"><?php if (empty($s['no_sap'])): ?><?= sap_b1_logo(34) ?><?php else: ?><strong class="connect-erp">ERP</strong><?php endif; ?></span>
        <span class="connect-label"><?= rich($s['labels'][0] ?? '') ?></span>
      </div>
      <div class="connect-line"><i class="connect-pulse"></i><i class="connect-pulse connect-pulse-2"></i><i class="connect-pulse connect-pulse-3"></i></div>
      <div class="connect-node">
        <span class="connect-card"><?php if ($logo): [$lw, $lh] = getimagesize(ROOT_DIR . '/public/assets/' . $logo); ?><img src="<?= e(asset($logo)) ?>" alt="Cortex" width="<?= (int) $lw ?>" height="<?= (int) $lh ?>" loading="lazy"><?php endif; ?></span>
        <span class="connect-label"><?= rich($s['labels'][1] ?? '') ?></span>
      </div>
    </div>
    <ul class="connect-facts">
      <?php foreach ($s['items'] as $it): ?>
      <li><span class="connect-fact-icon"><?= icon($it['icon']) ?></span><div><h3><?= rich($it['title']) ?></h3><p><?= rich($it['text']) ?></p></div></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
