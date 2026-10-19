<?php
/**
 * hero: eyebrow, sub, buttons[[label,href]], photo (path under assets), photo_alt, photo_pos (CSS object-position), form ('short' shows a short lead form beside the copy)
 * Editorial first section on every page: a full-bleed photo of people at work, a plain headline at the bottom
 * and a facts bar underneath. No badges, floating cards, glows or gradient text.
 * The photo falls back to the page's old bg_image / image when it is a photo (img/gen); otherwise the map below.
 * Home renders sections/hero-home.php (skyline photo + video) with the same facts bar.
 */
$key = (string) ($page['key'] ?? '');
if ($key === 'home') { echo view('sections/hero-home', ['s' => $s, 'page' => $page]); return; }

// Photo per page when the content file does not name one (pages whose old visual was an illustration or none)
$photos = [
    'sap-business-one' => 'img/gen/sap-finance.webp', 'cortex.ai' => 'img/gen/ai-for-sap.webp', 'cortex.intercompany' => 'img/gen/sap-implementation.webp',
    'cras' => 'img/gen/sap-pricing.webp', 'erp' => 'img/gen/industry-distribution.webp', 'hrms' => 'img/gen/careers-team.webp', 'pms' => 'img/gen/data-bi.webp',
    'industrial-iot-platform' => 'img/gen/vision-ai.webp', 'products' => 'img/gen/about-team.webp', 'solutions' => 'img/gen/consulting-roadmap.webp',
    'industries' => 'img/gen/industry-contracting.webp', 'privacy-policy' => 'img/gen/riyadh-skyline.webp', 'book-a-demo' => 'img/gen/free-assessment.webp',
];
$pick = fn($p) => $p && str_starts_with($p, 'img/gen/') && asset_exists($p) ? $p : null;
$photo = $s['photo'] ?? $photos[$key] ?? $pick($s['bg_image'] ?? null) ?? $pick($s['image']['src'] ?? null) ?? 'img/gen/riyadh-skyline.webp';
$alt = $s['photo_alt'] ?? (!empty($s['image']['alt']) && ($s['image']['src'] ?? '') === $photo ? $s['image']['alt'] : plain($page['h1']));
[$pw, $ph] = asset_exists($photo) ? getimagesize(ROOT_DIR . '/public/assets/' . $photo) : [1600, 1063];
$sm = small_variant($photo);
$hasForm = !empty($s['form']);
$trail = $page['trail'] ?? [];
$eyebrow = $s['eyebrow'] ?? (str_starts_with($key, 'sap-business-one') && $key !== 'sap-business-one' ? 'SAP Business One' : null);
?>
<section class="ed-hero<?= $hasForm ? ' ed-has-form' : '' ?>">
  <div class="ed-media">
    <img src="<?= e(asset($photo)) ?>"<?php if ($sm): ?> srcset="<?= e(asset($sm)) ?> 900w, <?= e(asset($photo)) ?> <?= (int) $pw ?>w" sizes="100vw"<?php endif; ?>
      alt="<?= e($alt) ?>" width="<?= (int) $pw ?>" height="<?= (int) $ph ?>" fetchpriority="high" decoding="async"<?= !empty($s['photo_pos']) ? ' style="object-position:' . e($s['photo_pos']) . '"' : '' ?>>
  </div>
  <?php if (count($trail) > 1): ?><?= view('partials/breadcrumbs', ['trail' => $trail]) ?><?php endif; ?>
  <div class="container ed-inner">
    <div class="ed-copy">
      <?php if ($eyebrow): ?><p class="ed-eyebrow"><?= rich($eyebrow) ?></p><?php endif; ?>
      <h1><?= rich($page['h1']) ?></h1>
      <?php if (!empty($s['sub'])): ?><p class="ed-sub"><?= rich($s['sub']) ?></p><?php endif; ?>
      <?php if (!empty($s['buttons'])): ?>
      <div class="ed-actions">
        <?php foreach ($s['buttons'] as $i => $b): ?>
          <?php if ($i === 0): ?><?= button($b + [2 => 'primary']) ?><?php else: ?><a class="ed-link" href="<?= e(url($b[1])) ?>"><?= rich($b[0]) ?> <?= icon('arrow') ?></a><?php endif; ?>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php if ($hasForm): ?>
      <div class="ed-form hero-form card">
        <?= view('partials/lead-form', ['variant' => 'short', 'interest' => $s['interest'] ?? $page['interest'], 'heading' => $s['form_heading'] ?? t('form.heading'), 'id' => 'hero-form']) ?>
      </div>
    <?php endif; ?>
  </div>
  <?= view('partials/hero-facts') ?>
</section>
