<?php
/** trust_strip (B6) */
$n = cfg('certified_consultants') ?: '[CONFIRM: 4]';
$items = [
    ['img/badges/sap-partner.png', t('trust.sap')],
    ['img/badges/odoo-silver-partner.webp', t('trust.odoo')],
    ['img/badges/future-factories.png', t('trust.ff')],
];
$facts = [sprintf(t('trust.consultants'), $n), t('trust.group'), t('trust.riyadh')];
$clients = db_all('SELECT name, logo FROM client_logos WHERE approved = 1 ORDER BY sort, id');
?>
<section class="trust-strip" aria-label="<?= e(t('trust.sap')) ?>">
  <div class="container">
    <ul class="trust-badges">
      <?php // Pages with a partner_band (home) already show the partner logos, so the strip keeps only the facts
      if (($s['badges'] ?? true) === false) $items = []; ?>
      <?php foreach ($items as $k => [$src, $alt]): ?>
      <?php if ($k === 0): // SAP partnership leads the strip (text on the dark strip; the official logo is shown on light sections only) ?>
      <li class="trust-primary"><?= icon('shield') ?><span><?= e($alt) ?><small>SAP Business One</small></span></li>
      <?php continue; endif; ?>
      <li>
        <?php if (asset_exists($src)): ?><img src="<?= e(asset($src)) ?>" alt="<?= e($alt) ?>" width="120" height="48" loading="lazy">
        <?php else: ?><span class="trust-text"><?= icon('shield') ?><?= e($alt) ?></span><?php endif; ?>
      </li>
      <?php endforeach; ?>
      <?php foreach ($facts as $f): ?><li><span class="trust-text"><?= icon('check') ?><?= rich($f) ?></span></li><?php endforeach; ?>
    </ul>
    <?php
    $logos = array_values(array_filter([
        ['img/partners/sap.webp', 'SAP'], ['img/partners/odoo.webp', 'Odoo'], ['img/partners/microsoft.webp', 'Microsoft'],
        ['img/partners/huawei.webp', 'Huawei'], ['img/partners/cisco.webp', 'Cisco'], ['img/partners/fortinet.webp', 'Fortinet'],
        ['img/partners/schneider-electric.webp', 'Schneider Electric'],
    ], fn($l) => asset_exists($l[0])));
    if ($logos): ?>
    <p class="marquee-label"><?= e(is_ar() ? 'منظومة التقنيات والمنصات' : 'Technology & platform ecosystem') ?></p>
    <div class="marquee" aria-label="<?= e(is_ar() ? 'منظومة التقنيات والمنصات' : 'Technology & platform ecosystem') ?>">
      <div class="marquee-track">
        <?php foreach ([0, 1, 2, 3] as $copy): foreach ($logos as [$src, $name]): ?>
          <img src="<?= e(asset($src)) ?>" alt="<?= e($name) ?>"<?= $copy ? ' aria-hidden="true"' : '' ?> width="120" height="34" loading="lazy">
        <?php endforeach; endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
    <?php if ($clients): ?>
    <ul class="logo-row" aria-label="<?= e(t('trust.clients')) ?>">
      <?php foreach ($clients as $c): ?><li><img src="<?= e(asset($c['logo'])) ?>" alt="<?= e($c['name']) ?>" width="120" height="60" loading="lazy"></li><?php endforeach; ?>
    </ul>
    <?php elseif (show_markers()): ?>
    <p class="center"><?= rich('[CONFIRM: client logos with written permission]') ?></p>
    <?php endif; ?>
  </div>
</section>
