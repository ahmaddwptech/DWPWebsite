<?php
/**
 * prod_process: eyebrow, h2, intro, bg, steps[{icon, title, text, functions[]}]
 * The production process as a clickable flow: a pulse travels along the steps, a step lights up in turn and its
 * functions show in the panel below. Clicking a step stops the auto play and shows that step (site.js).
 */
$steps = $s['steps'] ?? [];
if (!$steps) return;
?>
<section class="<?= section_class($s, 'pp-section') ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container">
    <?= section_head($s) ?>
    <div class="pp" data-pp style="--n:<?= count($steps) ?>">
      <div class="pp-track">
        <span class="pp-line" aria-hidden="true"><i class="pp-pulse"></i></span>
        <ol class="pp-steps" role="tablist">
          <?php foreach ($steps as $n => $st): ?>
          <li><button type="button" class="pp-step<?= $n ? '' : ' is-on' ?>" role="tab" aria-selected="<?= $n ? 'false' : 'true' ?>" data-pp-step="<?= $n ?>"><span class="pp-node"><?= icon($st['icon']) ?></span><span class="pp-label"><?= rich($st['title']) ?></span></button></li>
          <?php endforeach; ?>
        </ol>
      </div>
      <div class="pp-panels">
        <?php foreach ($steps as $n => $st): ?>
        <div class="pp-panel<?= $n ? '' : ' is-on' ?>" data-pp-panel="<?= $n ?>" role="tabpanel">
          <div class="pp-panel-head"><span class="pp-panel-icon"><?= icon($st['icon']) ?></span><div><h3><?= rich($st['title']) ?></h3><p><?= rich($st['text']) ?></p></div></div>
          <ul class="pp-funcs"><?php foreach ($st['functions'] as $i => $f): ?><li style="--i:<?= $i ?>"><?= icon('check') ?><span><?= rich($f) ?></span></li><?php endforeach; ?></ul>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
