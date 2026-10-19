<?php
/**
 * cortex_demo: eyebrow, h2, intro, bg, placeholder, examples[{tab, q, a, kind ('bars'|'list'|'table'), title, data[[label, value, note]]}]
 * Interactive chat demo: pick an example question; the answer is "typed" and its chart/list/table builds in (site.js).
 * Without JavaScript the first example is shown complete.
 */
$ex = $s['examples'] ?? [];
if (!$ex) return;
?>
<section class="<?= section_class($s, 'demo-section') ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container demo-layout">
    <div class="demo-intro">
      <?= section_head($s) ?>
      <div class="demo-tabs" role="tablist">
        <?php foreach ($ex as $n => $x): ?>
        <button type="button" class="demo-tab" role="tab" aria-selected="<?= $n ? 'false' : 'true' ?>" data-demo-tab="<?= $n ?>"><?= icon($x['icon'] ?? 'chat') ?><span><?= rich($x['tab']) ?></span></button>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="demo-app" data-demo>
      <div class="demo-bar"><span class="demo-dots" aria-hidden="true"><i></i><i></i><i></i></span><span class="demo-title">Cortex AI</span><span class="demo-status"><i></i><?= e($s['status'] ?? 'Connected to SAP Business One') ?></span></div>
      <div class="demo-chat" aria-live="polite">
        <?php foreach ($ex as $n => $x): ?>
        <div class="demo-thread<?= $n ? '' : ' is-active' ?>" data-demo-thread="<?= $n ?>">
          <p class="demo-msg demo-user"><?= rich($x['q']) ?></p>
          <div class="demo-msg demo-bot">
            <p class="demo-answer" data-text="<?= e(plain($x['a'])) ?>"><?= rich($x['a']) ?></p>
            <div class="demo-card demo-kind-<?= e($x['kind']) ?>">
              <?php if (!empty($x['title'])): ?><p class="demo-card-title"><?= rich($x['title']) ?></p><?php endif; ?>
              <?php $max = max(array_map(fn($d) => (float) $d[1], $x['data']) ?: [1]); ?>
              <?php if ($x['kind'] === 'bars'): ?>
                <div class="demo-bars"><?php foreach ($x['data'] as $i => $d): ?><div class="demo-bar-col" style="--h:<?= round(100 * (float) $d[1] / $max) ?>%;--i:<?= $i ?>"><span class="demo-bar-val"><?= e($d[2] ?? '') ?></span><span class="demo-bar-fill"></span><span class="demo-bar-label"><?= rich($d[0]) ?></span></div><?php endforeach; ?></div>
              <?php elseif ($x['kind'] === 'list'): ?>
                <ul class="demo-list"><?php foreach ($x['data'] as $i => $d): ?><li style="--i:<?= $i ?>"><span><?= rich($d[0]) ?></span><strong><?= rich($d[2] ?? $d[1]) ?></strong></li><?php endforeach; ?></ul>
              <?php else: ?>
                <table class="demo-table"><tbody><?php foreach ($x['data'] as $i => $d): ?><tr style="--i:<?= $i ?>"><td><?= rich($d[0]) ?></td><td><?= rich($d[2] ?? $d[1]) ?></td></tr><?php endforeach; ?></tbody></table>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="demo-input"><span><?= rich($s['placeholder'] ?? '') ?></span><i class="demo-send"><?= icon('arrow') ?></i></div>
    </div>
  </div>
</section>
