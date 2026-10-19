<?php
/**
 * SAP Business One Web Client home page drawn in HTML so it stays sharp at any size (shown inside the product_hero laptop).
 * $w: [label (accessible description)]. Layout follows the real Web Client: shell bar, module tabs, Fiori tiles grouped
 * under My Home and Sales, with counters, "20m ago / Open" footers and analytical micro charts. Sample company: OEC Computers.
 * Sizes use container units so the whole screen scales with the laptop.
 */
$plus = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h4M14 3l5 5M14 3v5h5M19 8v4M16 18h6M19 15v6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$list = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6h11M9 12h11M9 18h11M4 6h1M4 12h1M4 18h1" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
$refresh = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12a8 8 0 1 1-2.3-5.6M20 4v4h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
// [title, icon, count] ; count null = launch tile
$tile = function (string $t, string $ic, ?string $n) use ($refresh) {
    $icon = str_starts_with($ic, '<svg') ? $ic : icon($ic);
    return '<div class="wc-tile"><p class="wc-t">' . e($t) . '</p><div class="wc-kpi">' . $icon . ($n !== null ? '<b>' . e($n) . '</b>' : '') . '</div>'
        . ($n !== null ? '<p class="wc-foot">' . $refresh . '20m ago<span>Open</span></p>' : '') . '</div>';
};
$tabs = ['My Home', 'CRM', 'Sales', 'Purchasing', 'Business Partners', 'Payments', 'Item Management', 'Inventory Transactions', 'Production', 'Accounting', 'Service', 'Human Resources', 'Analytics', 'Administration'];
?>
<div class="wc" dir="ltr" lang="en" role="img" aria-label="<?= e($w['label'] ?? 'SAP Business One Web Client home page') ?>">
  <div class="wc-shell">
    <span class="wc-menu"><?= $list ?></span>
    <?= sap_b1_logo(20, 'wc-logo') ?>
    <span class="wc-home">Home</span><span class="wc-co">OEC Computers</span>
    <span class="wc-search"><span>All<i></i></span><em>Search</em><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="2"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span>
    <span class="wc-icons"><i>?</i><i class="wc-bell"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 16V11a6 6 0 0 1 12 0v5l1.5 2h-15zM10 20.5a2 2 0 0 0 4 0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></i><b>JB</b></span>
  </div>
  <div class="wc-tabs"><?php foreach ($tabs as $i => $t): ?><span<?= $i ? '' : ' class="is-on"' ?>><?= e($t) ?></span><?php endforeach; ?></div>
  <div class="wc-body">
    <div class="wc-grid">
      <div class="wc-tile wc-wide">
        <p class="wc-t">Sales Analysis<small>Net sales by month, SAR</small></p>
        <div class="wc-chart"><b>4.8M</b><span class="wc-cols"><?php foreach ([48, 56, 52, 64, 70, 66, 78, 84] as $h): ?><i style="--h:<?= $h ?>%"></i><?php endforeach; ?></span></div>
        <p class="wc-foot"><?= $refresh ?>20m ago</p>
      </div>
      <?= $tile('Sales Quotations', 'doc', '32') ?>
      <?= $tile('Sales Orders', 'cart', '288') ?>
      <?= $tile('Purchase Requests', 'doc', '1') ?>
      <?= $tile('Purchase Orders', 'box', '57') ?>
      <?= $tile('Journal Vouchers', $list, null) ?>
      <?= $tile('Create Sales Order', $plus, null) ?>
      <?= $tile('Business Partners', 'users', null) ?>
      <?= $tile('Deliveries', 'box', '142') ?>
      <?= $tile('A/R Invoices', 'receipt', '638') ?>
      <?= $tile('Approval Decisions', 'check', '4') ?>
      <div class="wc-tile wc-wide">
        <p class="wc-t">Top Customers<small>Sales YTD, SAR</small></p>
        <ul class="wc-bars"><?php foreach ([['Najd Trading', 92], ['Red Sea Holding', 74], ['Al Noor Contracting', 61], ['Eastern Foods', 47]] as [$c, $v]): ?><li><span><?= e($c) ?></span><i style="--v:<?= $v ?>%"></i></li><?php endforeach; ?></ul>
      </div>
      <?= $tile('Create A/R Credit Memo', $plus, null) ?>
      <?= $tile('Inventory Status', 'chart', null) ?>
    </div>
    <p class="wc-group">Sales</p>
    <div class="wc-grid">
      <?= $tile('Sales Quotations', 'doc', '32') ?>
      <?= $tile('Create Sales Quotation', $plus, null) ?>
      <?= $tile('Sales Orders', 'cart', '288') ?>
      <?= $tile('Create Sales Order', $plus, null) ?>
      <?= $tile('Deliveries', 'box', '142') ?>
      <?= $tile('Create Delivery', $plus, null) ?>
      <?= $tile('A/R Invoices', 'receipt', '638') ?>
      <?= $tile('Create A/R Invoice', $plus, null) ?>
    </div>
  </div>
</div>
