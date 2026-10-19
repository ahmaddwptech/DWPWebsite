<?php /** sap_modules: eyebrow, h2, intro, note, items[{icon, title, text}] — standard modules: intro column + module tiles */ ?>
<section class="<?= section_class($s, 'modules-section') ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container modules-layout">
    <div class="modules-intro">
      <?= section_head($s) ?>
      <?php if (!empty($s['note'])): ?><p class="modules-note"><?= icon('check') ?><?= rich($s['note']) ?></p><?php endif; ?>
    </div>
    <ul class="modules-grid">
      <?php foreach ($s['items'] as $it): ?>
      <li class="module-tile">
        <span class="module-icon"><?= icon($it['icon']) ?></span>
        <h3><?= rich($it['title']) ?></h3>
        <p><?= rich($it['text']) ?></p>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
