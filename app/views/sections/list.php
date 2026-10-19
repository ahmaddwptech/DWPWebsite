<?php /** list: h2, intro, items[string], cols (1|2|3), link[label,href], bg, closing */ ?>
<section class="<?= section_class($s) ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container">
    <?= section_head($s) ?>
    <ul class="checklist cols-<?= (int) ($s['cols'] ?? 1) ?>">
      <?php foreach ($s['items'] as $it): ?><li><?= icon('check') ?><span><?= rich($it) ?></span></li><?php endforeach; ?>
    </ul>
    <?php if (!empty($s['closing'])): ?><p class="closing"><?= rich($s['closing']) ?></p><?php endif; ?>
    <?php if (!empty($s['link'])): ?><p class="section-link"><a href="<?= e(url($s['link'][1])) ?>"><?= rich($s['link'][0]) ?> <?= icon('arrow') ?></a></p><?php endif; ?>
  </div>
</section>
