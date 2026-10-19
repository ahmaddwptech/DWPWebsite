<?php
/**
 * product_hero: logo (product logo path; default SAP Business One), logo_alt, sub, buttons[[label, href]], points[],
 * brand (product name as text when there is no logo), accent [colour, colour], image{src, alt, w, h} or window{partial, data} (HTML product screen), screen_zoom (show only the left part of a very wide
 * screenshot, e.g. 1.4), phone{...see partials/cortex-phone}
 * First section for SAP Business One and the Cortex add-ons: mid-tone background (the home skyline photo blurred under a slate-blue tint), the real screen on top inside a laptop
 * shown whole (the phone beside it when the product has a mobile app), and the wording below it in two columns
 * (logo + headline | text + actions). Sized so everything fits one screen. No badges, floating cards or glows.
 */
$trail = $page['trail'] ?? [];
$logo = !empty($s['logo']) && asset_exists($s['logo']) ? $s['logo'] : null;
$brand = $s['brand'] ?? null;                 // product name as text when there is no logo image
$acc = $s['accent'] ?? null;                  // product colours for the page (results, process, works with)
?>
<?php if ($acc): ?><style>body{--acc:<?= e($acc[0]) ?>;--acc2:<?= e($acc[1]) ?>;--acc-soft:color-mix(in srgb, <?= e($acc[0]) ?> 12%, transparent)}</style><?php endif; ?>
<section class="pd-hero<?= !empty($s['phone']) ? ' pd-has-phone' : '' ?>" style="--pd-bg:url('<?= e(asset('img/hero/home-skyline.webp')) ?>')">
  <span class="pd-bg" aria-hidden="true"></span>
  <?php if (count($trail) > 1): ?><?= view('partials/breadcrumbs', ['trail' => $trail]) ?><?php endif; ?>
  <div class="pd-devices">
    <div class="pd-laptop">
      <div class="pd-lid">
        <span class="pd-cam" aria-hidden="true"></span>
        <div class="pd-display"<?= !empty($s['screen_zoom']) ? ' style="--zoom:' . (float) $s['screen_zoom'] . '"' : '' ?>>
          <?= !empty($s['window']) ? view('partials/' . $s['window']['partial'], ['w' => $s['window']['data']]) : img($s['image'] + ['sizes' => '(min-width: 1200px) 1000px, 92vw'], false) ?>
        </div>
      </div>
      <div class="pd-base" aria-hidden="true"><i></i></div>
    </div>
    <?php if (!empty($s['phone'])): ?><div class="pd-phone"><?= view('partials/cortex-phone', ['p' => $s['phone'], 'size' => 'sm']) ?></div><?php endif; ?>
  </div>
  <div class="container pd-words">
    <div class="pd-title">
      <p class="pd-logo"><?php if ($logo): [$lw, $lh] = getimagesize(ROOT_DIR . '/public/assets/' . $logo); ?><img src="<?= e(asset($logo)) ?>" alt="<?= e($s['logo_alt'] ?? '') ?>" width="<?= (int) $lw ?>" height="<?= (int) $lh ?>"><?php elseif ($brand): ?><span class="pd-brand"><?= rich($brand) ?></span><?php else: ?><?= sap_b1_logo(34, '', 'dark') ?><?php endif; ?></p>
      <h1><?= rich($page['h1']) ?></h1>
    </div>
    <div class="pd-intro">
      <?php if (!empty($s['sub'])): ?><p class="pd-sub"><?= rich($s['sub']) ?></p><?php endif; ?>
      <?php if (!empty($s['buttons'])): ?>
      <div class="pd-actions">
        <?php foreach ($s['buttons'] as $i => $b): ?>
          <?php if ($i === 0): ?><?= button($b + [2 => 'primary']) ?><?php else: ?><a class="pd-link" href="<?= e(url($b[1])) ?>"><?= rich($b[0]) ?> <?= icon('arrow') ?></a><?php endif; ?>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
