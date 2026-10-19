<?php
/**
 * ind_hero: photo, photo_alt, eyebrow, icon, sub, buttons[[label, href]], metrics[[label, value, suffix, step, decimals]], note
 * First section for the industry pages: a full-screen photo of the industry, the headline and actions, and a band of
 * live industry figures along the bottom that keep moving (site.js), each with a small trend line.
 * No laptop, no floating cards.
 */
$trail = $page['trail'] ?? [];
$photo = !empty($s['photo']) && asset_exists($s['photo']) ? $s['photo'] : 'img/gen/riyadh-skyline.webp';
[$pw, $ph] = getimagesize(ROOT_DIR . '/public/assets/' . $photo);
$sm = small_variant($photo);
$acc = $s['accent'] ?? null;
?>
<?php if ($acc): ?><style>body{--acc:<?= e($acc[0]) ?>;--acc2:<?= e($acc[1]) ?>;--acc-soft:color-mix(in srgb, <?= e($acc[0]) ?> 12%, transparent)}</style><?php endif; ?>
<section class="ind-hero">
  <div class="ind-media">
    <img src="<?= e(asset($photo)) ?>"<?php if ($sm): ?> srcset="<?= e(asset($sm)) ?> 900w, <?= e(asset($photo)) ?> <?= (int) $pw ?>w" sizes="100vw"<?php endif; ?> alt="<?= e($s['photo_alt'] ?? plain($page['h1'])) ?>" width="<?= (int) $pw ?>" height="<?= (int) $ph ?>" fetchpriority="high" decoding="async">
  </div>
  <?php if (count($trail) > 1): ?><?= view('partials/breadcrumbs', ['trail' => $trail]) ?><?php endif; ?>
  <div class="container ind-inner">
    <div class="ind-copy">
      <?php if (!empty($s['eyebrow'])): ?><p class="ind-eyebrow"><span class="ind-eyebrow-icon"><?= icon($s['icon'] ?? 'factory') ?></span><?= rich($s['eyebrow']) ?></p><?php endif; ?>
      <h1><?= rich($page['h1']) ?></h1>
      <?php if (!empty($s['sub'])): ?><p class="ind-sub"><?= rich($s['sub']) ?></p><?php endif; ?>
      <?php if (!empty($s['buttons'])): ?>
      <div class="ind-actions">
        <?php foreach ($s['buttons'] as $i => $b): ?>
          <?php if ($i === 0): ?><?= button($b + [2 => 'primary']) ?><?php else: ?><a class="ind-link" href="<?= e(url($b[1])) ?>"><?= rich($b[0]) ?> <?= icon('arrow') ?></a><?php endif; ?>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <?php if (!empty($s['metrics'])): ?>
  <div class="ind-band" data-ind>
    <div class="container ind-metrics">
      <?php foreach ($s['metrics'] as $i => $m): [$label, $value, $suffix, $step, $dec] = $m + [2 => '', 3 => 0, 4 => 0]; ?>
      <div class="ind-metric" style="--i:<?= $i ?>">
        <span class="ind-live"><i></i></span>
        <strong><span data-ind-n="<?= e((string) $value) ?>" data-step="<?= e((string) $step) ?>" data-dec="<?= (int) $dec ?>"><?= number_format((float) $value, (int) $dec) ?></span><small><?= rich($suffix) ?></small></strong>
        <span class="ind-label"><?= rich($label) ?></span>
        <svg class="ind-spark" viewBox="0 0 100 24" preserveAspectRatio="none" aria-hidden="true"><polyline points="<?= implode(' ', array_map(fn($x) => $x * 10 . ',' . (20 - (($x * 7 + $i * 5) % 11) - $x), range(0, 10))) ?>"/></svg>
      </div>
      <?php endforeach; ?>
    </div>
    <?php if (!empty($s['note'])): ?><p class="container ind-note"><?= rich($s['note']) ?></p><?php endif; ?>
  </div>
  <?php endif; ?>
</section>
