<?php
/**
 * pm_control: eyebrow, h2, intro, bg, hint
 * Dark project control room that runs like a live system (site.js): costs post to projects, progress moves, the
 * schedule's today line advances and events appear in the log. The bottom card has tabs: schedule, resources (fix an
 * overbooked crew) and BOQ analysis (import a ready BOQ, watch it analysed, create the budget). Visitors can open a project (issue a progress invoice
 * with retention, record a cost, approve its variation) and act on the approvals inbox (timesheets, subcontractor
 * certificate, purchase request, variation order). Reuses the production control room styles (.pcr). Sample data only.
 */
$projects = [
    // id, name, contract, budget, cost, committed, progress, billed, variation (pending, SAR)
    ['tb', 'Tower B, Riyadh', 18600000, 14200000, 9080000, 2950000, 64, 10900000, 420000],
    ['lw', 'Logistics warehouse', 10900000, 8600000, 4470000, 1860000, 52, 5100000, 0],
    ['cf', 'Clinic fit out', 5200000, 4100000, 3730000, 310000, 82, 3900000, 260000],
    ['se', 'School extension', 7900000, 6300000, 2850000, 1240000, 40, 2700000, 0],
    ['mf', 'Mall fit out, Jeddah', 10400000, 8300000, 4820000, 1580000, 58, 5600000, 0],
];
$health = function ($p) { $used = $p[4] / $p[3] * 100; return $used > $p[6] + 6 ? ['stop', 'Over budget'] : ($used > $p[6] + 2 ? ['idle', 'At risk'] : ['run', 'On track']); };
$ar = is_ar();
$m = fn($v) => $ar ? number_format($v / 1e6, 2) . ' مليون ريال' : 'SAR ' . number_format($v / 1e6, 2) . 'M';
// Arabic pages: the room's text is swapped from this list on output, and the script reads the same list
$tr = $ar ? include ROOT_DIR . '/content/cortex-pm-room-ar.php' : [];
// Gantt per project: task, start %, length % (0 = milestone), progress %, over October to December
$gantt = [
    'tb' => ['Tower B', [['Structure levels 12 to 16', 0, 40, 70], ['MEP risers', 18, 50, 35], ['Facade cladding', 30, 50, 20], ['Topping out', 56, 0, 0]]],
    'lw' => ['Warehouse', [['Steel structure', 0, 26, 100], ['Roof sheeting', 20, 30, 60], ['Floor slab', 45, 28, 0], ['Handover', 86, 0, 0]]],
    'cf' => ['Clinic', [['MEP second fix', 0, 36, 55], ['Ceilings and finishes', 30, 40, 10], ['Medical equipment', 62, 26, 0]]],
    'se' => ['School', [['Foundations', 0, 22, 90], ['Ground floor slab', 16, 34, 30], ['Block work', 46, 40, 0]]],
    'mf' => ['Mall', [['Shop fronts', 4, 46, 50], ['Floor tiles', 30, 42, 25], ['Snagging', 76, 20, 0]]],
];
?>
<section class="<?= section_class($s, 'pcr-section pmc-section') ?>"<?= !empty($s['id']) ? ' id="' . e($s['id']) . '"' : '' ?>>
  <div class="container">
    <?= section_head($s) ?>
    <?php if (!empty($s['hint'])): ?><p class="pcr-hint"><?= icon('check') ?><?= rich($s['hint']) ?></p><?php endif; ?>
    <div class="pcr-wrap"><div class="pcr-frame">
      <?php ob_start(); ?>
      <div class="pcr pmc" data-pmc dir="<?= $ar ? 'rtl' : 'ltr' ?>" lang="<?= $ar ? 'ar' : 'en' ?>">
        <script type="application/json" data-pmc-i18n><?= json_encode($tr, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?></script>
        <div class="pcr-top">
          <div class="pcr-title"><strong>Project Control</strong><span class="pcr-live"><i></i>Live</span></div>
          <span class="pcr-clock" data-pmc-clock>10:42</span>
          <span class="pcr-date"><?= e('Mon 12 Oct 2026') ?><?php if ($hj = hijri('2026-10-12', $ar ? 'ar' : 'en')): ?><em><?= e($hj) ?></em><?php endif; ?></span>
          <span class="pcr-shift">OEC Contracting, all projects</span>
        </div>
        <div class="pcr-kpis">
          <div><span>Contract value</span><strong data-pmc-contract>SAR 53.00M</strong><em data-pmc-var>2 variations pending</em></div>
          <div><span>Cost to date</span><strong data-pmc-cost>SAR 25.00M</strong><em>Posted today <b data-pmc-today>SAR 0</b></em></div>
          <div><span>Billed</span><strong data-pmc-billed>SAR 28.20M</strong><em data-pmc-ret>Retention held SAR 2.82M</em></div>
          <div><span>Forecast margin</span><strong data-pmc-margin>20.1%</strong><em>Plan 21%</em></div>
          <div><span>Pending approvals</span><strong class="is-warn" data-pmc-pending>4</strong><em>In your inbox</em></div>
        </div>
        <div class="pcr-main">
          <div class="pcr-floor">
            <p class="pcr-h">Projects <small>Click a project</small></p>
            <div class="pmc-projects">
              <?php foreach ($projects as $p): [$hc, $hl] = $health($p); ?>
              <button type="button" class="pcr-m pmc-p is-<?= $hc ?>" data-pmc-p="<?= $p[0] ?>" data-name="<?= e($p[1]) ?>" data-contract="<?= $p[2] ?>" data-budget="<?= $p[3] ?>" data-cost="<?= $p[4] ?>" data-committed="<?= $p[5] ?>" data-progress="<?= $p[6] ?>" data-billed="<?= $p[7] ?>" data-variation="<?= $p[8] ?>">
                <span class="pcr-m-top"><b><?= e($p[1]) ?></b><span class="pcr-st"><i></i><span data-st><?= $hl ?></span></span></span>
                <span class="pmc-row"><span>Progress</span><strong data-prog><?= $p[6] ?>%</strong></span>
                <span class="pcr-bar"><i data-prog-bar style="--p:<?= $p[6] ?>%"></i></span>
                <span class="pmc-row"><span>Budget used</span><strong data-used><?= round($p[4] / $p[3] * 100) ?>%</strong></span>
                <span class="pcr-bar pmc-budget"><i data-used-bar style="--p:<?= round($p[4] / $p[3] * 100) ?>%"></i></span>
                <span class="pmc-row pmc-money"><span>Cost <b data-cost><?= $m($p[4]) ?></b></span><span>Billed <b data-billed><?= $m($p[7]) ?></b></span></span>
              </button>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="pcr-side">
            <div class="pcr-card">
              <p class="pcr-h">Approvals <span class="pcr-tag" data-pmc-pending-tag>4 pending</span></p>
              <ul class="pmc-inbox">
                <li data-pmc-ap="ts"><div><b>Timesheets, week 41</b><span>Tower B site team, 412 hours</span></div><div class="pmc-ap-btns"><button type="button" class="pcr-btn is-ok" data-ap="approve">Approve</button></div></li>
                <li data-pmc-ap="sc"><div><b>Subcontractor certificate 7</b><span>Gulf Steel Works, Tower B, SAR 640,000</span></div><div class="pmc-ap-btns"><button type="button" class="pcr-btn is-ok" data-ap="approve">Approve</button><button type="button" class="pcr-btn is-bad" data-ap="reject">Reject</button></div></li>
                <li data-pmc-ap="pr"><div><b>Purchase request PR 1180</b><span>Ceramic tiles, Mall fit out, SAR 96,000</span></div><div class="pmc-ap-btns"><button type="button" class="pcr-btn is-ok" data-ap="approve">Approve</button></div></li>
                <li data-pmc-ap="vo"><div><b>Variation order VO 12</b><span>Clinic fit out, extra X ray room, SAR 260,000</span></div><div class="pmc-ap-btns"><button type="button" class="pcr-btn is-ok" data-ap="approve">Approve</button><button type="button" class="pcr-btn is-bad" data-ap="reject">Reject</button></div></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="pcr-bottom">
          <div class="pcr-card pmc-gantt" data-pmc-tabs>
            <div class="pmc-tabs" role="tablist">
              <button type="button" class="is-on" role="tab" aria-selected="true" data-pmc-tab="0">Schedule</button>
              <button type="button" role="tab" aria-selected="false" data-pmc-tab="1">Resources</button>
              <button type="button" role="tab" aria-selected="false" data-pmc-tab="2">BOQ analysis</button>
            </div>
            <div class="pmc-pane is-on" data-pmc-pane="0">
              <div class="pmc-chips" data-pmc-chips><button type="button" class="is-on" data-chip="all">All projects</button><?php foreach ($gantt as $pid => [$pname]): ?><button type="button" data-chip="<?= $pid ?>"><?= e($pname) ?></button><?php endforeach; ?></div>
              <div class="pmc-g" style="--today:24" data-pmc-gantt>
                <div class="pmc-g-months"><span>Oct</span><span>Nov</span><span>Dec</span></div>
                <?php foreach ($gantt as $pid => [$pname, $rows]): ?>
                <div class="pmc-g-proj" data-proj="<?= $pid ?>"><?= e($pname) ?></div>
                <?php foreach ($rows as [$t, $st, $len, $pr]): ?>
                <div class="pmc-g-row" data-proj="<?= $pid ?>"><span><?= e($t) ?></span><span class="pmc-g-track"><?php if ($len === 0): ?><i class="pmc-ms" style="--s:<?= $st ?>%" title="Milestone"></i><?php else: ?><i style="--s:<?= $st ?>%;--l:<?= $len ?>%"><b style="--p:<?= $pr ?>%"></b></i><?php endif; ?></span><em><?= $len === 0 ? 'Milestone' : $pr . '%' ?></em></div>
                <?php endforeach; ?>
                <?php endforeach; ?>
                <span class="pmc-g-today" aria-hidden="true"><em>Today</em></span>
              </div>
            </div>
            <div class="pmc-pane" data-pmc-pane="1">
              <ul class="pmc-res" data-pmc-res>
                <?php foreach ([['Concrete crew 1', 'Crew, 14 people', 'Tower B', 92], ['Steel fixers', 'Crew, 10 people', 'Tower B and School', 118], ['MEP team A', 'Crew, 8 people', 'Clinic fit out', 86], ['Tower crane TC 2', 'Equipment', 'Tower B', 74], ['Excavator EX 05', 'Equipment', 'School extension', 41], ['Finishing crew', 'Crew, 12 people', 'Mall fit out', 79]] as [$r, $k, $on, $u]): ?>
                <li class="<?= $u > 100 ? 'is-over' : '' ?>" data-res="<?= e($r) ?>"><div><b><?= e($r) ?></b><span><?= e($k) ?>, <?= e($on) ?></span></div><span class="pcr-bar pmc-util"><i style="--p:<?= min(100, $u) ?>%"></i></span><em data-util><?= $u ?>%</em><?php if ($u > 100): ?><button type="button" class="pcr-btn" data-res-fix>Reassign</button><?php endif; ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
            <div class="pmc-pane" data-pmc-pane="2">
              <div class="pmc-boq" data-pmc-boq>
                <div class="pmc-boq-head"><span class="pmc-file"><?= icon('doc') ?>BOQ_Tower_C.xlsx</span><span class="pmc-boq-status" data-boq-status>Ready BOQ from the client, 148 items</span><button type="button" class="pcr-btn is-ok" data-boq-import>Import and analyse</button></div>
                <table class="pmc-boq-t">
                  <thead><tr><th>Item</th><th>Description</th><th>Qty</th><th>Rate</th><th>Amount</th><th>Analysis</th></tr></thead>
                  <tbody>
                    <?php foreach ([['2.1', 'Excavation in soil', '3,200 m3', '28', '89,600', [0, 30, 70, 0]], ['3.4', 'Reinforced concrete C40', '1,850 m3', '540', '999,000', [62, 18, 8, 12]], ['3.7', 'Steel reinforcement', '240 t', '3,900', '936,000', [78, 22, 0, 0]], ['5.2', 'Block work 200 mm', '4,600 m2', '72', '331,200', [55, 45, 0, 0]], ['8.1', 'Aluminium cladding', '2,900 m2', '610', '1,769,000', [0, 0, 0, 100]], ['9.3', 'Ceramic floor tiles', '6,100 m2', '95', '579,500', [58, 42, 0, 0]]] as $i => [$it, $d, $q, $r, $a, $mix]): ?>
                    <tr style="--i:<?= $i ?>"><td><?= $it ?></td><td><?= e($d) ?></td><td><?= $q ?></td><td><?= $r ?></td><td><?= $a ?></td><td><span class="pmc-mix"><?php foreach ($mix as $k => $v): if ($v): ?><i class="m<?= $k ?>" style="--w:<?= $v ?>%"></i><?php endif; endforeach; ?></span></td></tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
                <div class="pmc-boq-foot"><span class="pmc-legend"><i class="m0"></i>Material <i class="m1"></i>Labour <i class="m2"></i>Equipment <i class="m3"></i>Subcontract</span><button type="button" class="pcr-btn" data-boq-budget disabled>Create budget from BOQ</button></div>
              </div>
            </div>
          </div>
          <div class="pcr-card pcr-log">
            <p class="pcr-h">Events</p>
            <ul data-pmc-log>
              <li><time>10:40</time>GRN 3318: rebar 24 t received at Tower B</li>
              <li class="is-ok"><time>10:36</time>Invoice INV 2207 issued to Red Sea Holding</li>
              <li class="is-warn"><time>10:31</time>Clinic fit out passed 90% of budget</li>
              <li><time>10:25</time>PO 4512 issued: aluminium panels, Tower B</li>
            </ul>
          </div>
        </div>
        <aside class="pcr-drawer" data-pmc-drawer aria-hidden="true">
          <button type="button" class="pcr-close" data-pmc-close aria-label="Close">×</button>
          <p class="pcr-d-name" data-d-name></p>
          <p class="pcr-st pcr-d-st" data-d-st><i></i><span></span></p>
          <dl class="pcr-d-facts">
            <div><dt>Contract value</dt><dd data-d-contract></dd></div>
            <div><dt>Budget</dt><dd data-d-budget></dd></div>
            <div><dt>Cost to date</dt><dd data-d-cost></dd></div>
            <div><dt>Committed (open orders)</dt><dd data-d-committed></dd></div>
            <div><dt>Progress</dt><dd data-d-progress></dd></div>
            <div><dt>Billed</dt><dd data-d-billed></dd></div>
            <div><dt>Retention held</dt><dd data-d-ret></dd></div>
            <div><dt>Pending variation</dt><dd data-d-var></dd></div>
          </dl>
          <div class="pcr-d-actions">
            <button type="button" class="pcr-btn is-ok" data-d-action="invoice">Issue progress invoice</button>
            <button type="button" class="pcr-btn" data-d-action="cost">Record cost SAR 85,000</button>
            <button type="button" class="pcr-btn" data-d-action="variation">Approve variation</button>
          </div>
        </aside>
      </div>
<?php $room = ob_get_clean();
// swap the English text for Arabic, but not inside the script's own word list
if ($ar) { [$head, $rest] = explode('</script>', $room, 2); $room = $head . '</script>' . strtr($rest, $tr); }
echo $room; ?>
    </div></div>
  </div>
</section>
