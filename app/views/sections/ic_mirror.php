<?php
/**
 * ic_mirror: eyebrow, h2, intro, bg, logo, posted (status label), examples[{tab, icon, from{company, doc, no, partner, lines[[label, amount]], total}, to{...same}}]
 * Intercompany demo: pick a document; it is posted in one company, travels through Cortex Intercompany and the matching
 * document builds in the other company (site.js). Plays through the examples until a visitor picks one.
 * Without JavaScript the first example is shown complete.
 */
$ex = $s['examples'] ?? [];
if (!$ex) return;
$logo = !empty($s['logo']) && asset_exists($s['logo']) ? $s['logo'] : null;
$doc = function (array $d, string $side) {
    ob_start(); ?>
    <div class="icm-doc icm-<?= $side ?>">
      <div class="icm-doc-head"><span class="icm-co"><?= rich($d['company']) ?></span><span class="icm-no"><?= rich($d['no'] ?? '') ?></span></div>
      <p class="icm-type"><?= rich($d['doc']) ?></p>
      <?php if (!empty($d['partner'])): ?><p class="icm-partner"><?= rich($d['partner']) ?></p><?php endif; ?>
      <ul class="icm-lines"><?php foreach ($d['lines'] as $i => [$l, $a]): ?><li style="--i:<?= $i ?>"><span><?= rich($l) ?></span><strong><?= rich($a) ?></strong></li><?php endforeach; ?></ul>
      <?php if (!empty($d['total'])): ?><p class="icm-total"><span><?= rich($d['total'][0]) ?></span><strong><?= rich($d['total'][1]) ?></strong></p><?php endif; ?>
    </div>
    <?php return ob_get_clean();
};
?>
<section class="<?= section_class($s, 'icm-section') ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container">
    <?= section_head($s) ?>
    <div class="icm" data-icm>
      <div class="demo-tabs icm-tabs" role="tablist">
        <?php foreach ($ex as $n => $x): ?>
        <button type="button" class="demo-tab" role="tab" aria-selected="<?= $n ? 'false' : 'true' ?>" data-icm-tab="<?= $n ?>"><?= icon($x['icon'] ?? 'doc') ?><span><?= rich($x['tab']) ?></span></button>
        <?php endforeach; ?>
      </div>
      <div class="icm-stage">
        <?php foreach ($ex as $n => $x): ?>
        <div class="icm-pair<?= $n ? '' : ' is-active is-done' ?>" data-icm-pair="<?= $n ?>">
          <?= $doc($x['from'], 'from') ?>
          <div class="icm-bridge" aria-hidden="true">
            <span class="icm-track"><i></i></span>
            <span class="icm-hub"><?php if ($logo): ?><img src="<?= e(asset($logo)) ?>" alt="Cortex Intercompany" width="1228" height="270" loading="lazy"><?php endif; ?></span>
            <span class="icm-track"><i></i></span>
          </div>
          <?= $doc($x['to'], 'to') ?>
          <p class="icm-status"><?= icon('check') ?><span><?= rich($x['status'] ?? $s['posted'] ?? '') ?></span></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
