<?php /** stats: items[[value, label]] */ ?>
<section class="section stats-section">
  <div class="container">
    <?= section_head($s) ?>
    <ul class="stats">
      <?php foreach ($s['items'] as [$v, $l]): ?><li><strong><?= rich($v) ?></strong><span><?= rich($l) ?></span></li><?php endforeach; ?>
    </ul>
  </div>
</section>
