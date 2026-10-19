<?php
/** thank_you (C7): fires generate_lead (+ book_demo) via data attributes read by site.js */
$f = $_GET['f'] ?? 'lead';
$events = $f === 'cv' ? [] : ($f === 'demo' ? ['generate_lead', 'book_demo'] : ['generate_lead']);
?>
<section class="section page-hero">
  <div class="container narrow center" data-events="<?= e(implode(',', $events)) ?>" data-interest="<?= e(preg_replace('~[^a-z]~', '', $_GET['i'] ?? '')) ?>">
    <span class="success-icon"><?= icon('check') ?></span>
    <h1><?= rich($page['h1']) ?></h1>
    <p class="lead"><?= e($f === 'cv' ? t('thank_you.cv') : t('thank_you.text')) ?></p>
    <?php if ($f !== 'cv'): ?>
    <div class="btn-row center"><?= button([t('whatsapp_us'), 'whatsapp', 'whatsapp']) ?></div>
    <p><?= rich(t('thank_you.read')) ?></p>
    <?php endif; ?>
  </div>
</section>
