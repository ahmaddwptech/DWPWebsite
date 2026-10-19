<?php
/** people: h2, intro, items[{name, title, photo{}, linkedin}] — leadership cards; placeholder cards in staging when empty. */
$items = $s['items'] ?? [];
if (!$items && !show_markers()) return;
?>
<section class="section">
  <div class="container">
    <?= section_head($s) ?>
    <?php if (!$items): ?>
      <p class="center"><?= rich('[CONFIRM: leadership names, titles, photos, LinkedIn links]') ?></p>
    <?php else: ?>
    <ul class="cards cols-<?= min(4, count($items)) ?>">
      <?php foreach ($items as $p): ?>
      <li class="card"><div class="card-inner person">
        <?php if (!empty($p['photo'])) echo img($p['photo']); ?>
        <h3><?= rich($p['name']) ?></h3>
        <p><?= rich($p['title']) ?></p>
        <?php if (!empty($p['linkedin'])): ?><a href="<?= e($p['linkedin']) ?>" target="_blank" rel="noopener" aria-label="LinkedIn: <?= e(plain($p['name'])) ?>"><?= icon('linkedin') ?></a><?php endif; ?>
      </div></li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </div>
</section>
