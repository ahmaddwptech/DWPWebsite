<?php /** table: h2, intro, head[], rows[][], note, bg */ ?>
<section class="<?= section_class($s) ?>">
  <div class="container">
    <?= section_head($s) ?>
    <div class="table-wrap" tabindex="0" role="region" aria-label="<?= e(plain($s['h2'] ?? '')) ?>">
      <table class="data-table">
        <?php if (!empty($s['head'])): ?>
        <thead><tr><?php foreach ($s['head'] as $h): ?><th scope="col"><?= rich($h) ?></th><?php endforeach; ?></tr></thead>
        <?php endif; ?>
        <tbody>
          <?php foreach ($s['rows'] as $row): ?>
          <tr><?php foreach ($row as $i => $cell): ?><?= $i === 0 ? '<th scope="row">' . rich($cell) . '</th>' : '<td' . (isset($s['head'][$i]) ? ' data-label="' . e(plain($s['head'][$i])) . '"' : '') . '>' . rich($cell) . '</td>' ?><?php endforeach; ?></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php if (!empty($s['note'])): ?><p class="note"><?= rich($s['note']) ?></p><?php endif; ?>
  </div>
</section>
