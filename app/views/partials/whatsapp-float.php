<?php
/** B3: floating WhatsApp button on every page (bottom-right EN, bottom-left AR). In staging without a number it shows as a [CONFIRM] placeholder linking to Contact. */
$wa = wa_link(sprintf(t('wa_message'), page_name($page['key'] ?? 'home', lang())));
if (!$wa && !show_markers()) return;
?>
<a class="wa-float<?= $wa ? '' : ' wa-float-placeholder' ?>" href="<?= e($wa ?? url('/contact/')) ?>"<?= $wa ? ' target="_blank" rel="noopener"' : '' ?> data-track="whatsapp_click" aria-label="<?= e(t('wa_button')) ?>"<?= $wa ? '' : ' title="[CONFIRM: WhatsApp number]"' ?>><?= icon('whatsapp') ?></a>
