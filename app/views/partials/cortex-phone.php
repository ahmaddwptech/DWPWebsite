<?php
/**
 * Cortex AI mobile app screen drawn in HTML (crisp, translatable). $p: [chips[], q, a, value, delta, bars[], alert[title, text], placeholder]
 * Optional $size ('sm' for the hero overlay).
 */
$size = $size ?? '';
?>
<div class="phone<?= $size ? ' phone-' . e($size) : '' ?>" aria-hidden="true">
  <div class="phone-screen">
    <div class="phone-status"><span>9:41</span><i class="phone-notch"></i><span class="phone-sig"><i></i><i></i><i></i></span></div>
    <?php if (!empty($p['alert'])): ?>
    <div class="phone-push"><span class="phone-push-icon"><?= icon('shield') ?></span><span><strong><?= rich($p['alert'][0]) ?></strong><small><?= rich($p['alert'][1]) ?></small></span></div>
    <?php endif; ?>
    <div class="phone-head"><?php if (asset_exists('img/cortex/cortex-ai.webp')): ?><img src="<?= e(asset('img/cortex/cortex-ai.webp')) ?>" alt="Cortex AI" width="1228" height="270"><?php endif; ?></div>
    <div class="phone-chips"><?php foreach ($p['chips'] ?? [] as $i => $c): ?><span style="--i:<?= $i ?>"><?= rich($c) ?></span><?php endforeach; ?></div>
    <p class="phone-q"><?= rich($p['q']) ?></p>
    <div class="phone-a">
      <p><?= rich($p['a']) ?></p>
      <div class="phone-kpi"><strong><?= rich($p['value']) ?></strong><em><?= rich($p['delta']) ?></em></div>
      <?php $mx = max($p['bars'] ?? [1]); ?>
      <div class="phone-bars"><?php foreach ($p['bars'] ?? [] as $i => $b): ?><i style="--h:<?= round(100 * $b / $mx) ?>%;--i:<?= $i ?>"></i><?php endforeach; ?></div>
    </div>
    <div class="phone-input"><span><?= rich($p['placeholder'] ?? '') ?></span><i><?= icon('arrow') ?></i></div>
  </div>
</div>
