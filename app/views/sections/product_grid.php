<?php
/**
 * product_grid: groups[{title, items[{title, logo ('sap'), text, href, cta, image{src,alt,w,h}, featured}]}]
 * High-level product overview: one short card per product with a button to its detail page.
 */
?>
<section class="<?= section_class($s, 'pgrid-section') ?>">
  <div class="container">
    <?php foreach ($s['groups'] as $g): ?>
    <div class="pgrid-group">
      <?php if (!empty($g['title'])): ?><h2 class="pgrid-title"><?= rich($g['title']) ?></h2><?php endif; ?>
      <ul class="pgrid pgrid-<?= count($g['items']) ?>">
        <?php foreach ($g['items'] as $it): $f = !empty($it['featured']); ?>
        <li class="pcard<?= $f ? ' is-featured' : '' ?>">
          <a class="pcard-media<?= !empty($it['fit']) ? ' is-contain' : '' ?>" href="<?= e(url($it['href'])) ?>" tabindex="-1" aria-hidden="true"<?= !empty($it['bg']) ? ' style="background:' . e($it['bg']) . '"' : '' ?>><?= img($it['image'] + ['sizes' => '(min-width: 1000px) 40vw, 100vw']) ?></a>
          <div class="pcard-body">
            <?php if (($it['logo'] ?? '') === 'sap' && ($logo = sap_b1_logo(32, '', $f ? 'dark' : 'light'))): ?>
              <h3 class="pcard-logo"><?= $logo ?></h3>
            <?php else: ?>
              <h3><?= rich($it['title']) ?></h3>
            <?php endif; ?>
            <p><?= rich($it['text']) ?></p>
            <a class="btn <?= $f ? 'btn-primary' : 'btn-secondary' ?>" href="<?= e(url($it['href'])) ?>"><?= e($it['cta']) ?> <?= icon('arrow') ?></a>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endforeach; ?>
  </div>
</section>
