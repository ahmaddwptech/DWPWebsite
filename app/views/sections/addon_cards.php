<?php
/**
 * addon_cards: eyebrow, h2, intro, bg, reveal_bg (image fixed behind the section, revealed while scrolling like the home FAQ), items[{slug, name, text, href, cta}]
 * DWP's Cortex add-ons. Each logo is the Cortex logotype (circuit-symbol "C" + "ortex") followed by the application name:
 * img/cortex/cortex-{slug}.webp (generated full lockups, teal name, works on dark and light). One short line and a link to the add-on's own page (details live there).
 */
?>
<?php $rb = !empty($s['reveal_bg']) && asset_exists($s['reveal_bg']) ? $s['reveal_bg'] : null; ?>
<section class="<?= section_class($s, 'addons-section' . ($rb ? ' has-reveal-bg' : '')) ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <?php if ($rb): ?><span class="reveal-bg" aria-hidden="true" style="--reveal-bg:url('<?= e(asset($rb)) ?>');--reveal-bg-sm:url('<?= e(asset(small_variant($rb) ?? $rb)) ?>')"></span><?php endif; ?>
  <div class="container">
    <?= section_head($s) ?>
    <ul class="addons">
      <?php foreach ($s['items'] as $it): $logo = "img/cortex/cortex-{$it['slug']}.webp"; ?>
      <li>
        <a class="addon" href="<?= e(url($it['href'])) ?>">
          <?php if (asset_exists($logo)): [$lw, $lh] = getimagesize(ROOT_DIR . '/public/assets/' . $logo); ?><span class="addon-logo-wrap"><img class="addon-logo" src="<?= e(asset($logo)) ?>" alt="Cortex <?= e(plain($it['name'])) ?>" width="<?= (int) $lw ?>" height="<?= (int) $lh ?>" loading="lazy"></span><?php endif; ?>
          <h3 class="<?= asset_exists($logo) ? 'sr-only' : '' ?>">Cortex <?= rich($it['name']) ?></h3>
          <p class="addon-text"><?= rich($it['text']) ?></p>
          <span class="addon-link"><?= e($it['cta'] ?? t('learn_more')) ?> <?= icon('arrow') ?></span>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
