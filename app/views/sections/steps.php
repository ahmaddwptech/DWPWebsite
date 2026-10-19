<?php /** steps: h2, intro, items[[title, text]], howto (bool → HowTo schema), closing, link */ ?>
<section class="<?= section_class($s) ?>">
  <div class="container">
    <?= section_head($s) ?>
    <ol class="steps steps-<?= count($s['items']) ?>">
      <?php foreach ($s['items'] as $n => $it): ?>
      <li class="step">
        <div><h3><?= rich($it[0]) ?></h3><?php if (!empty($it[1])): ?><p><?= rich($it[1]) ?></p><?php endif; ?></div>
      </li>
      <?php endforeach; ?>
    </ol>
    <?php if (!empty($s['closing'])): ?><p class="closing"><?= rich($s['closing']) ?></p><?php endif; ?>
    <?php if (!empty($s['link'])): ?><p class="section-link"><a href="<?= e(url($s['link'][1])) ?>"><?= rich($s['link'][0]) ?> <?= icon('arrow') ?></a></p><?php endif; ?>
  </div>
</section>
