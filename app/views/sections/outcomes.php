<?php
/**
 * outcomes: eyebrow, h2, intro, bg, note, items[{value, prefix, suffix, label, text, before[label, value], after[label, value]}]
 * Big result numbers that count up when the section scrolls into view (site.js), each with a short before and after
 * comparison drawn as two bars. Coloured with the page's product accent.
 */
$items = $s['items'] ?? [];
if (!$items) return;
?>
<section class="<?= section_class($s, 'oc-section') ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container">
    <?= section_head($s) ?>
    <ul class="oc" data-oc>
      <?php foreach ($items as $i => $it): ?>
      <li class="oc-item" style="--i:<?= $i ?>">
        <p class="oc-num"><?= rich($it['prefix'] ?? '') ?><span data-oc-n="<?= e((string) $it['value']) ?>"><?= e((string) $it['value']) ?></span><small><?= rich($it['suffix'] ?? '') ?></small></p>
        <h3><?= rich($it['label']) ?></h3>
        <?php if (!empty($it['before'])): $bv = (float) $it['before'][1]; $av = (float) $it['after'][1]; $mx = max($bv, $av) ?: 1; ?>
        <div class="oc-cmp">
          <div><span><?= rich($it['before'][0]) ?></span><i style="--w:<?= round($bv / $mx * 100) ?>%"></i></div>
          <div class="is-after"><span><?= rich($it['after'][0]) ?></span><i style="--w:<?= round($av / $mx * 100) ?>%"></i></div>
        </div>
        <?php endif; ?>
        <?php if (!empty($it['text'])): ?><p class="oc-text"><?= rich($it['text']) ?></p><?php endif; ?>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php if (!empty($s['note'])): ?><p class="oc-note"><?= rich($s['note']) ?></p><?php endif; ?>
  </div>
</section>
