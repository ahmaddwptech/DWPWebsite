<?php
/**
 * B2 + B5: footer — dark, clean and minimal.
 * 1) Slim call-to-action row carrying the page's CTA text (replaces the separate CTA band).
 * 2) Brand + social, then Solutions, Products, Company and Contact columns.
 * 3) Partnerships & certifications (official badges in their own colours), then the legal bar.
 */
$sol = t('nav.solutions_items');
$products = array_values(array_filter(t('nav.products_items'), fn($i) => $i[1] !== '/hrms/' || cfg('hrms_enabled')));
$wa = wa_link(sprintf(t('wa_message'), page_name($page['key'] ?? 'home', lang())));
$social = array_filter(['linkedin' => cfg('linkedin'), 'instagram' => cfg('instagram'), 'facebook' => cfg('facebook'), 'x' => cfg('x')]);
$socialNames = ['linkedin' => 'LinkedIn', 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'x' => 'X'];
$ft = fn(string $k) => t("footer.$k");
$cols = [
    [t('nav.solutions'), array_merge([[t('nav.ai'), '/ai/']], array_values(array_filter($sol, fn($i) => !in_array($i[1], ['/erp/odoo/'], true))))],
    [t('nav.products'), array_merge([['SAP Business One', '/sap-business-one/']], array_values(array_filter($sol, fn($i) => $i[1] === '/erp/odoo/')), $products)],
    [$ft('col_company'), t('nav.company_items')],
];
$ctaBy = t('footer.cta_by');
$area = $page['key'] ?? '';
$area = isset($ctaBy[$area]) ? $area : explode('.', $area)[0];
$cta = $GLOBALS['footer_cta'] ?? $ctaBy[$area] ?? t('cta_band.text');
$showCta = ($page['key'] ?? '') !== 'thank-you'; // the footer always carries the "Let's talk" contact row
$isoPdf = asset_exists('docs/iso-9001-2015-certificate.pdf') ? asset('docs/iso-9001-2015-certificate.pdf') : '';
?>
<footer class="site-footer ft">
  <div class="container">
    <?php if ($showCta): ?>
    <section class="ft-cta" aria-label="<?= e($ft('cta_eyebrow')) ?>">
      <div>
        <p class="ft-cta-eyebrow"><?= e($ft('cta_eyebrow')) ?></p>
        <p class="ft-cta-title"><?= rich($cta) ?></p>
      </div>
      <div class="ft-cta-actions">
        <?= button([t('cta_band.demo'), '/book-a-demo/', 'primary']) ?>
        <?= button([t('cta_band.whatsapp'), 'whatsapp', 'secondary']) ?>
      </div>
    </section>
    <?php endif; ?>

    <div class="ft-main">
      <div class="ft-brand">
        <a class="ft-logo" href="<?= e(url('/')) ?>" aria-label="DWP Arabia, <?= e(t('home')) ?>">
          <?php if (asset_exists('img/logo-light.png')): ?><img src="<?= e(asset('img/logo-light.png')) ?>" alt="DWP Arabia" width="116" height="44" loading="lazy"><?php else: ?><span class="logo-text">DWP <strong>Arabia</strong></span><?php endif; ?>
        </a>
        <p class="ft-tagline"><?= e($ft('tagline')) ?></p>
        <?php if ($social): ?><p class="ft-follow"><?= e($ft('follow')) ?></p><?php endif; ?>
        <ul class="ft-social">
          <?php foreach ($social as $net => $href): ?>
          <li><a href="<?= e($href) ?>" target="_blank" rel="noopener" aria-label="<?= e($socialNames[$net]) ?>"><?= icon($net) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php foreach ($cols as [$title, $links]): ?>
      <nav class="ft-col" aria-label="<?= e($title) ?>">
        <h2 class="ft-title"><?= e($title) ?></h2>
        <ul><?php foreach ($links as [$l, $h]): ?><li><a href="<?= e(url($h)) ?>"><?= e($l) ?></a></li><?php endforeach; ?></ul>
      </nav>
      <?php endforeach; ?>
      <div class="ft-col">
        <h2 class="ft-title"><?= e($ft('col_contact')) ?></h2>
        <ul class="ft-contact">
          <li><?= icon('phone') ?><a href="tel:<?= e(cfg('phone_e164')) ?>" dir="ltr" data-track="phone_click"><?= e(cfg('phone')) ?></a></li>
          <?php if (cfg('email')): ?><li><?= icon('mail') ?><a href="mailto:<?= e(cfg('email')) ?>" data-track="email_click"><?= e(cfg('email')) ?></a></li><?php elseif (show_markers()): ?><li><?= icon('mail') ?><span><?= rich('[CONFIRM: email address]') ?></span></li><?php endif; ?>
          <?php if ($wa): ?><li><?= icon('whatsapp') ?><a href="<?= e($wa) ?>" target="_blank" rel="noopener" dir="ltr" data-track="whatsapp_click">+<?= e(cfg('whatsapp')) ?></a></li><?php elseif (show_markers()): ?><li><?= icon('whatsapp') ?><span><?= rich('[CONFIRM: WhatsApp number]') ?></span></li><?php endif; ?>
          <li><?= icon('pin') ?><span><?= e($ft('address')) ?></span></li>
          <li><?= icon('clock') ?><span><?= e($ft('hours')) ?> <?= rich('[CONFIRM: working days]') ?></span></li>
        </ul>
      </div>
    </div>

    <div class="ft-certs">
      <p class="ft-certs-label"><?= e($ft('partnerships')) ?></p>
      <ul>
        <?php if ($l = sap_partner_logo(40)): ?><li><?= $l ?></li><?php endif; ?>
        <?php if (asset_exists('img/badges/iso-9001-2015.svg')): ?>
        <li class="ft-iso">
          <<?= $isoPdf ? 'a href="' . e($isoPdf) . '" target="_blank" rel="noopener"' : 'span' ?> class="ft-iso-inner">
            <img src="<?= e(asset('img/badges/iso-9001-2015.svg')) ?>" alt="ISO 9001:2015" width="48" height="48" loading="lazy">
            <span><strong>ISO 9001:2015</strong><small><?= e($ft('iso')) ?></small></span>
          </<?= $isoPdf ? 'a' : 'span' ?>>
        </li>
        <?php endif; ?>
        <?php if (asset_exists('img/badges/huawei-partner.webp')): ?><li><img src="<?= e(asset('img/badges/huawei-partner.webp')) ?>" alt="Huawei Enterprise Partner" width="62" height="60" loading="lazy"></li><?php endif; ?>
        <?php if (asset_exists('img/badges/odoo-silver-partner.webp')): ?><li><img src="<?= e(asset('img/badges/odoo-silver-partner.webp')) ?>" alt="<?= e(t('trust.odoo')) ?>" width="69" height="60" loading="lazy"></li><?php endif; ?>
      </ul>
    </div>

    <div class="ft-bottom">
      <p>© <?= date('Y') ?> <?= e(cfg('legal_name')) ?>. <?= e($ft('rights')) ?>
        <?php if (cfg('cr_number')): ?> · <?= e($ft('cr')) ?> <?= e(cfg('cr_number')) ?><?php else: ?> <?= rich('[CONFIRM: legal entity name + CR number]') ?><?php endif; ?>
      </p>
      <ul class="ft-legal">
        <li><a href="<?= e(url('/privacy-policy/')) ?>"><?= e($ft('privacy')) ?></a></li>
        <li><button type="button" class="link-button" data-cookie-settings><?= e($ft('cookie_settings')) ?></button></li>
        <li><a class="back-top" href="#top"><?= e($ft('back_top')) ?> <?= icon('arrow') ?></a></li>
      </ul>
    </div>
  </div>
</footer>
