<?php
/**
 * sap_hero: sub, buttons[[label, href]], image{src,alt,w,h} (product screenshot), points[] (short facts under the buttons),
 * chips[[icon, label]] (glass cards floating around the screenshot), badge, phone{...} (Cortex mobile screen overlapping the window), logo (product logo path; default SAP Business One), logo_alt, bg_image (light background image, slowly animated), window{partial, data} (HTML product screen drawn in place of the image): light product hero for the SAP Business One page.
 * Background: optional image (slow drift + shine) under a soft animated colour mesh; content enters with a staggered fade/rise.
 */
$trail = $page['trail'] ?? [];
?>
<?php $bg = !empty($s['bg_image']) && asset_exists($s['bg_image']) ? $s['bg_image'] : null; ?>
<section class="b1-hero<?= $bg ? ' has-photo' : '' ?>">
  <?php if ($bg): ?><span class="b1-photo" aria-hidden="true" style="--b1-bg:url('<?= e(asset($bg)) ?>');--b1-bg-sm:url('<?= e(asset(small_variant($bg) ?? $bg)) ?>')"><i class="b1-shine"></i></span><?php endif; ?>
  <span class="b1-mesh" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
  <?php if (count($trail) > 1): ?><?= view('partials/breadcrumbs', ['trail' => $trail]) ?><?php endif; ?>
  <div class="container b1-hero-grid">
    <div class="b1-hero-copy">
      <div class="b1-hero-brand b1-in" style="--i:0">
        <?php if (!empty($s['logo']) && asset_exists($s['logo'])): [$lw, $lh] = getimagesize(ROOT_DIR . '/public/assets/' . $s['logo']); ?><img class="b1-hero-logo" src="<?= e(asset($s['logo'])) ?>" alt="<?= e($s['logo_alt'] ?? '') ?>" width="<?= (int) $lw ?>" height="<?= (int) $lh ?>"><?php else: ?><?= sap_b1_logo(38) ?><?php endif; ?>
        <?php if (!empty($s['badge'])): ?><span class="b1-hero-badge"><?= icon('shield') ?><?= rich($s['badge']) ?></span><?php endif; ?>
      </div>
      <h1 class="b1-in" style="--i:1"><?= rich($page['h1']) ?></h1>
      <?php if (!empty($s['sub'])): ?><p class="b1-hero-sub b1-in" style="--i:2"><?= rich($s['sub']) ?></p><?php endif; ?>
      <?php if (!empty($s['buttons'])): ?><div class="btn-row b1-in" style="--i:3"><?php foreach ($s['buttons'] as $i => $b) echo button($b + [2 => $i === 0 ? 'primary' : 'secondary']); ?></div><?php endif; ?>
      <?php if (!empty($s['points'])): ?>
      <ul class="b1-hero-points b1-in" style="--i:4"><?php foreach ($s['points'] as $p): ?><li><?= icon('check') ?><?= rich($p) ?></li><?php endforeach; ?></ul>
      <?php endif; ?>
    </div>
    <?php if (!empty($s['image']) || !empty($s['window'])): ?>
    <div class="b1-hero-visual">
      <div class="b1-window">
        <span class="b1-window-bar" aria-hidden="true"><i></i><i></i><i></i></span>
        <?= !empty($s['window']) ? view('partials/' . $s['window']['partial'], ['w' => $s['window']['data']]) : img($s['image'] + ['sizes' => '(min-width: 1000px) 55vw, 100vw'], false) ?>
      </div>
      <?php if (!empty($s['phone'])): ?><div class="b1-phone"><?= view('partials/cortex-phone', ['p' => $s['phone'], 'size' => 'sm']) ?></div><?php endif; ?>
      <?php foreach ($s['chips'] ?? [] as $n => [$ic, $label]): ?>
      <span class="b1-chip b1-chip-<?= $n + 1 ?>" aria-hidden="true"><span class="b1-chip-icon"><?= icon($ic) ?></span><?= rich($label) ?></span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
