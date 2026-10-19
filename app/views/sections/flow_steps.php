<?php
/**
 * flow_steps: eyebrow, h2, intro, bg, logo (Cortex logo path), no_sap, items[{visual ('connect'|'ask'|'act'|'group'|'mirror'), title, text, list[], ask, docs[from, to], chips[]}]
 * "How it works" as an illustrated flow: each step has its own animated visual; a solid progress line fills as the
 * section scrolls into view and lights the steps in turn (site.js sets --flow and .is-on).
 */
$logo = !empty($s['logo']) && asset_exists($s['logo']) ? $s['logo'] : null;
?>
<section class="<?= section_class($s, 'flow-section') ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container">
    <?= section_head($s) ?>
    <div class="flow" data-flow>
      <span class="flow-rail" aria-hidden="true"><i class="flow-fill"></i></span>
      <ol class="flow-steps">
        <?php foreach ($s['items'] as $n => $it): ?>
        <li class="flow-step" data-flow-step style="--i:<?= $n ?>">
          <span class="flow-dot" aria-hidden="true"><?= icon($it['icon'] ?? 'check') ?></span>
          <div class="flow-card">
            <div class="flow-visual flow-visual-<?= e($it['visual']) ?>" aria-hidden="true">
              <?php if ($it['visual'] === 'connect'): ?>
                <span class="fv-box"><?php if (empty($s['no_sap'])): ?><?= sap_b1_logo(18) ?><?php else: ?><strong class="connect-erp">ERP</strong><?php endif; ?></span>
                <span class="fv-link"><i></i><i></i></span>
                <span class="fv-box"><?php if ($logo): ?><img src="<?= e(asset($logo)) ?>" alt="Cortex AI" width="1228" height="270" loading="lazy"><?php endif; ?></span>
              <?php elseif ($it['visual'] === 'ask'): ?>
                <span class="fv-bubble fv-bubble-1"><?= rich($it['ask'][0] ?? '') ?></span>
                <span class="fv-bubble fv-bubble-2" dir="rtl" lang="ar"><?= rich($it['ask'][1] ?? '') ?></span>
                <span class="fv-typing"><i></i><i></i><i></i></span>
              <?php elseif ($it['visual'] === 'group'): ?>
                <span class="fv-group"><span class="fv-node fv-hq"><?= icon('briefcase') ?></span><span class="fv-branches"><i></i><i></i><i></i></span><span class="fv-kids"><span class="fv-node"><?= icon('cart') ?></span><span class="fv-node"><?= icon('box') ?></span><span class="fv-node"><?= icon('factory') ?></span></span></span>
              <?php elseif ($it['visual'] === 'mirror'): ?>
                <span class="fv-doc"><b><?= rich($it['docs'][0] ?? '') ?></b><i></i><i></i></span>
                <span class="fv-link"><i></i><i></i></span>
                <span class="fv-doc fv-doc-to"><b><?= rich($it['docs'][1] ?? '') ?></b><i></i><i></i></span>
              <?php else: ?>
                <span class="fv-report"><span class="fv-bars"><i style="--h:55%"></i><i style="--h:80%"></i><i style="--h:40%"></i><i style="--h:95%"></i><i style="--h:70%"></i></span><span class="fv-lines"><i></i><i></i></span></span>
                <span class="fv-chips"><?php foreach ($it['chips'] ?? [] as $c): ?><span><?= rich($c) ?></span><?php endforeach; ?></span>
              <?php endif; ?>
            </div>
            <h3><?= rich($it['title']) ?></h3>
            <p><?= rich($it['text']) ?></p>
            <?php if (!empty($it['list'])): ?><ul class="flow-list"><?php foreach ($it['list'] as $li): ?><li><?= icon('check') ?><span><?= rich($li) ?></span></li><?php endforeach; ?></ul><?php endif; ?>
          </div>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </div>
</section>
