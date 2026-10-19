<?php /** cta_box: h2, text, button[label, href, style], button2 */ ?>
<section class="section">
  <div class="container">
    <div class="cta-box">
      <div>
        <h2><?= rich($s['h2']) ?></h2>
        <?php if (!empty($s['text'])): ?><p><?= rich($s['text']) ?></p><?php endif; ?>
      </div>
      <div class="btn-row">
        <?php if (!empty($s['button'])) echo button($s['button'] + [2 => 'light']); ?>
        <?php if (!empty($s['button2'])) echo button($s['button2'] + [2 => 'outline-light']); ?>
      </div>
    </div>
  </div>
</section>
