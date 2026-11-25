<?php

/* ============================================================
   1) DATA
   ============================================================ */
if (!isset($menuCategories)) {
    $menuCategories = [];
    $subToCat       = [];

    $res = mysqli_query($con, "SELECT id, category_name, slug
                               FROM elso_category
                               WHERE status = 1 AND is_deleted = 0
                               ORDER BY category_name ASC");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $row['subs'] = [];
            $menuCategories[$row['id']] = $row;
        }
    }

    $res = mysqli_query($con, "SELECT id, category_id, subcategory_name, slug
                               FROM elso_subcategory
                               WHERE status = 1 AND is_deleted = 0
                               ORDER BY subcategory_name ASC");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            if (isset($menuCategories[$row['category_id']])) {
                $row['children'] = [];
                $menuCategories[$row['category_id']]['subs'][$row['id']] = $row;
                $subToCat[$row['id']] = $row['category_id'];
            }
        }
    }

    $res = mysqli_query($con, "SELECT id, subcategory_id, child_subcategory_name, slug
                               FROM elso_child_subcategory
                               WHERE status = 1 AND is_deleted = 0
                               ORDER BY sort_order ASC, child_subcategory_name ASC");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            if (isset($subToCat[$row['subcategory_id']])) {
                $menuCategories[$subToCat[$row['subcategory_id']]]['subs'][$row['subcategory_id']]['children'][] = $row;
            }
        }
    }
}

/* ============================================================
   2) HELPERS
   ============================================================ */
if (!function_exists('cm_h')) {
    function cm_h($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
    function cm_cat_url($cat)        { return 'category/' . urlencode($cat['slug']); }
    function cm_sub_url($cat, $sub)  { return cm_cat_url($cat) . '/' . urlencode($sub['slug']); }
    function cm_child_url($cat, $sub, $ch) { return cm_sub_url($cat, $sub) . '/' . urlencode($ch['slug']); }
}

/* ============================================================
   3) DESKTOP MEGA MENU  (top: $sfx = ''   |  sticky: $sfx = '-cs')
   ============================================================ */
if (!function_exists('render_category_mega')) {
    function render_category_mega($cats, $sfx = '')
    {
        $maxChildren = 6; // in one subcategory card how many child show
        ?>
        <li class="with-rbt-megamenu has-menu-child-item position-static">
          <a href="category">Category <i class="fa-regular fa-chevron-down"></i></a>

          <div class="rbt-megamenu rbt-megamenu-4">
            <div class="rbt-megamenu-wrapper p--0">
              <div class="row row--0">

                <!-- LEFT: Categories -->
                <div class="col-3">
                  <div class="rbt-menu-tab-wrapper">
                    <nav id="rbt-megamenuTab<?= $sfx ?>"
                      class="nav nav-pills flex-column rbt-megamenu-tab rbt-megamenu-tab-cs-activation rbt-dynamic-cat-tabs">
                      <?php $first = true; foreach ($cats as $cat) { ?>
                        <a href="#rbt-cat-pane-<?= (int) $cat['id'] . $sfx ?>" data-bs-toggle="pill"
                          data-cat-url="<?= cm_h(cm_cat_url($cat)) ?>"
                          class="nav-link <?= $first ? 'active' : '' ?>"  style="font-size: 16px!important;">
                          <span><i class="fa-regular fa-tag"></i></span>
                          <?= cm_h($cat['category_name']) ?>
                          <?php if (!empty($cat['subs'])) { ?>
                            <span class="rbt-chevron-right"><i class="fa-regular fa-chevron-right"></i></span>
                          <?php } ?>
                        </a>
                      <?php $first = false; } ?>
                    </nav>
                  </div>
                </div>

                <!-- RIGHT: Subcategories + Child -->
                <div class="col-9">
                  <div class="rbt-dyn-right">
                    <div class="tab-content rbt-dyn-tabcontent">
                      <?php $first = true; foreach ($cats as $cat) {
                        $subCount = count($cat['subs']); ?>
                        <div class="tab-pane fade <?= $first ? 'show active' : '' ?>"
                          id="rbt-cat-pane-<?= (int) $cat['id'] . $sfx ?>">

                          <div class="rbt-dyn-head">
                            <div>
                              <h4 class="rbt-dyn-title"><a href="<?= cm_h(cm_cat_url($cat)) ?>"><?= cm_h($cat['category_name']) ?></a></h4>
                              <?php if ($subCount) { ?>
                                <span class="rbt-dyn-count"><?= $subCount ?> sub-categor<?= $subCount == 1 ? 'y' : 'ies' ?></span>
                              <?php } ?>
                            </div>
                            <a class="rbt-dyn-all" href="<?= cm_h(cm_cat_url($cat)) ?>">
                              View all <i class="fa-regular fa-arrow-right"></i>
                            </a>
                          </div>

                          <?php if (!$subCount) { ?>
                            <div class="rbt-dyn-empty">
                              <p>Is category ke products dekhne ke liye neeche click karein.</p>
                              <a class="rbt-btn rbt-btn-sm" href="<?= cm_h(cm_cat_url($cat)) ?>">View all products</a>
                            </div>
                          <?php } else { ?>
                            <div class="rbt-dyn-grid">
                              <?php foreach ($cat['subs'] as $sub) {
                                $children = $sub['children'];
                                $shown    = array_slice($children, 0, $maxChildren);
                                $extra    = count($children) - count($shown); ?>
                                <div class="rbt-dyn-card">
                                  <a class="rbt-dyn-card-title" href="<?= cm_h(cm_sub_url($cat, $sub)) ?>">
                                    <?= cm_h($sub['subcategory_name']) ?>
                                  </a>
                                  <?php if (empty($shown)) { ?>
                                    <a class="rbt-dyn-browse" href="<?= cm_h(cm_sub_url($cat, $sub)) ?>">Browse products <i class="fa-regular fa-arrow-right"></i></a>
                                  <?php } ?>
                                  <?php if (!empty($shown)) { ?>
                                    <ul class="rbt-dyn-children">
                                      <?php foreach ($shown as $ch) { ?>
                                        <li><a href="<?= cm_h(cm_child_url($cat, $sub, $ch)) ?>"><?= cm_h($ch['child_subcategory_name']) ?></a></li>
                                      <?php } ?>
                                      <?php if ($extra > 0) { ?>
                                        <li class="more"><a href="<?= cm_h(cm_sub_url($cat, $sub)) ?>">+ <?= $extra ?> more</a></li>
                                      <?php } ?>
                                    </ul>
                                  <?php } ?>
                                </div>
                              <?php } ?>
                            </div>
                          <?php } ?>

                        </div>
                      <?php $first = false; } ?>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </li>
        <?php
    }
}

/* ============================================================
   4) MOBILE MENU  ("Categories" under this tab)
   ============================================================ */
if (!function_exists('render_mobile_categories')) {
    function render_mobile_categories($cats)
    { ?>
        <div class="tab-pane fade" id="rbt-tab-pane-mobilemenu-2" role="tabpanel"
          aria-labelledby="rbt-tab-mobilemenu-2" tabindex="0">
          <nav class="rbt-mainmenu-nav">
            <ul class="mainmenu mm-cat-list">
              <?php foreach ($cats as $cat) {
                $catId   = 'mmCat' . (int) $cat['id'];
                $hasSubs = !empty($cat['subs']); ?>
                <li class="mm-cat-item">
                  <div class="mm-row">
                    <a href="<?= cm_h(cm_cat_url($cat)) ?>">
                      <span class="mm-ico"><i class="rbt-catagories-icon fa-regular fa-tag"></i></span>
                      <?= cm_h($cat['category_name']) ?>
                    </a>
                    <?php if ($hasSubs) { ?>
                      <button type="button" class="mm-toggle collapsed" data-bs-toggle="collapse"
                        data-bs-target="#<?= $catId ?>" aria-expanded="false" aria-label="Toggle">
                        <i class="fa-regular fa-chevron-down"></i>
                      </button>
                    <?php } ?>
                  </div>

                  <?php if ($hasSubs) { ?>
                    <ul class="mm-sub collapse" id="<?= $catId ?>">
                      <?php foreach ($cat['subs'] as $sub) {
                        $subId       = 'mmSub' . (int) $sub['id'];
                        $hasChildren = !empty($sub['children']); ?>
                        <li>
                          <div class="mm-row">
                            <a href="<?= cm_h(cm_sub_url($cat, $sub)) ?>"><?= cm_h($sub['subcategory_name']) ?></a>
                            <?php if ($hasChildren) { ?>
                              <button type="button" class="mm-toggle mm-toggle-sm collapsed" data-bs-toggle="collapse"
                                data-bs-target="#<?= $subId ?>" aria-expanded="false" aria-label="Toggle">
                                <i class="fa-regular fa-chevron-down"></i>
                              </button>
                            <?php } ?>
                          </div>
                          <?php if ($hasChildren) { ?>
                            <ul class="mm-child collapse" id="<?= $subId ?>">
                              <?php foreach ($sub['children'] as $ch) { ?>
                                <li><a href="<?= cm_h(cm_child_url($cat, $sub, $ch)) ?>"><?= cm_h($ch['child_subcategory_name']) ?></a></li>
                              <?php } ?>
                            </ul>
                          <?php } ?>
                        </li>
                      <?php } ?>
                    </ul>
                  <?php } ?>
                </li>
              <?php } ?>
            </ul>
          </nav>
        </div>
    <?php }
}

/* ============================================================
   5) BURGER SIDE MENU  (from hamburger icon open)
   ============================================================ */
if (!function_exists('render_sidenav_tabs')) {
    function render_sidenav_tabs($cats)
    { ?>
        <ul class="rbt-sidebar-sub-categories nav flex-column nav-pills" id="v-pills-tab" role="tablist"
          aria-orientation="vertical">
          <?php $first = true; foreach ($cats as $cat) {
            $n = count($cat['subs']); ?>
            <li>
              <button class="rbt-nav-link nav-link <?= $first ? 'active' : '' ?>"
                id="rbt-tab-cat-sidebar-<?= (int) $cat['id'] ?>" data-bs-toggle="pill"
                data-bs-target="#rbt-nav-pill-<?= (int) $cat['id'] ?>" type="button" role="tab"
                aria-controls="rbt-nav-pill-<?= (int) $cat['id'] ?>" aria-selected="<?= $first ? 'true' : 'false' ?>">
                <span class="rbt-round-btn"><i class="fa-regular fa-tag"></i></span>
                <span class="rbt-content">
                  <span class="rbt-sub-category-title"><span><?= cm_h($cat['category_name']) ?></span></span>
                  <span class="description"><?= $n ? $n . ' sub-categor' . ($n == 1 ? 'y' : 'ies') : 'View products' ?></span>
                </span>
                <span class="icon"><i class="fa-regular fa-chevron-right"></i></span>
              </button>
            </li>
          <?php $first = false; } ?>
        </ul>
    <?php }

    function render_sidenav_panes($cats)
    { ?>
        <div class="tab-content" id="v-pills-tabContent">
          <?php $first = true; foreach ($cats as $cat) { ?>
            <div class="rbt-tab-content tab-pane fade <?= $first ? 'show active' : '' ?>"
              id="rbt-nav-pill-<?= (int) $cat['id'] ?>" role="tabpanel"
              aria-labelledby="rbt-tab-cat-sidebar-<?= (int) $cat['id'] ?>" tabindex="0">
              <div class="rbt-sub-category-products">
                <div class="rbt-category-products-inner">
                  <?php if (empty($cat['subs'])) { ?>
                    <div class="rbt-sub-category-product">
                      <h2 class="rbt-category-offcanvas-header h5" style="font-size: 34px !Important;">
                        <a href="<?= cm_h(cm_cat_url($cat)) ?>" style="font-size: 34px !Important;"><?= cm_h($cat['category_name']) ?></a>
                      </h2>
                      <ul class="rbt-product-features has-link-underline-effect">
                        <li><a href="<?= cm_h(cm_cat_url($cat)) ?>">View all products</a></li>
                      </ul>
                    </div>
                  <?php } else { foreach ($cat['subs'] as $sub) { ?>
                    <div class="rbt-sub-category-product">
                      <h2 class="rbt-category-offcanvas-header h5">
                        <a href="<?= cm_h(cm_sub_url($cat, $sub)) ?>"><?= cm_h($sub['subcategory_name']) ?></a>
                      </h2>
                      <?php if (!empty($sub['children'])) { ?>
                        <ul class="rbt-product-features has-link-underline-effect">
                          <?php foreach ($sub['children'] as $ch) { ?>
                            <li><a href="<?= cm_h(cm_child_url($cat, $sub, $ch)) ?>"><?= cm_h($ch['child_subcategory_name']) ?></a></li>
                          <?php } ?>
                        </ul>
                      <?php } ?>
                    </div>
                  <?php } } ?>
                </div>
              </div>
            </div>
          <?php $first = false; } ?>
        </div>
    <?php }
}
?>
<!-- =====================  STYLES  ===================== -->
<style>
  /* ---------- Desktop: LEFT categories ---------- */
  .rbt-dynamic-cat-tabs {
    display: flex !important; flex-direction: column !important; flex-wrap: nowrap !important;
    max-height: 62vh; overflow-y: auto; overflow-x: hidden;
  }
  .rbt-dynamic-cat-tabs .nav-link { width: 100%; flex: 0 0 auto; }
  .rbt-dynamic-cat-tabs::-webkit-scrollbar { width: 5px; }
  .rbt-dynamic-cat-tabs::-webkit-scrollbar-thumb { background: #cfd4dc; border-radius: 4px; }

  /* ---------- Desktop: RIGHT panel ---------- */
  .rbt-dyn-right { padding: 24px 28px 28px; min-height: 320px; background: #fff; }
  .rbt-dyn-tabcontent { max-height: 62vh; overflow-y: auto; overflow-x: hidden; padding-right: 6px; }
  .rbt-dyn-tabcontent::-webkit-scrollbar { width: 5px; }
  .rbt-dyn-tabcontent::-webkit-scrollbar-thumb { background: #d7dbe8; border-radius: 4px; }
  .rbt-dyn-tabcontent .tab-pane.active {
    display: block !important; opacity: 1 !important; visibility: visible !important;
    position: static !important; transform: none !important;
    animation: rbtDynFade .25s ease;
  }
  @keyframes rbtDynFade { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }

  .rbt-dyn-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px;
    padding-bottom: 14px; margin-bottom: 20px; border-bottom: 2px solid #eef0f6; }
  .rbt-dyn-title { margin: 0; font-size: 22px; font-weight: 700; line-height: 1.2; }
  .rbt-dyn-title a { color: #111; text-decoration: none; }
  .rbt-dyn-title a:hover { color: var(--color-primary, #2f57ef); }
  .rbt-dyn-count { display: inline-block; margin-top: 4px; font-size: 13px; color: #7b8194; }
  .rbt-dyn-all { white-space: nowrap; font-weight: 600; font-size: 14px; color: var(--color-primary, #2f57ef);
    background: #eef2ff; padding: 8px 16px; border-radius: 50px; text-decoration: none; transition: all .25s; }
  .rbt-dyn-all i { margin-left: 4px; transition: transform .25s; }
  .rbt-dyn-all:hover { background: var(--color-primary, #2f57ef); color: #fff; }
  .rbt-dyn-all:hover i { transform: translateX(3px); }

  /* subcategory cards (3 columns, heights auto) */
  .rbt-dyn-grid { column-count: 3; column-gap: 18px; }
  .rbt-dyn-card { break-inside: avoid; margin-bottom: 18px; padding: 16px 18px; background: #fafbff;
    border: 1px solid #edf0f8; border-radius: 14px; transition: transform .25s, box-shadow .25s, border-color .25s; }
  .rbt-dyn-card:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(20, 30, 80, .08); border-color: #dbe3ff; }
  .rbt-dyn-card-title { display: block; font-size: 15px; font-weight: 700; color: #1f1f25; text-decoration: none; line-height: 1.35; }
  .rbt-dyn-card-title:hover { color: var(--color-primary, #2f57ef); }
  .rbt-dyn-children { list-style: none; margin: 10px 0 0; padding: 10px 0 0; border-top: 1px dashed #dfe3f0; }
  .rbt-dyn-children li { margin: 0; }
  .rbt-dyn-children li a { position: relative; display: block; padding: 5px 0 5px 14px; font-size: 13.5px; color: #4b5563; text-decoration: none; transition: color .2s, padding .2s; }
  .rbt-dyn-children li a::before { content: ""; position: absolute; left: 0; top: 50%; width: 5px; height: 5px; border-radius: 50%; background: #c3c9de; transform: translateY(-50%); transition: background .2s; }
  .rbt-dyn-children li a:hover { color: var(--color-primary, #2f57ef); padding-left: 18px; }
  .rbt-dyn-children li a:hover::before { background: var(--color-primary, #2f57ef); }
  .rbt-dyn-children li.more a { color: var(--color-primary, #2f57ef); font-weight: 600; }
  .rbt-dyn-children li.more a::before { display: none; }
  .rbt-dyn-children li.more a { padding-left: 0; }

  .rbt-dyn-empty { padding: 40px 0; text-align: center; color: #6b7280; }
  .rbt-dyn-empty p { margin-bottom: 16px; }

  @media (max-width: 1399px) { .rbt-dyn-grid { column-count: 2; } }

  /* ---------- Mobile menu ---------- */
  .mm-cat-list, .mm-cat-list ul { list-style: none; margin: 0; padding: 0; }
  .mm-cat-list .mm-row { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #eceef5; }
  .mm-cat-list .mm-row > a { flex: 1; display: flex; align-items: center; padding: 13px 4px; font-weight: 600; color: #1f1f25; text-decoration: none; }
  .mm-cat-list .mm-row > a:hover { color: var(--color-primary, #2f57ef); }
  .mm-cat-list .mm-ico { width: 30px; height: 30px; margin-right: 10px; display: inline-flex; align-items: center; justify-content: center;
    background: #eef2ff; color: var(--color-primary, #2f57ef); border-radius: 8px; font-size: 13px; }
  .mm-cat-list .mm-toggle { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;
    border: 1px solid #dfe3f0; border-radius: 8px; background: #fff; color: #1f1f25; font-size: 12px; }
  .mm-cat-list .mm-toggle i { transition: transform .25s; }
  .mm-cat-list .mm-toggle:not(.collapsed) { background: var(--color-primary, #2f57ef); border-color: var(--color-primary, #2f57ef); color: #fff; }
  .mm-cat-list .mm-toggle:not(.collapsed) i { transform: rotate(180deg); }
  .mm-cat-list .mm-toggle-sm { width: 26px; height: 26px; font-size: 10px; }
  .mm-cat-list .mm-sub { background: #f7f8fc; border-radius: 10px; margin: 4px 0 8px; padding-left: 14px; }
  .mm-cat-list .mm-sub .mm-row > a { font-weight: 500; font-size: 14px; }
  .mm-cat-list .mm-child { padding-left: 14px; border-left: 2px solid #dfe3f0; margin: 2px 0 6px 6px; }
  .mm-cat-list .mm-child li a { display: block; padding: 9px 4px; font-size: 13px; color: #4b5563; text-decoration: none; }
  .mm-cat-list .mm-child li a:hover { color: var(--color-primary, #2f57ef); }

  /* ---------- Burger side menu ---------- */
  #v-pills-tabContent .rbt-sub-category-product { min-width: 0; }
  #v-pills-tabContent .rbt-category-offcanvas-header a { font-weight: 700; }
</style>

<!-- =====================  CLEAN MEGA MENU UI (overrides)  ===================== -->
<style>
  /* ---------- LEFT rail ---------- */
  .rbt-dynamic-cat-tabs { padding: 14px 10px 14px 14px !important; gap: 2px; scrollbar-width: thin; }
  .rbt-dynamic-cat-tabs .nav-link {
    display: flex !important; align-items: center; gap: 10px;
    padding: 11px 14px !important; border-radius: 10px !important;
    font-size: 14.5px; font-weight: 600; color: #2b2f3a; line-height: 1.3;
    transition: background .2s, color .2s, box-shadow .2s;
  }
  .rbt-dynamic-cat-tabs .nav-link > span:first-child { color: #8a91a5; font-size: 14px; display: inline-flex; width: 18px; justify-content: center; }
  .rbt-dynamic-cat-tabs .nav-link .rbt-chevron-right { margin-left: auto; font-size: 11px; opacity: .55; }
  .rbt-dynamic-cat-tabs .nav-link:hover { background: rgba(255, 255, 255, .7) !important; color: var(--color-primary, #2f57ef); }
  .rbt-dynamic-cat-tabs .nav-link.active {
    background: #fff !important; color: var(--color-primary, #2f57ef) !important;
    box-shadow: 0 6px 18px rgba(20, 30, 80, .08);
  }
  .rbt-dynamic-cat-tabs .nav-link.active > span:first-child,
  .rbt-dynamic-cat-tabs .nav-link.active .rbt-chevron-right { color: var(--color-primary, #2f57ef); opacity: 1; }

  /* ---------- RIGHT panel ---------- */
  .rbt-dyn-right { padding: 26px 34px 30px !important; }
  .rbt-dyn-tabcontent { scrollbar-width: thin; padding-right: 8px; }

  .rbt-dyn-head {
    display: flex !important; align-items: center !important; justify-content: space-between;
    gap: 16px; margin: 0 0 24px !important; padding: 0 0 16px !important; border-bottom: 1px solid #eceef5 !important;
  }
  .rbt-dyn-head > div { display: flex; align-items: baseline; gap: 12px; flex-wrap: wrap; }
  /* .rbt-dyn-head .rbt-dyn-title { margin: 0 !important; font-size: 21px !important; line-height: 1.25 !important; } */
  .rbt-dyn-head .rbt-dyn-title,
.rbt-dyn-head .rbt-dyn-title a { margin: 0 !important; font-size: 20px !important; line-height: 1.25 !important; }
  .rbt-dyn-head .rbt-dyn-count { margin: 0 !important; font-size: 13px; color: #8a91a5; }
  .rbt-dyn-head .rbt-dyn-all {
    display: inline-flex !important; align-items: center; gap: 6px;
    width: auto !important; height: auto !important; min-height: 0 !important; flex: 0 0 auto; align-self: center;
    padding: 8px 16px !important; line-height: 1 !important; border-radius: 50px !important;
    font-size: 13px !important; font-weight: 600;
  }
  .rbt-dyn-head .rbt-dyn-all i { margin: 0 !important; font-size: 12px; }

  /* sub-category columns (no boxes - clean list style) */
  .rbt-dyn-grid { display: grid !important; column-count: auto !important; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 28px 36px; align-items: start; }
  .rbt-dyn-card {
    padding: 0 !important; margin: 0 !important; background: none !important; border: 0 !important;
    box-shadow: none !important; transform: none !important; border-radius: 0 !important; break-inside: auto;
  }
  .rbt-dyn-card-title {
    display: block; margin: 0 0 10px !important; padding: 0 0 9px !important;
    font-size: 16px !important; font-weight: 700 !important; color: #1b1e27 !important; line-height: 1.35;
    border-bottom: 2px solid #eceef5; transition: color .2s, border-color .2s;
  }
  .rbt-dyn-card-title:hover { color: var(--color-primary, #2f57ef) !important; border-color: var(--color-primary, #2f57ef); }

  .rbt-dyn-children { margin: 0 !important; padding: 0 !important; border: 0 !important; list-style: none; }
  .rbt-dyn-children li { margin: 0 !important; padding: 0 !important; }
  .rbt-dyn-children li a {
    display: block; padding: 5px 0 !important; font-size: 14px; line-height: 1.45; color: #5a6275 !important;
    text-decoration: none !important; transition: color .2s, transform .2s;
  }
  .rbt-dyn-children li a::before { display: none !important; }
  .rbt-dyn-children li a:hover { color: var(--color-primary, #2f57ef) !important; transform: translateX(4px); padding-left: 0 !important; }
  .rbt-dyn-children li.more a { color: var(--color-primary, #2f57ef) !important; font-weight: 600; }

  .rbt-dyn-browse { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #8a91a5; text-decoration: none; transition: color .2s; }
  .rbt-dyn-browse i { font-size: 11px; transition: transform .2s; }
  .rbt-dyn-browse:hover { color: var(--color-primary, #2f57ef); }
  .rbt-dyn-browse:hover i { transform: translateX(3px); }

  @media (max-width: 1399px) { .rbt-dyn-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
</style>

<!-- =====================  MOBILE ICON FIX  ===================== -->
<style>
  .mm-cat-list .mm-row > a { align-items: center !important; padding: 12px 4px !important; line-height: 1.3 !important; }
  .mm-cat-list .mm-ico {
    flex: 0 0 32px; width: 32px !important; height: 32px !important; margin: 0 12px 0 0 !important; padding: 0 !important;
    display: inline-flex !important; align-items: center; justify-content: center;
    background: #eef2ff !important; border-radius: 9px !important;
  }
  .mm-cat-list .mm-ico i {
    display: inline-block !important; margin: 0 !important; padding: 0 !important;
    font-size: 14px !important; line-height: 1 !important; opacity: 1 !important; visibility: visible !important;
    color: var(--color-primary, #2f57ef) !important;
  }
  .mm-cat-list .mm-toggle i { display: inline-block !important; opacity: 1 !important; visibility: visible !important; }
</style>

<!-- =====================  SCRIPT  ===================== -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.rbt-dynamic-cat-tabs .nav-link').forEach(function (link) {
      var timer;
      // on hover  subcategory 
      link.addEventListener('mouseenter', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
          if (window.bootstrap) { bootstrap.Tab.getOrCreateInstance(link).show(); }
        }, 60);
      });
      link.addEventListener('mouseleave', function () { clearTimeout(timer); });
      //on  click  category page
      link.addEventListener('click', function (e) {
        var url = link.getAttribute('data-cat-url');
        if (url) { e.preventDefault(); window.location.href = url; }
      });
    });
  });
</script>