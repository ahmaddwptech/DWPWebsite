<?php /** film: h2, intro, video (path under assets), poster (path), title — click-to-play brand film (never autoplays) */ ?>
<section class="<?= section_class($s) ?>">
  <div class="container">
    <?= section_head($s) ?>
    <div class="framed">
      <button type="button" class="film" data-film="<?= e(asset($s['video'])) ?>" aria-label="<?= e(plain($s['title'] ?? $s['h2'] ?? 'Play video')) ?>">
        <img src="<?= e(asset($s['poster'])) ?>" alt="<?= e(plain($s['poster_alt'] ?? $s['title'] ?? '')) ?>" width="1280" height="720" loading="lazy">
        <span class="film-play"><span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 4v16l13-8z" fill="currentColor"/></svg></span></span>
      </button>
    </div>
  </div>
</section>
