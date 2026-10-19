<?php
/** partner_band: light strip with the official partner logos in their own colours (SAP first). */
$logos = official_partner_logos();
if (!$logos) return;
?>
<section class="partner-band" aria-label="<?= e(t('partner_band')) ?>">
  <div class="container partner-band-inner">
    <p class="partner-band-label"><?= e(t('partner_band')) ?></p>
    <ul class="partner-band-logos">
      <?php foreach ($logos as $logo): ?><li><?= $logo ?></li><?php endforeach; ?>
    </ul>
  </div>
</section>
