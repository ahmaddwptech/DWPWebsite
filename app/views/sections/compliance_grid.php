<?php /** compliance_grid: eyebrow, h2, intro, link[label, href], items[{icon, title, text, tag}] — Saudi compliance coverage */ ?>
<section class="<?= section_class($s, 'compliance-section') ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container">
    <div class="compliance-top">
      <?= section_head($s) ?>
      <?php if (!empty($s['link'])): ?><a class="compliance-link" href="<?= e(url($s['link'][1])) ?>"><?= e($s['link'][0]) ?> <?= icon('arrow') ?></a><?php endif; ?>
    </div>
    <ul class="compliance-grid">
      <?php foreach ($s['items'] as $it): ?>
      <li class="compliance-item">
        <span class="compliance-icon"><?= icon($it['icon']) ?></span>
        <div>
          <h3><?= rich($it['title']) ?><?php if (!empty($it['tag'])): ?> <span class="compliance-tag"><?= rich($it['tag']) ?></span><?php endif; ?></h3>
          <p><?= rich($it['text']) ?></p>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
