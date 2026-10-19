<?php
/**
 * Home hero: full-bleed Riyadh skyline photo (video on desktop), left-aligned copy and a proof bar.
 * The photo is a real <img> so it can be the LCP element on mobile; the video fades in over it on desktop only (spec A4).
 */
$hh = t('home_hero');
$photo = 'img/hero/home-skyline.webp';
$photoSm = 'img/hero/home-skyline-sm.webp';
$photoHd = 'img/hero/home-skyline-2560.webp';
$hasVideo = asset_exists('video/hero-skyline.mp4');
?>
<section class="hero home-hero">
  <div class="home-hero-media">
    <img src="<?= e(asset($photoSm)) ?>" srcset="<?= e(asset($photoSm)) ?> 900w, <?= e(asset($photo)) ?> 1920w<?= asset_exists($photoHd) ? ', ' . e(asset($photoHd)) . ' 2560w' : '' ?>" sizes="100vw"
      alt="<?= e($hh['img_alt']) ?>" width="1920" height="1086" fetchpriority="high" decoding="async">
    <?php if ($hasVideo): ?>
    <video class="hero-video" aria-hidden="true" muted loop playsinline preload="none" data-hero-video
      <?php foreach (['webm' => 'hero-skyline.webm', 'mp4' => 'hero-skyline.mp4', 'webm-hd' => 'hero-skyline-hd.webm', 'mp4-hd' => 'hero-skyline-hd.mp4'] as $k => $f): if (asset_exists("video/$f")): ?> data-<?= $k ?>="<?= e(asset("video/$f")) ?>"<?php endif; endforeach; ?>></video>
    <?php endif; ?>
    <?php /* Live tower lights over the photo and video: the Kingdom Centre arch fades slowly between colours, office windows switch off and on and the aviation lights blink, as at night in Riyadh */ ?>
    <?php if (asset_exists('img/hero/skyline-arch-light.webp')): $arch = e(asset('img/hero/skyline-arch-light.webp')); ?>
    <span class="sky-layer sky-arch sky-arch-blue" aria-hidden="true" style="--sky:url('<?= $arch ?>')"></span>
    <span class="sky-layer sky-arch sky-arch-violet" aria-hidden="true" style="--sky:url('<?= $arch ?>')"></span>
    <span class="sky-layer sky-arch sky-arch-green" aria-hidden="true" style="--sky:url('<?= $arch ?>')"></span>
    <?php endif; ?>
    <?php for ($w = 0; $w < 6; $w++): if (asset_exists("img/hero/skyline-windows-$w.webp")): ?><span class="sky-layer sky-win sky-win-<?= $w ?>" aria-hidden="true" style="--sky:url('<?= e(asset("img/hero/skyline-windows-$w.webp")) ?>')"></span><?php endif; endfor; ?>
    <?php for ($w = 0; $w < 4; $w++): if (asset_exists("img/hero/skyline-windows-on-$w.webp")): ?><span class="sky-layer sky-lit sky-lit-<?= $w ?>" aria-hidden="true" style="--sky:url('<?= e(asset("img/hero/skyline-windows-on-$w.webp")) ?>')"></span><?php endif; endfor; ?>
    <?php if (asset_exists('img/hero/skyline-beacons.webp')): ?><span class="sky-layer sky-beacons" aria-hidden="true" style="--sky:url('<?= e(asset('img/hero/skyline-beacons.webp')) ?>')"></span><?php endif; ?>
  </div>
  <div class="container home-hero-inner">
    <div class="home-hero-copy">
      <h1><?= rich($page['h1']) ?></h1>
      <?php if (!empty($s['sub'])): ?><p class="hero-sub"><?= rich($s['sub']) ?></p><?php endif; ?>
      <?php if (!empty($s['buttons'])): ?>
      <div class="btn-row">
        <?php foreach ($s['buttons'] as $i => $b) echo button($b + [2 => $i === 0 ? 'primary' : 'ghost']); ?>
      </div>
      <?php endif; ?>
      <p class="home-hero-since"><span class="dot" aria-hidden="true"></span><?= e($hh['since']) ?></p>
    </div>
  </div>
</section>
