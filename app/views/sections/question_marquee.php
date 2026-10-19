<?php
/** question_marquee: label, rows[[question | [team, question], ...], ...]: example questions drifting slowly across the page (two rows, opposite directions), each optionally tagged with the team that asks it */
$rows = $s['rows'] ?? [];
if (!$rows) return;
?>
<section class="qm-section" aria-label="<?= e(plain($s['label'] ?? '')) ?>">
  <?php if (!empty($s['label'])): ?><p class="qm-label"><?= rich($s['label']) ?></p><?php endif; ?>
  <?php foreach ($rows as $r => $qs): $set = implode('', array_map(fn($q) => is_array($q)
    ? '<li><span class="qm-team">' . rich($q[0]) . '</span>' . rich($q[1]) . '</li>'
    : '<li><span class="qm-icon">' . icon('chat') . '</span>' . rich($q) . '</li>', $qs)); ?>
  <div class="qm-row<?= $r % 2 ? ' is-reverse' : '' ?>">
    <ul class="qm-track"><?= $set ?></ul>
    <ul class="qm-track" aria-hidden="true"><?= $set ?></ul>
  </div>
  <?php endforeach; ?>
</section>
