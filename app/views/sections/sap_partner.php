<?php
/** sap_partner: the SAP Business One partnership as the company's core. Copy lives in lang 'sap_partner'. Options: h2 (override), bg, others (bool, show the secondary Odoo line) */
$sp = t('sap_partner');
$n = cfg('certified_consultants') ?: '[CONFIRM: 4]';
$img = ['src' => 'img/gen/sap-implementation.webp', 'alt' => $sp['img_alt'], 'w' => 1600, 'h' => 1067, 'sizes' => '(min-width: 1000px) 45vw, 100vw'];
?>
<section class="<?= section_class($s, 'sap-partner') ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container sap-partner-grid">
    <div class="sap-partner-copy">
      <?php if (sap_partner_logo()): ?><div class="sap-partner-mark"><?= sap_partner_logo(44) ?></div><?php endif; ?>
      <p class="eyebrow"><?= e($sp['eyebrow']) ?></p>
      <h2><?= rich($s['h2'] ?? $sp['h2']) ?></h2>
      <p class="sap-partner-lead"><?= rich($sp['body']) ?></p>
      <ul class="sap-pillars">
        <?php foreach ($sp['pillars'] as [$ic, $title, $text]): ?>
        <li><span class="sap-pillar-icon"><?= icon($ic) ?></span><div><h3><?= e($title) ?></h3><p><?= e($text) ?></p></div></li>
        <?php endforeach; ?>
      </ul>
      <div class="btn-row"><?php foreach ($sp['buttons'] as $i => $b) echo button($b + [2 => $i === 0 ? 'primary' : 'secondary']); ?></div>
      <?php if (!empty($s['others'])): ?>
      <p class="sap-partner-others"><?= e($sp['others']) ?> <a href="<?= e(url($sp['others_link'][1])) ?>"><?= e($sp['others_link'][0]) ?></a></p>
      <?php endif; ?>
    </div>
    <div class="sap-partner-media">
      <div class="framed"><?= img($img) ?></div>
      <div class="sap-partner-stat"><strong><?= rich($n) ?></strong><span><?= e($sp['stat_label']) ?></span></div>
    </div>
  </div>
</section>
