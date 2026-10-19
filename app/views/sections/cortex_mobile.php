<?php
/** cortex_mobile: eyebrow, h2, intro, bg, note, phone{...see partials/cortex-phone}, items[{icon, title, text}] — the Cortex AI mobile app for quick questions */
?>
<section class="<?= section_class($s, 'mobile-section') ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container mobile-layout">
    <div class="mobile-visual"><span class="mobile-glow" aria-hidden="true"></span><?= view('partials/cortex-phone', ['p' => $s['phone']]) ?></div>
    <div class="mobile-copy">
      <?= section_head($s) ?>
      <ul class="mobile-features">
        <?php foreach ($s['items'] as $it): ?>
        <li><span class="mobile-feature-icon"><?= icon($it['icon']) ?></span><div><h3><?= rich($it['title']) ?></h3><p><?= rich($it['text']) ?></p></div></li>
        <?php endforeach; ?>
      </ul>
      <?php if (!empty($s['note'])): ?><p class="mobile-note"><?= icon('check') ?><?= rich($s['note']) ?></p><?php endif; ?>
    </div>
  </div>
</section>
