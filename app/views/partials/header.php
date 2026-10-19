<?php
/**
 * B1: header with a full-width mega menu on desktop and a slide-in drawer on mobile.
 * Four menus: Products (ERP: SAP Business One as the flagship, then Odoo; then DWP platforms),
 * Solutions (AI, data / IoT / consulting), Industries, Company. Links come from nav.*_items (shared with the footer).
 */
$other = other_lang();
$switchPath = $page['switch_path'] ?? $page['path'];
$hasOther = !empty($page['alternates'][$other]);
$current = url($page['path']);
$mega = t('mega');
$icons = [
    '/sap-business-one/' => 'box', '/sap-business-one/implementation/' => 'gear', '/sap-business-one/zatca-e-invoicing/' => 'receipt',
    '/sap-business-one/support/' => 'shield', '/sap-business-one/switch/' => 'link', '/sap-business-one/pricing/' => 'coins',
    '/sap-business-one/vs-odoo/' => 'chart', '/sap-business-one/free-assessment/' => 'check',
    '/ai/' => 'spark', '/cortex/ai/' => 'chat', '/ai/ai-for-sap-business-one/' => 'link', '/ai/predictive-analytics/' => 'chart',
    '/ai/arabic-ai-assistants/' => 'chat', '/ai/document-automation/' => 'doc', '/ai/vision-ai/' => 'eye',
    '/erp/' => 'box', '/erp/odoo/' => 'gear', '/data-and-bi/' => 'chart', '/consulting/' => 'briefcase', '/iot-industrial-intelligence/' => 'factory',
    '/industries/distribution/' => 'box', '/industries/manufacturing/' => 'factory', '/industries/retail/' => 'cart',
    '/industries/contracting/' => 'briefcase', '/built-for-saudi-arabia/' => 'map',
    '/cras/' => 'coins', '/pms/' => 'chart', '/industrial-iot-platform/' => 'cloud', '/hrms/' => 'users',
    '/about/' => 'users', '/partners/' => 'link', '/our-clients/' => 'check', '/insights/' => 'doc', '/careers/' => 'briefcase', '/contact/' => 'mail',
];
$without = fn(array $items, array $hrefs) => array_values(array_filter($items, fn($i) => !in_array($i[1], $hrefs, true)));
$only = fn(array $items, array $hrefs) => array_values(array_filter($items, fn($i) => in_array($i[1], $hrefs, true)));
$sol = t('nav.solutions_items');
$products = t('nav.products_items');
if (!cfg('hrms_enabled')) $products = $without($products, ['/hrms/']);
$menus = [
    // Products: ERP first — SAP Business One as the flagship with its pages, then Odoo; DWP's own platforms after
    'products' => ['label' => t('nav.products'), 'feature' => null, 'groups' => [
        ['title' => $mega['group_erp'], 'href' => '/erp/', 'wide' => true,
         'primary' => ['title' => t('nav.sap'), 'href' => '/sap-business-one/', 'tag' => t('trust.sap'), 'text' => $mega['sap_card_text'], 'cta' => $mega['sap_cta']],
         'items' => $only($sol, ['/erp/odoo/']), 'desc' => true],
        ['title' => $mega['group_platforms'], 'items' => $products, 'desc' => true],
    ]],
    'solutions' => ['label' => t('nav.solutions'), 'feature' => null, 'groups' => [
        ['title' => $mega['group_ai'], 'href' => '/ai/', 'items' => $without(t('nav.ai_items'), ['/ai/'])],
        ['title' => $mega['group_more'], 'items' => $without($sol, ['/erp/', '/erp/odoo/']), 'desc' => true],
    ]],
    'industries' => ['label' => t('nav.industries'), 'feature' => 'industries', 'items' => t('nav.industries_items')],
    // Company menu: only About, Success Stories and Partners (Insights, Careers and Contact stay in the footer)
    'company' => ['label' => t('nav.company'), 'feature' => 'company', 'items' => array_values(array_filter(array_map(fn($h) => current(array_filter(t('nav.company_items'), fn($i) => $i[1] === $h)), ['/about/', '/our-clients/', '/partners/'])))],
];
$phone = cfg('phone');
$tel = cfg('phone_e164');
$link = function (array $it, bool $desc) use ($icons, $mega, $current) {
    [$label, $href] = $it;
    $here = url($href) === $current;
    return '<li><a href="' . e(url($href)) . '"' . ($here ? ' aria-current="page"' : '') . '><span class="mega-icon">' . icon($icons[$href] ?? 'arrow') . '</span>'
        . '<span class="mega-text"><strong>' . e($label) . '</strong>' . ($desc && !empty($mega['desc'][$href]) ? '<small>' . e($mega['desc'][$href]) . '</small>' : '') . '</span></a></li>';
};
?>
<header class="site-header" data-header>
  <div class="container header-inner">
    <a class="logo" href="<?= e(url('/')) ?>" aria-label="DWP Arabia, <?= e(t('home')) ?>">
      <?php if (asset_exists('img/logo-light.png')): // white logo on the dark header ?>
        <img src="<?= e(asset('img/logo-light.png')) ?>" alt="DWP Arabia" width="105" height="40">
        <?php if (asset_exists('img/logo-dark.png')): ?><img class="logo-on-light" src="<?= e(asset('img/logo-dark.png')) ?>" alt="DWP Arabia" width="105" height="40"><?php endif; ?>
      <?php else: ?>
        <span class="logo-text">DWP <strong>Arabia</strong></span>
      <?php endif; ?>
    </a>

    <nav class="site-nav" id="site-nav" aria-label="<?= e(t('menu')) ?>" data-nav>
      <ul class="menu">
        <?php foreach ($menus as $m => $menu):
            $all = $menu['items'] ?? array_merge(...array_map(fn($g) => array_merge($g['items'], $g['secondary'] ?? [], $g['primary']['items'] ?? [],
                isset($g['href']) ? [['', $g['href']]] : [], isset($g['primary']) ? [['', $g['primary']['href']]] : []), $menu['groups']));
            $active = (bool) array_filter($all, fn($i) => url($i[1]) === $current);
            $f = $menu['feature'] ? ($mega['feature'][$menu['feature']] ?? null) : null;
            $id = "mega-$m";
        ?>
        <li class="menu-item has-sub<?= $active ? ' is-active' : '' ?>" data-menu-item>
          <button type="button" class="menu-link" aria-expanded="false" aria-controls="<?= $id ?>" data-sub-toggle><?= e($menu['label']) ?><?= icon('chevron') ?></button>
          <div class="mega mega-v2" id="<?= $id ?>">
            <div class="container mega-inner">
              <?php $intro = $mega['intro'][$m] ?? null; $va = $mega['view_all'][$m] ?? null; ?>
              <div class="mega-intro">
                <?php if ($intro): ?>
                <p class="mega-intro-eyebrow"><?= e($menu['label']) ?></p>
                <p class="mega-intro-title"><?= e($intro[0]) ?></p>
                <p class="mega-intro-text"><?= rich($intro[1]) ?></p>
                <?php endif; ?>
                <?php if ($va): ?><a class="mega-view-all" href="<?= e(url($va[1])) ?>"><?= e($va[0]) ?> <?= icon('arrow') ?></a><?php endif; ?>
              </div>
              <div class="mega-body">
                <?php foreach ($menu['groups'] ?? [['title' => null, 'items' => $menu['items']]] as $g):
                    $items = $g['items'];
                    if (!$items && empty($g['primary'])) continue;
                ?>
                <div class="mega-col<?= count($items) > 4 ? ' is-2col' : '' ?>">
                  <?php if (!empty($g['title'])): ?>
                  <p class="mega-col-title"><?php if (!empty($g['href'])): ?><a href="<?= e(url($g['href'])) ?>"><?= e($g['title']) ?></a><?php else: ?><?= e($g['title']) ?><?php endif; ?></p>
                  <?php endif; ?>
                  <?php if (!empty($g['primary'])): $pr = $g['primary']; ?>
                  <a class="mega-sap" href="<?= e(url($pr['href'])) ?>"<?= url($pr['href']) === $current ? ' aria-current="page"' : '' ?>>
                    <span class="mega-sap-logo"><?= sap_b1_logo(36, '', 'dark') ?: '<strong>' . e($pr['title']) . '</strong>' ?></span>
                    <span class="mega-sap-text"><?= e($pr['text']) ?></span>
                    <span class="mega-sap-cta"><?= e($pr['cta']) ?> <?= icon('arrow') ?></span>
                  </a>
                  <?php endif; ?>
                  <ul class="mega-cards">
                    <?php foreach ($items as $it): [$label, $href] = $it; $here = url($href) === $current; ?>
                    <li><a class="mega-card" href="<?= e(url($href)) ?>"<?= $here ? ' aria-current="page"' : '' ?>>
                      <span class="mega-card-icon"><?= icon($icons[$href] ?? 'arrow') ?></span>
                      <span class="mega-card-text">
                        <strong><?= e($label) ?><?php if (!empty($it[2])): ?> <span class="mega-card-tag"><?= e($it[2]) ?></span><?php endif; ?></strong>
                        <?php if (!empty($mega['desc'][$href])): ?><small><?= e($mega['desc'][$href]) ?></small><?php endif; ?>
                      </span>
                    </a></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
                <?php endforeach; ?>
              </div>
              <?php if ($f): ?>
              <a class="mega-feature" href="<?= e(url($f['href'])) ?>">
                <?php if (asset_exists($f['img'])): $sm = small_variant($f['img']); ?>
                <img src="<?= e(asset($sm && asset_exists($sm) ? $sm : $f['img'])) ?>" alt="" width="800" height="533" loading="lazy" decoding="async">
                <?php endif; ?>
                <span class="mega-feature-body">
                  <span class="mega-eyebrow"><?= e($f['eyebrow']) ?></span>
                  <strong><?= e($f['title']) ?></strong>
                  <span class="mega-cta"><?= e($f['cta']) ?> <?= icon('arrow') ?></span>
                </span>
              </a>
              <?php endif; ?>
            </div>
            <div class="mega-foot">
              <div class="container mega-foot-inner">
                <?php if ($m === 'products'): ?>
                <span class="mega-partner"><?= icon('shield') ?><?= e($mega['partner_line']) ?></span>
                <?php else: ?>
                <span><?= e($mega['help']) ?> <a href="<?= e(url('/contact/')) ?>"><?= e($mega['help_link']) ?></a></span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
      <div class="nav-actions">
        <?php if ($phone): ?>
        <a class="header-phone" href="tel:<?= e($tel) ?>" data-track="phone_click" aria-label="<?= e($mega['call']) ?>: <?= e($phone) ?>"><?= icon('phone') ?><span dir="ltr"><?= e($phone) ?></span></a>
        <?php endif; ?>
        <?php if ($hasOther): ?>
        <a class="lang-switch" href="<?= e(url($switchPath, $other)) ?>" hreflang="<?= $other ?>" lang="<?= $other ?>" aria-label="<?= e(t('lang_switch_label')) ?>"><?= e(t('lang_switch')) ?></a>
        <?php endif; ?>
        <a class="btn btn-primary btn-sm" href="<?= e(url('/book-a-demo/')) ?>"><?= e(t('book_demo')) ?></a>
      </div>
    </nav>

    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" data-nav-toggle>
      <span class="nav-toggle-bars" aria-hidden="true"><i></i><i></i><i></i></span><span class="sr-only"><?= e(t('menu')) ?></span>
    </button>
  </div>
  <div class="mega-backdrop" data-mega-backdrop aria-hidden="true"></div>
</header>
