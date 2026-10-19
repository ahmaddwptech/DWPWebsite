<?php
/** contact_cards: Phone · WhatsApp · Email · Address · Hours (B2 facts) */
$wa = wa_link(sprintf(t('wa_message'), page_name($page['key'], lang())));
$cards = [
    ['phone', t('footer.phone'), '<a href="tel:' . e(cfg('phone_e164')) . '" dir="ltr" data-track="phone_click">' . e(cfg('phone')) . '</a>'],
    ['whatsapp', t('footer.whatsapp'), $wa ? '<a href="' . e($wa) . '" target="_blank" rel="noopener" dir="ltr" data-track="whatsapp_click">+' . e(cfg('whatsapp')) . '</a>' : rich('[CONFIRM: WhatsApp number]')],
    ['mail', t('footer.email'), cfg('email') ? '<a href="mailto:' . e(cfg('email')) . '" data-track="email_click">' . e(cfg('email')) . '</a>' : rich('[CONFIRM: email address]')],
    ['pin', is_ar() ? 'العنوان' : 'Address', e(t('footer.address'))],
    ['clock', is_ar() ? 'ساعات العمل' : 'Working hours', e(t('footer.hours')) . ' ' . rich('[CONFIRM: working days]')],
];
?>
<section class="section">
  <div class="container">
    <?= section_head($s) ?>
    <ul class="cards cols-3 contact-cards">
      <?php foreach ($cards as [$ic, $label, $val]): ?>
      <li class="card"><div class="card-inner"><span class="card-icon"><?= icon($ic) ?></span><h3><?= e($label) ?></h3><p><?= $val ?></p></div></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
