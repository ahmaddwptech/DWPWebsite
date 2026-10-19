<?php
$s['bg_image'] = $s['bg_image'] ?? 'img/gen/riyadh-skyline.webp'; // same background on every FAQ
/** faq: h2, intro, bg_image (photo fixed behind the section: revealed from under the section above, hidden under the one below), items[[q, a]], cta{h2, text, button, button2} (closing demo box shown inside the section, below the questions); the same strings feed FAQPage schema. Two columns: heading + contact link, then an accordion where one answer is open at a time. */
?>
<section class="<?= section_class($s, 'faq-section' . (!empty($s['bg_image']) ? ' faq-has-bg' : '')) ?>" id="faq">
  <?php if (!empty($s['bg_image']) && asset_exists($s['bg_image'])): ?><div class="faq-bg" aria-hidden="true" style="--faq-bg: url('<?= e(asset($s['bg_image'])) ?>')"></div><?php endif; ?>
  <div class="container faq-layout">
    <div class="faq-head">
      <h2><?= rich($s['h2'] ?? t('faq')) ?></h2>
      <p><?= rich($s['intro'] ?? t('faq_intro')) ?></p>
      <a class="faq-contact" href="<?= e(url('/contact/')) ?>"><?= e(t('faq_contact')) ?> <?= icon('arrow') ?></a>
    </div>
    <div class="faq" data-accordion>
      <?php foreach ($s['items'] as $n => [$q, $a]): ?>
      <details name="faq"<?= $n ? '' : ' open' ?>>
        <summary><h3><?= rich($q) ?></h3><?= icon('chevron') ?></summary>
        <div class="faq-answer"><p><?= rich($a) ?></p></div>
      </details>
      <?php endforeach; ?>
    </div>
  </div>
  <?php if (!empty($s['cta'])): $c = $s['cta']; ?>
  <div class="container faq-cta">
    <div class="cta-box">
      <div>
        <h2><?= rich($c['h2']) ?></h2>
        <?php if (!empty($c['text'])): ?><p><?= rich($c['text']) ?></p><?php endif; ?>
      </div>
      <div class="btn-row">
        <?php if (!empty($c['button'])) echo button($c['button'] + [2 => 'light']); ?>
        <?php if (!empty($c['button2'])) echo button($c['button2'] + [2 => 'outline-light']); ?>
      </div>
    </div>
  </div>
  <?php endif; ?>
</section>
