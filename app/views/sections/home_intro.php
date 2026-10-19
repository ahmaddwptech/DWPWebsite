<?php /** home_intro: h2, body, pillars[[icon, title, text, href]], link[label, href], image — "From strategy to systems" block under the home hero */ ?>
<section class="<?= section_class($s, 'home-intro') ?>">
  <div class="container sap-partner-grid">
    <div class="sap-partner-copy">
      <?php if (!empty($s['eyebrow'])): ?><p class="eyebrow"><?= e($s['eyebrow']) ?></p><?php endif; ?>
      <h2><?= rich($s['h2']) ?></h2>
      <p class="sap-partner-lead"><?= rich($s['body']) ?></p>
      <ul class="sap-pillars home-intro-pillars">
        <?php foreach ($s['pillars'] as [$ic, $title, $text, $href]): ?>
        <li><a href="<?= e(url($href)) ?>"><span class="sap-pillar-icon"><?= icon($ic) ?></span><div><h3><?= rich($title) ?></h3><p><?= rich($text) ?></p></div></a></li>
        <?php endforeach; ?>
      </ul>
      <?php if (!empty($s['link'])): ?><div class="btn-row"><?= button([$s['link'][0], $s['link'][1], 'secondary']) ?></div><?php endif; ?>
    </div>
    <div class="sap-partner-media">
      <div class="framed"><?= img($s['image'] + ['sizes' => '(min-width: 1000px) 45vw, 100vw']) ?></div>
    </div>
  </div>
</section>
<?php if (!empty($s['partners']) && ($logos = array_merge(official_partner_logos(), technology_logos()))): $set = implode('', array_map(fn($l) => '<li>' . $l . '</li>', $logos)); ?>
<div class="partner-roller">
  <div class="container"><p class="partner-roller-label"><?= e(t('partner_roller')) ?></p></div>
  <div class="partner-roller-viewport">
    <ul class="partner-roller-track"><?= str_repeat($set, 2) ?></ul>
    <ul class="partner-roller-track" aria-hidden="true"><?= str_repeat($set, 2) ?></ul>
  </div>
</div>
<?php endif; ?>
