<?php /** text: h2, intro, body (string|array), image, id, variant ('muted'|'narrow'), buttons */ ?>
<section class="section<?= !empty($s['variant']) ? ' section-' . e($s['variant']) : '' ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container<?= empty($s['image']) ? '' : ' split' ?>">
    <div class="prose">
      <?php if (!empty($s['h2'])): ?><h2><?= rich($s['h2']) ?></h2><?php endif; ?>
      <?= paragraphs($s['body'] ?? []) ?>
      <?php if (!empty($s['buttons'])): ?><div class="btn-row"><?php foreach ($s['buttons'] as $i => $b) echo button($b + [2 => $i === 0 ? 'primary' : 'secondary']); ?></div><?php endif; ?>
    </div>
    <?php if (!empty($s['image'])): ?><div class="split-media framed"><?= img($s['image']) ?></div><?php endif; ?>
  </div>
</section>
