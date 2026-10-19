<?php
/**
 * Cortex Intercompany group screen drawn in HTML (crisp, translatable), shown in the sap_hero window.
 * $w: [title, period, hq, companies[3], balance[label, value, note], feed[[from, to, doc, amount]]]
 * Documents travel between the head office and the group companies along the links; the feed lists mirrored documents.
 */
$co = $w['companies'] ?? [];
?>
<div class="icg" aria-hidden="true">
  <div class="icg-head">
    <?php if (asset_exists('img/cortex/cortex-intercompany.webp')): ?><img src="<?= e(asset('img/cortex/cortex-intercompany.webp')) ?>" alt="Cortex Intercompany" width="1228" height="270"><?php endif; ?>
    <span class="icg-title"><?= rich($w['title'] ?? '') ?></span>
    <span class="icg-period"><?= icon('calendar') ?><?= rich($w['period'] ?? '') ?></span>
  </div>
  <div class="icg-body">
    <div class="icg-map">
      <svg class="icg-links" viewBox="0 0 400 240" preserveAspectRatio="none">
        <defs><linearGradient id="icg-g" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#1d5fd1"/><stop offset="1" stop-color="#14b8a6"/></linearGradient></defs>
        <path id="icg-p1" d="M200 52 C200 120 70 110 70 186"/>
        <path id="icg-p2" d="M200 52 L200 186"/>
        <path id="icg-p3" d="M200 52 C200 120 330 110 330 186"/>
        <path id="icg-p4" d="M84 200 C150 236 250 236 316 200"/>
        <?php foreach ([['p1', '3.2s', '0s'], ['p2', '2.6s', '.8s'], ['p3', '3.4s', '1.6s'], ['p4', '4s', '.4s']] as [$id, $dur, $begin]): ?>
        <circle r="5"><animateMotion dur="<?= $dur ?>" begin="<?= $begin ?>" repeatCount="indefinite" keyPoints="0;1" keyTimes="0;1" calcMode="linear"><mpath href="#icg-<?= $id ?>"/></animateMotion></circle>
        <?php endforeach; ?>
      </svg>
      <span class="icg-node icg-hq"><span class="icg-node-icon"><?= icon('briefcase') ?></span><?= rich($w['hq'] ?? '') ?></span>
      <?php foreach ($co as $i => $c): ?>
      <span class="icg-node icg-co icg-co-<?= $i + 1 ?>"><span class="icg-node-icon"><?= icon(['cart', 'box', 'factory'][$i] ?? 'box') ?></span><?= rich($c) ?></span>
      <?php endforeach; ?>
    </div>
    <div class="icg-side">
      <?php if (!empty($w['balance'])): ?>
      <div class="icg-balance">
        <span class="icg-label"><?= rich($w['balance'][0]) ?></span>
        <strong><?= rich($w['balance'][1]) ?></strong>
        <span class="icg-match"><?= icon('check') ?><?= rich($w['balance'][2]) ?></span>
        <span class="icg-meter"><i></i></span>
      </div>
      <?php endif; ?>
      <ul class="icg-feed">
        <?php foreach ($w['feed'] ?? [] as $i => [$from, $to, $doc, $amt]): ?>
        <li style="--i:<?= $i ?>"><span class="icg-feed-icon"><?= icon('doc') ?></span><span><strong><?= rich($doc) ?></strong><small><?= rich($from) ?> <?= icon('arrow') ?> <?= rich($to) ?></small></span><em><?= rich($amt) ?></em></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>
