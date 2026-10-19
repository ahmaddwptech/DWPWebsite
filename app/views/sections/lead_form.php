<?php /** lead_form (B4): variant (default|demo|quote|short), interest, heading, sub */ ?>
<section class="section section-muted lead-section" id="<?= e($s['id'] ?? 'lets-talk') ?>">
  <div class="container form-container">
    <div class="form-intro">
      <h2><?= rich($s['heading'] ?? (t('form.headings')[$s['interest'] ?? ''] ?? t('form.heading'))) ?></h2>
      <p><?= rich($s['sub'] ?? t('form.sub')) ?></p>
      <ul class="contact-list">
        <li><?= icon('phone') ?><a href="tel:<?= e(cfg('phone_e164')) ?>" dir="ltr" data-track="phone_click"><?= e(cfg('phone')) ?></a></li>
        <?php if ($wa = wa_link(sprintf(t('wa_message'), page_name($page['key'], lang())))): ?>
        <li><?= icon('whatsapp') ?><a href="<?= e($wa) ?>" target="_blank" rel="noopener" data-track="whatsapp_click"><?= e(t('footer.whatsapp')) ?></a></li>
        <?php endif; ?>
        <li><?= icon('clock') ?><span><?= e(t('footer.hours')) ?></span></li>
      </ul>
    </div>
    <div class="card form-card">
      <?= view('partials/lead-form', ['variant' => $s['variant'] ?? 'default', 'interest' => $s['interest'] ?? $page['interest'], 'heading' => null, 'id' => 'lead-form-' . $index]) ?>
    </div>
  </div>
</section>
