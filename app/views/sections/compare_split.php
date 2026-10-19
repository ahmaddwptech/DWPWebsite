<?php
/** compare_split: eyebrow, h2, intro, bg, before{title, items[]}, after{title, logo, items[]}: the work today next to the work with the product */
$logo = !empty($s['after']['logo']) && asset_exists($s['after']['logo']) ? $s['after']['logo'] : null;
?>
<section class="<?= section_class($s, 'cmp-section') ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container">
    <?= section_head($s) ?>
    <div class="cmp">
      <div class="cmp-col cmp-before reveal">
        <h3><?= rich($s['before']['title']) ?></h3>
        <ul><?php foreach ($s['before']['items'] as $it): ?><li><span class="cmp-icon"><?= icon('close') ?></span><?= rich($it) ?></li><?php endforeach; ?></ul>
      </div>
      <div class="cmp-col cmp-after reveal">
        <h3><?php if ($logo): [$lw, $lh] = getimagesize(ROOT_DIR . '/public/assets/' . $logo); ?><img src="<?= e(asset($logo)) ?>" alt="<?= e(plain($s['after']['title'])) ?>" width="<?= (int) $lw ?>" height="<?= (int) $lh ?>" loading="lazy"><?php else: ?><?= rich($s['after']['title']) ?><?php endif; ?></h3>
        <ul><?php foreach ($s['after']['items'] as $it): ?><li><span class="cmp-icon"><?= icon('check') ?></span><?= rich($it) ?></li><?php endforeach; ?></ul>
      </div>
    </div>
  </div>
</section>
